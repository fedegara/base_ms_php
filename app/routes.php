<?php
declare(strict_types=1);

use App\Application\Actions\Brand\Get;
use App\Application\Actions\Status;
use Cratia\Rest\Actions\Observability\Ping;
use Cratia\Rest\Actions\Observability\Error;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

//TODO: Always reflect all the new routes in the README
return function (App $app) {

    $app->get('/ping', Ping::class);
    $app->get('/status', Status::class);
    $app->get('/error', Error::class);
    $app->get('/pong', Ping::class)->setArgument("auth", "true");

    $auth_scoped_routes = [
        [
            'method' => ["GET"],
            'route' => 'brands[/]',
            'callable' => Get::class
        ]
    ];

    $auth_scoped_period_routes = [
        [
            'method' => ["GET"],
            'route' => 'brands[/]',
            'callable' => Get::class
        ]
    ];

    /** ------------------------------------------------------------------------------------------------------------------ */


    $app->group("/", function (RouteCollectorProxy $group) use ($auth_scoped_routes) {
        foreach ($auth_scoped_routes as $route) {
            $group->map($route['method'], $route['route'], $route['callable'])->setArgument("auth", "true");
        }
    });


    $app->group("/instance/{instance}/otype/{otype}/oid/{oid}/entity/{entity_id}/period_id/{period_id}/period_start/{period_start}/period_end/{period_end}/", function (RouteCollectorProxy $group) use ($auth_scoped_period_routes) {
        foreach ($auth_scoped_period_routes as $route) {
            $group->map($route['method'], $route['route'], $route['callable'])->setArgument("auth", "true");
        }
    });


    $app->group("/", function (RouteCollectorProxy $group) use ($auth_scoped_routes) {
        foreach (array_unique(array_column($auth_scoped_routes, 'route')) as $route) {
            $group->options($route, function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
                // Do nothing here. Just return the response.
                return $response;
            });
        }
    });

    $app->group("/instance/{instance}/otype/{otype}/oid/{oid}/organization/{organization_id}/period_id/{period_id}/period_start/{period_start}/period_end/{period_end}/", function (RouteCollectorProxy $group) use ($auth_scoped_period_routes) {
        foreach (array_unique(array_column($auth_scoped_period_routes, 'route')) as $route) {
            $group->options($route, function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
                // Do nothing here. Just return the response.
                return $response;
            });
        }
    });
};

