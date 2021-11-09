<?php
require __DIR__.'/vendor/autoload.php';

$environment = [];

if (isset($_ENV['DB_SERVER'])){
    $environment = [
        'adapter' => 'mysql',
        'host' => $_ENV['DB_SERVER'],
        'name' => $_ENV['DB_BASE'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASS'],
        'port' => $_ENV['DB_PORT'],
        'charset' => $_ENV['DB_CHARSET'],
    ];
}

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => $environment
    ],
    'version_order' => 'creation'
];
