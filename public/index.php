<?php
declare(strict_types=1);

use App\Domain\Inheritances\HttpErrorHandlerCors;
use Cratia\Rest\Dependencies\ErrorManager;
use DI\Container;
use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;

require __DIR__ . '/../vendor/autoload.php';

if (isset($_ENV['DEFAULT_TIMEZONE'])) {
    date_default_timezone_set($_ENV['DEFAULT_TIMEZONE']);
}

// Instantiate PHP-DI ContainerBuilder
/** @var ContainerBuilder $containerBuilder */
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require ROOT_PATH . 'app/settings.php';
$settings($containerBuilder);

if (isset($_GET["DEBUG"]) && $_GET["DEBUG"] == "true") {
    $_ENV['DEBUG_ON'] = true;
}

// Set up dependencies
$dependencies = require ROOT_PATH . 'app/dependencies.php';
$dependencies($containerBuilder);

// Build PHP-DI Container instance
/** @var Container $container */
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
/** @var App $app */
$app = AppFactory::create();

// Register middleware
$middleware = require ROOT_PATH . 'app/middleware.php';
$middleware($app);

// Register routes
$routes = require ROOT_PATH . 'app/routes.php';
$routes($app);

// Create internals Error Handlers
ErrorManager::getInstance()->registerErrorHandler($container, E_ALL);
// Create Shutdown Handler
ErrorManager::getInstance()->registerShutdownHandler($container);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add MethodOverride Middleware
$app->add(new MethodOverrideMiddleware());

//Add Request Body Parsing Middleware (Necessary from Slim version >= 4.0)
$app->addBodyParsingMiddleware();

// Create Error Handler
$errorHandler = new HttpErrorHandlerCors(
    $app->getContainer(),
    $app->getCallableResolver(),
    $app->getResponseFactory()
);

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(
    $app->getContainer()->get('settings')['displayErrorDetails'],
    false,
    false
);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$app->run();
