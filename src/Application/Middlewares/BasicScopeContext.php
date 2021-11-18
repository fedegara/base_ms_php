<?php


namespace App\Application\Middlewares;


use App\Context\ScopeContext;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class BasicScopeContext implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = (RouteContext::fromRequest($request))->getRoute();
        if (!empty($route->getArguments())) {
            if (!is_null($route->getArgument('instance'))) {
                ScopeContext::getInstance()->setInstanceName($route->getArgument('instance'));
            }
            if (!is_null($route->getArgument('otype'))) {
                ScopeContext::getInstance()->setOType($route->getArgument('otype'));
            }
            if (!is_null($route->getArgument('oid'))) {
                ScopeContext::getInstance()->setOId(intval($route->getArgument('oid')));
            }
            if (!is_null($route->getArgument('period_id'))) {
                ScopeContext::getInstance()->setPeriodId($route->getArgument('period_id'));
            }
            if (!is_null($route->getArgument('period_start'))) {
                ScopeContext::getInstance()->setPeriodStart($route->getArgument('period_start'));
            }
            if (!is_null($route->getArgument('period_end'))) {
                ScopeContext::getInstance()->setPeriodEnd($route->getArgument('period_end'));
            }
            if (!is_null($route->getArgument('entity'))) {
                ScopeContext::getInstance()->setPeriodEnd($route->getArgument('period_end'));
            }
        }
        return $handler->handle($request);
    }


}