<?php
require './vendor/autoload.php';

error_reporting(E_ALL & ~E_NOTICE);

use AntiMattr\MongoDB\Migrations\Configuration\ConfigurationBuilder;
use AntiMattr\MongoDB\Migrations\OutputWriter;
use AntiMattr\MongoDB\Migrations\Tools\Console\Command as AntiMattr;
use MongoDB\Client;
use Symfony\Component\Console\Application;

$uriOptions = [];

if ($_ENV['MONGO_AMAZON'] == "true") {
    $uriOptions = [
        'tls' => true,
        'tlsAllowInvalidHostnames' => true,
        'tlsCAFile' => ROOT_PATH . 'src/Infraestructure/Persistence/Mongo/certs/rds-combined-ca-bundle.pem',
        'retryWrites' => false
    ];
}



$conf = ConfigurationBuilder::create()
    ->setConnection(new Client($_ENV['MONGO_DSN_WRITER'], $uriOptions))
    ->setOnDiskConfiguration('migrations.yml')
    ->build();

$application = new Application();


$commands = [
    new AntiMattr\ExecuteCommand(),
    new AntiMattr\GenerateCommand(),
    new AntiMattr\MigrateCommand(),
    new AntiMattr\StatusCommand(),
    new AntiMattr\VersionCommand()
];
foreach($commands as $command){
    $command->setMigrationConfiguration($conf);
}

$application->addCommands($commands);
$application->run();