<?php
/**
 * Created by PhpStorm.
 * User: dabreu
 * Date: 4/24/20
 * Time: 2:58 p. m.
 */

namespace App\Application\Middlewares;

use App\Context\ScopeContext;
use App\Infraestructure\Contexts\UserContext;
use Clients\OAuthMS;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Slim\Routing\Route;

class AuthenticationJWT implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // detect route authentication
        $needAuthentication = $this->detectAuthentication($request);

        if ($needAuthentication) {
            $authorization = $request->getHeader('Authorization');
            if (!is_null($authorization) && !empty($authorization) && is_array($authorization)) {
                $authorization = $authorization[0];
                UserContext::getInstance()->setAuthorizationToken($authorization);
                $_token = trim(str_replace("Bearer","",$authorization));
            } else {
                throw new Exception('Invalid authentication.', 401);
            }
            $validation = (new OAuthMS())
                ->validate($_token)
                ->getContent()['data'];

            // $validation have jwt payload information do whatever you want
            if (empty($validation) || !$validation) {
                throw new Exception('Invalid authentication.', 401);
            }

            UserContext::getInstance()->setUserId($validation['user']['id']);
            UserContext::getInstance()->setUserEmail($validation['user']['email']);
            UserContext::getInstance()->setLang($validation['lang']);
        }

        return $handler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function detectAuthentication(ServerRequestInterface $request)
    {
        // resolve by route
        /** @var Route $route */
        $route = $request->getAttributes()['__route__'];
        $authentication = false;
        if (isset($route->authentication)){
            $authentication = $route->authentication;
        }else {
            $routeGroups = $route->getGroups();
            if (count($routeGroups) > 0) {
                $authentication = $routeGroups[0]->authentication;
            }
        }
        return (bool) $authentication;
    }
}