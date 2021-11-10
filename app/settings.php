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
            "MONGO_DSN_READER" => $_ENV['MONGO_DSN_READER'],
            "MONGO_DB_NAME" => $_ENV['MONGO_DB_NAME'],
            "MONGO_AMAZON" => $_ENV['MONGO_AMAZON'] === "true",
            "MYSQL_DSN" => 'mysql://'.$_ENV['DB_USER'].':'.$_ENV['DB_PASS'].'@'.$_ENV['DB_SERVER'].'/'.$_ENV['DB_BASE'],
        ],
    ]);
};
