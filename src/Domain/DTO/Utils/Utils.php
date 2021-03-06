<?php
/**
 * Created by PhpStorm.
 * User: dabreu
 * Date: 11/27/20
 * Time: 2:39 p. m.
 */

namespace App\Domain\DTO\Utils;

use APP\Adapters\Cfg;
use App\Context\ScopeContext;
use App\Infraestructure\Contexts\RequestContext;
use App\Infraestructure\Contexts\UserContext;
use DateTime;
use Exception;
use Monolog\Logger;
use Throwable;
use TypeError;

class Utils
{
    /**
     * @param Logger $logger
     * @param Exception $exception
     * @throws Exception
     */
    public static function logException(Logger $logger, Exception $exception): void
    {
        $ret = json_encode([
            "dateTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "method" => RequestContext::getInstance()->getMethod(),
            "url" => RequestContext::getInstance()->getUrl(),
            "headers" => RequestContext::getInstance()->getHeaders(),
            "body" => RequestContext::getInstance()->getBody(),
            "instance" => ScopeContext::getInstance()->getInstanceName(),
            "oType" => ScopeContext::getInstance()->getOType(),
            "oId" => ScopeContext::getInstance()->getOId(),
            "userId" => UserContext::getInstance()->getUserId(),
            "userToken" => UserContext::getInstance()->getAuthorizationToken(),
            "statusCode" => (($exception->getCode()) ? $exception->getCode() : 500),
            "message" => $exception->getMessage(),
            "trace" => $exception->getTrace()
        ]);
        if ($ret !== false) {
            $logger->error($ret);
        }
    }
}
