<?php


namespace App\Infraestructure\Adapters;


use CSApi\Api;
use CSApi\ApiRequest;
use CSApi\Cache\Adapter\FilesystemPool;
use CSApi\Cache\Adapter\MemcachePool;
use DateTime;
use Exception;
use Memcache;
use Memcached;

class BunkerCurrencyApi
{

    /** @var Api */
    private $api;

    /**
     * BunkerCurrencyApi constructor.
     */
    public function __construct($currency_url)
    {
        if (class_exists('Memcached') || class_exists('Memcache')) {
            $memCli = class_exists('Memcached') ? new Memcached() : new Memcache();
            $memCli->addServer('localhost', 11211);
            $cacheAdapter = new MemcachePool($memCli);
        } else {
            // using file
            $cacheAdapter = new FilesystemPool('./_cache');
        }

        $this->api = new Api(
            "{$currency_url}/",//I know this is better to load from container
            [
                Api::OPT_DEBUG => $_ENV['DEBUG_ON']
            ]
        );
    }

    public function getRate(DateTime $dateTime, string $from, string $to)
    {
        $request = new ApiRequest(
            ApiRequest::METHOD_GET,
            "history/getrate/{$dateTime->format("Y-m-d")}/{$from}/{$to}"
        );
        $response = $request->setApi($this->api)->execute();
        if(isset($response->getContent()['error']) && $response->getContent()['error']===true){
            throw new Exception("Error on BunkerCurrencyApi: {$response->getContent()['message']}",412);
        }
        return $response->getContent();
    }


}