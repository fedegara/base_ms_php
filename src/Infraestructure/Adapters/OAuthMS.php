<?php

namespace Clients;

use Core\BunkerApiProxy;
use CSApi\Api;
use CSApi\ApiRequest;
use CSApi\ApiResponse;
use CSApi\Cache\Adapter\FilesystemPool;
use CSApi\Cache\Adapter\MemcachePool;
use CSApi\Cache\CacheManager;
use Exception;
use Memcache;
use Memcached;
use Monolog\Logger;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Created by PhpStorm.
 * User: dabreu
 * Date: 4/21/20
 * Time: 3:58 p. m.
 */
class OAuthMS
{
    /** @var Api */
    private $api;

    /**
     * OAuthMS constructor.
     * @throws Exception
     */
    public function __construct()
    {
        // cache class definition
        // using memcache
        if (class_exists('Memcached') || class_exists('Memcache')) {
            $memCli = class_exists('Memcached') ? new Memcached() : new Memcache();
            $memCli->addServer('localhost', 11211);
            $cacheAdapter = new MemcachePool($memCli);
        } else {
            // using file
            $cacheAdapter = new FilesystemPool('./_cache');
        }

        $this->api = new Api(
            $_ENV['OAUTH_SERVICE_URL'],
            [
                Api::OPT_DEBUG => $_ENV['DEBUG_ON'],
                Api::OPT_CACHE => [
                    'adapter' => $cacheAdapter,
                    CacheManager::CMO_TTL => (isset($_ENV['OAUTH_TTL'])) ? intval($_ENV['OAUTH_TTL']) : 300,
                ]
            ]);
    }

    /**
     * @param string $token
     * @return ApiResponse
     * @throws Exception
     */
    public function validate(string $token)
    {
        return (new ApiRequest(
            ApiRequest::METHOD_POST,
            'validate',
            ["token" => $token]
        ))
            ->setApi($this->api)
            ->execute();
    }
}