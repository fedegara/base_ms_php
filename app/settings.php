<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Psr\Log\LogLevel;

return function (ContainerBuilder $containerBuilder) {
    $root = preg_replace("/\bpublic\b/", "", $_SERVER["DOCUMENT_ROOT"]);

    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'determineRouteBeforeAppMiddleware' => true,

            'displayErrorDetails' => isset($_ENV['DEBUG_ON'])
                ? filter_var($_ENV['DEBUG_ON'], FILTER_VALIDATE_BOOLEAN)
                : false
            ,
            'logger' => [
                'name' => 'slim-app',
                'path' => 'php://stdout',
                'level' => Logger::ERROR,
            ],
            'logdna_path' => [
                'name' => 'logdna_logger',
                'path' => $root . $_ENV['LOG_PATH'],
                'level' => LogLevel::ERROR,
            ],
            'currencies_logdna_path' => [
                'name' => 'logdna_logger',
                'path' => $root . $_ENV['LOG_PATH'],
                'level' => LogLevel::ERROR,
            ],
            'DEBUG_ON' => $_ENV['DEBUG_ON'],
            "OAUTH_SERVICE_URL" => $_ENV['OAUTH_SERVICE_URL'],
            "DB" => $_ENV['USE_CONNECTION'],
            "MONGO_DSN_READER" => $_ENV['MONGO_DSN_READER'],
            "MONGO_DB_NAME" => $_ENV['MONGO_DB_NAME'],
            "MONGO_AMAZON" => $_ENV['MONGO_AMAZON'] === "true",
            "MYSQL" => [
                "MYSQL_SERVER" => $_ENV['DB_SERVER'],
                "MYSQL_BASE" => $_ENV['DB_BASE'],
                "MYSQL_USER" => $_ENV['DB_USER'],
                "MYSQL_PASS" => $_ENV['DB_PASS'],
                "MYSQL_PORT" => $_ENV['DB_PORT'],
                "MYSQL_CHARSET" => $_ENV['DB_CHARSET'],
                "MYSQL_PERSISTENT" => $_ENV['DB_PERSISTENT'],
            ]
        ],
    ]);
};
