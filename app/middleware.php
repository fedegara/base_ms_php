<?php
declare(strict_types=1);


use App\Application\Middlewares\AuthenticationJWT;
use App\Application\Middlewares\BasicScopeContext;
use App\Application\Middlewares\CFGDB;
use App\Application\Middlewares\RequestMiddleware;
use App\Application\Middlewares\ScopeContextMiddleware;
use App\Application\Middlewares\UserScopeAccess;
use App\Middleware\CorsMiddleware;
use Cratia\Rest\Middleware\Context;
use Cratia\Rest\Middleware\RouteInfo;
use Slim\App;


return function (App $app) {
    $app->addMiddleware(new ScopeContextMiddleware($app->getContainer()));
    $app->addMiddleware(new RouteInfo($app->getContainer()));
    $app->addMiddleware(new CorsMiddleware());
    $app->addMiddleware(new Context($app->getContainer()));
    $app->addMiddleware(new AuthenticationJWT());
    $app->addMiddleware(new BasicScopeContext());
    $app->addMiddleware(new RequestMiddleware());
};
