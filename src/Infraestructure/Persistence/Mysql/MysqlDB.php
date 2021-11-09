<?php

namespace App\Infraestructure\Persistence\Mysql;

use PDO;

class MysqlDB
{
    private static $instance;
    private static $config;
    /**
     * @var PDO
     */
    private $con;

    /**
     * Fill here your database connection data.
     */
    protected function __construct()
    {
        $this->con = new ConnectPDO('mysql:dbname=' . self::$config['MYSQL_BASE'] . ';host=' . self::$config['MYSQL_SERVER'], self::$config['MYSQL_USER'], self::$config['MYSQL_PASS'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . self::$config['MYSQL_CHARSET']));
    }

    public static function config(array $config)
    {
        if (!isset($config['MYSQL_SERVER']) && !isset($config['MYSQL_BASE']) && !isset($config['MYSQL_USER']) && !isset($config['MYSQL_PASS'])) {
            throw new \Exception('Please provide host, name, username and password for the database');
        }
        self::$config = $config;
    }


    public static function getConfig()
    {
        return self::$config;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            if (!self::$config) {
                throw new \Exception('LightAdapter was not initialized with database configuration. Use LightAdapter::config()');
            }
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return array
     *
     */

    public function getConn(){
        return $this->con;
    }

    public function getRows($query, $values = array())
    {
        /*Developer::log('SQL STATEMENT: ' . $query, Developer::LOG_ANNOY);
        if (!empty($values)) {
            Developer::log('SQL VALUES: | ' . implode(' | ', $values) . ' |', Developer::LOG_ANNOY);
        }*/

        $dbh = $this->con->prepare($query);
        $ok = $dbh->execute($values);
        if (!$ok) {
            throw new \Exception(var_export($dbh->errorInfo(), 1) . "\nQUERY: $query");
        }
        $result = $dbh->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * @param       $query
     * @param array $values
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getRow($query, $values = array())
    {
        $result = $this->getRows($query, $values);

        return array_pop($result);
    }

    public function getTableColumns($table)
    {
        $result = $this->con->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_ASSOC);
        $columns = array();
        foreach ($result as $row) {
            $columns[] = $row['Field'];
        }

        return $columns;
    }

    public function write($query, $values = array())
    {
        /*Developer::log('SQL STATEMENT: ' . $query, Developer::LOG_ANNOY);
        if (!empty($values)) {
            Developer::log('SQL VALUES: | ' . implode(' | ', $values) . ' |', Developer::LOG_ANNOY);
        }*/

        $dbh = $this->con->prepare($query);
        $ok = $dbh->execute($values);
        if (!$ok) {
            throw new \Exception(var_export($dbh->errorInfo(), 1));
        }
        if (strtoupper(substr($query, 0, 6)) == 'INSERT') {
            $result = $this->con->lastInsertId();
        } else {
            $result = $dbh->rowCount();
        }

        return $result;
    }

    public function update($table, $where, array $columns, array $values)
    {
        $cols = array();
        foreach ($columns as $name) {
            $cols[] = "$name = ?";
        }
        $q = 'UPDATE ' . $table . ' SET ' . implode(',', $cols) . ' WHERE ' . $where;
        return $this->write($q, $values);
    }

    public function batchInsert($table, array $columns, array $rows)
    {
        $this->con->prepare('START TRANSACTION')->execute();

        $columns = $this->prepareColumns($columns);

        $template_query = "INSERT IGNORE INTO $table SET $columns";
        //Developer::log('SQL STATEMENT: ' . $template_query, Developer::LOG_ANNOY);
        $statement = $this->con->prepare($template_query);

        $inserted_count = 0;
        foreach ($rows as $row) {
            //Developer::log('SQL VALUES: | ' . implode(' | ', $row) . ' |', Developer::LOG_ANNOY);

            if ($statement->execute($row)) {
                ++$inserted_count;
            } else {
                throw new \Exception(var_export($statement->errorInfo(), 1));
            }
        }

        $this->con->prepare('COMMIT')->execute();

        return $inserted_count;
    }

    /**
     * @param $table
     * @param array $columns
     * @param array $rows
     * @param null $callbackWhenAffectedRow
     * @return int
     * @throws \Exception
     */
    public function batchInsertUpdate($table, array $columns, array $rows, $callbackWhenAffectedRow = null)
    {
        static $attempts = 0;
        try {
            return $this->_batchInsertUpdate($table, $columns, $rows, $callbackWhenAffectedRow);
        } catch (\PDOException $e) {
            if ($e->getCode() == 40001) {
                if ($attempts < 5) {
                    $attempts += 1;
                    //UtilsDeveloper::log("Error in the LightAdapter::batchInsertUpdate - (attemp #({$attempts}) - error: {$e->getCode()}: {$e->getMessage()}  RETRYING...", UtilsDeveloper::LOG_ERROR);
                    sleep($attempts);
                    return $this->_batchInsertUpdate($table, $columns, $rows, $callbackWhenAffectedRow);
                }
            } else {
                $attempts = 0;
                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function _batchInsertUpdate($table, array $columns, array $rows, $callbackWhenAffectedRow = null)
    {
        // Mysql Transaction Start - improves performance
        $this->con->prepare('START TRANSACTION')->execute();

        $columns = $this->prepareColumns($columns);

        $template_query = "INSERT INTO $table SET $columns ON DUPLICATE KEY UPDATE $columns";
        //Developer::log('SQL STATEMENT: ' . $template_query, Developer::LOG_ANNOY);

        $statement = $this->con->prepare($template_query);

        $inserted_count = 0;
        foreach ($rows as $row) {
            //Developer::log('SQL VALUES: | ' . implode(' | ', $row) . ' |', Developer::LOG_ANNOY);

            $row = array_values($row);

            if ($statement->execute(array_merge($row, $row))) {

                if($statement->rowCount()>0){
                    if (is_callable($callbackWhenAffectedRow)) {
                        $callbackWhenAffectedRow($row);
                    }
                };

                ++$inserted_count;

            } else {
                throw new \Exception(var_export($statement->errorInfo(), 1));
            }
        }


        // Mysql Transaction Ends
        $this->con->prepare('COMMIT')->execute();

        return $inserted_count;
    }

    public function insertUpdate($table, array $columns, array $row)
    {
        $row = array_values($row);
        $columns = $this->prepareColumns($columns);

        $template_query = "INSERT INTO $table SET $columns ON DUPLICATE KEY UPDATE $columns";

        return $this->write($template_query, array_merge($row, $row));
    }

    /**
     * Generate a set of "key = ?" values
     * WARNING: this code not prevent injections.
     *
     * @param string[] $columns
     *
     * @return string
     */
    private function prepareColumns($columns)
    {
        return implode(
            ', ',
            array_map(
                function ($v) {
                    return "`$v` = ?";
                },
                $columns
            )
        );
    }
}