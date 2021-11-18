<?php

namespace App\Infraestructure\Persistence\MySql;

use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use Cratia\ORM\DBAL\Adapter\MysqlAdapter;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class MySqlConnection extends MysqlAdapter implements IAdapter
{
    /**
     * DataBase constructor.
     * @param string $mysqlDsn
     * @param LoggerInterface|null $logger
     * @param EventManager|null $eventManager
     * @throws DBALException
     */
    public function __construct(string $mysqlDsn, ?LoggerInterface $logger = null, ?EventManager $eventManager = null)
    {
        parent::__construct(['url' => $mysqlDsn], $logger, $eventManager);
    }
}
