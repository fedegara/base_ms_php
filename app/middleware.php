<?php

declare(strict_types=1);


use App\Application\Middlewares\AuthenticationJWT;
use App\Application\Middlewares\BasicScopeContext;
use App\Application\Middlewares\RequestMiddleware;
use App\Middleware\CorsMiddleware;
use Cratia\Rest\Middleware\Context;
use Cratia\Rest\Middleware\RouteInfo;
use Slim\App;


return function (App $app) {
    $container = $app->getContainer();
    if(is_null($container)){
        throw  new Exception("Error container not specified");
    }
    $app->addMiddleware(new RouteInfo($container));
    $app->addMiddleware(new CorsMiddleware());
    $app->addMiddleware(new Context($container));
    $app->addMiddleware(new AuthenticationJWT());
    $app->addMiddleware(new BasicScopeContext());
    $app->addMiddleware(new RequestMiddleware());
};
