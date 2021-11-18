<?php
declare(strict_types=1);

use App\Domain\DAO\Adapters\Mongo\BrandMongoAdapter;
use App\Domain\DAO\Adapters\Mysql\BrandMysqlAdapter;
use App\Domain\Events\Subscribers\ActiveRecord;
use App\Domain\Events\Subscribers\Adapter;
use App\Domain\Inheritances\LogInterfaces\LogdnaInterface;
use App\Domain\Interfaces\DAO\IBrandDAO;
use App\Infraestructure\Persistence\Mongo\MongoConnection;
use App\Infraestructure\Persistence\MySql\MySqlConnection;
use Cratia\Rest\Dependencies\AppManager;
use Cratia\Rest\Dependencies\DebugBag;
use Cratia\Rest\Dependencies\ErrorBag;
use Cratia\Rest\Dependencies\ErrorManager;
use DI\ContainerBuilder;
use Doctrine\Common\EventManager;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {

    $definitions = [
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $logger->pushProcessor(new UidProcessor());
            $logger->pushProcessor(new MemoryUsageProcessor());
            $logger->pushProcessor(new IntrospectionProcessor());
            $logger->pushProcessor(new WebProcessor());

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        ErrorManager::class => function () {
            return ErrorManager::getInstance();
        },

        AppManager::class => function () {
            return AppManager::getInstance();
        },

        MongoConnection::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            return MongoConnection::getInstance($settings['MONGO_DSN_READER'], $settings['MONGO_DB_NAME'], $settings['MONGO_AMAZON']);
        },

        MySqlConnection::class => function (ContainerInterface $c) {
            $eventManager = $c->get(EventManager::class);
            $logger = $c->get(LoggerInterface::class);
            $settings = $c->get('settings');
            return new MySqlConnection($settings['MYSQL_DSN'],$logger, $eventManager);
        },

        LogdnaInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $loggerSettings = $settings['logdna_path'];
            $logger = new Logger($loggerSettings['name']);
            $logger->pushHandler(
                (new StreamHandler("{$loggerSettings['path']}linkedin-ms-error.log", $loggerSettings['level']))->setFormatter(new LineFormatter("%message%\n"))
            );
            return $logger;
        },

        IBrandDAO::class => function (ContainerInterface $container) {
            return new BrandMongoAdapter($container->get(MongoConnection::class));
        },

//        IBrandDAO::class => function (ContainerInterface $container) {
//            return new BrandMysqlAdapter($container->get(MySqlConnection::class));
//        },
    ];
    if ($_ENV['DEBUG_ON'] == "true") {
        $definitions[ErrorBag::class] = function () {
            return ErrorBag::getInstance();
        };

        $definitions[DebugBag::class] = function () {
            return DebugBag::getInstance();
        };

        $definitions[EventManager::class] = function (ContainerInterface $c) {
            $debugBag = $c->get(DebugBag::class);
            $errorBag = $c->get(ErrorBag::class);

            $subscriber1 = new ActiveRecord($debugBag, $errorBag);
            $subscriber2 = new Adapter($debugBag);

            $eventManager = new EventManager();
            $eventManager->addEventSubscriber($subscriber1);
            $eventManager->addEventSubscriber($subscriber2);

            return $eventManager;
        };

    }else {
        $definitions[EventManager::class] = function (ContainerInterface $c) {
            $eventManager = new EventManager();
            return $eventManager;
        };
    }
    $containerBuilder->addDefinitions($definitions);
};
