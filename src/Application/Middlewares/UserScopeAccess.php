<?php


namespace App\Application\Middlewares;


use APP\Adapters\Cfg;
use App\Domain\DTO\Utils\Utils;
use App\Infraestructure\Adapters\BunkerApi;
use App\Infraestructure\Contexts\UserContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

class UserScopeAccess implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if ($request->getMethod() == "OPTIONS") {
            return $handler->handle($request);
        }
        //know the domain of the request to call his Bunker-Api
        if (!is_null(UserContext::getInstance()->getUserId())) {
            $host = Utils::getHost((RouteContext::fromRequest($request))->getRoute()->getArgument('instance'));
            $authorization = $request->getHeader('Authorization');
            $response = (new BunkerApi($host))->getUserAccess(UserContext::getInstance()->getUserId(), $authorization[0]);
            UserContext::getInstance()->setBrandUserAccess(array_filter($response["brands"],function($value){return $value!="-1";}));
            UserContext::getInstance()->setCampaignUserAccess($response["campaigns"]);
        }
        return $handler->handle($request);
    }
}