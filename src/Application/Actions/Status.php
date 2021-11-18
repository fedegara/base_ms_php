<?php
/**
 * Created by PhpStorm.
 * User: dabreu
 * Date: 2/1/21
 * Time: 1:54 p. m.
 */

namespace App\Application\Actions;

use App\Domain\DTO\Utils\Cipher\GPG;
use App\Domain\DTO\Utils\Cipher\SSL;
use App\Infraestructure\Adapters\QueueAdapter;
use App\Infraestructure\Persistence\Mongo\MongoConnection;
use App\Infraestructure\Persistence\MySql\MySqlConnection;
use Cratia\Rest\Actions\Action;
use Exception;

class Status extends Action
{
    /**
     * @return array<string,mixed>
     * @throws Exception
     */
    protected function action()
    {
        $mongoStatus = $this->testMongoDB();
        $mySqlStatus = $this->testMySql();
        return [
            'MongoDB' => $mongoStatus === true ? 'OK' : $mongoStatus,
            'Mysql' => $mySqlStatus === true ? 'OK' : $mySqlStatus
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function testMongoDB()
    {
        try {
            /** @var MongoConnection $mongo_connection */
            $mongo_connection = $this->getContainer()->get(MongoConnection::class);
            $mongo_connection->listCollections();
            return true;
        } catch (Exception $ex) {
            throw new Exception("MongoDB: ERROR -> [EXCEPTION] {$ex->getMessage()}");
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function testMySql()
    {
        try {
            /** @var MySqlConnection $mysql_connection */
            $mysql_connection = $this->getContainer()->get(MySqlConnection::class);
            $mysql_connection->query("show tables");
            return true;
        } catch (Exception $ex) {
            throw new Exception("MongoDB: ERROR -> [EXCEPTION] {$ex->getMessage()}");
        }
    }
}
