<?php


namespace App\Infraestructure\Adapters;


use App\Context\ScopeContext;
use CSApi\Api;
use CSApi\ApiRequest;
use CSApi\ApiResponse;
use CSApi\Cache\Adapter\FilesystemPool;
use CSApi\Cache\Adapter\MemcachePool;
use CSApi\Cache\CacheManager;
use Exception;
use Memcache;
use Memcached;
use Psr\SimpleCache\InvalidArgumentException;

class BunkerApi
{
    /** @var Api */
    private $api;
    /** @var string */
    private $instance_id;
    /** @var string */
    private $service_id;

    public function __construct(string $domain)
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
//TODO ELIMINAR ESTO
        $protocol = "https";
        if (isset($_ENV['IS_UNILEVER']) && strtolower($_ENV['IS_UNILEVER']) === 'true') {
            $protocol = "http";
        }
        $this->api = new Api(
            "{$protocol}://{$domain}/bunker-api/",
            [
                Api::OPT_DEBUG => $_ENV['DEBUG_ON'],
                Api::OPT_CACHE => [
                    'adapter' => $cacheAdapter,
                    CacheManager::CMO_TTL => (isset($_ENV['BUNKER_API_TTL'])) ? intval($_ENV['BUNKER_API_TTL']) : 300,
                ]
            ]
        );
    }

    /**
     * @param int $user_id
     * @param string $bearer_token
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getUserAccess(int $user_id, string $bearer_token)
    {
        $request = new ApiRequest(
            ApiRequest::METHOD_GET,
            "users/{$user_id}/access",
            null,
            ["Authorization" => $bearer_token]
        );
        $response = $request->setApi($this->api)->execute();
        if ($response->getStatusCode() == 200) {
            $access = $response->getContent()['data'];
            if (empty($access)) {
                throw new Exception("Empty response from Bunker Api to get the access of user");
            }
            return $access;
        } else {
            throw new Exception("Service BunkerAPI not response properly: Code: {$response->getStatusCode()} | Error: {$response->getError()} | Content: {$response->getContent()}");
        }
    }

    /**
     * @param array $values
     * @param string $bearer_token
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getI18N(array $values, string $bearer_token)
    {
        $request = new ApiRequest(
            ApiRequest::METHOD_POST,
            "p/i18n",
            $values,
            ["Authorization" => $bearer_token]
        );
        $response = $request->setApi($this->api)->execute();
        if ($response->getStatusCode() == 200) {
            $translations = $response->getContent()['data'];
            if (empty($translations)) {
                throw new Exception("Empty response from Bunker Api to get translations");
            }
            return $translations;
        } else {
            throw new Exception("Service BunkerAPI not response properly: Code: {$response->getStatusCode()} | Error: {$response->getError()} | Content: {$response->getContent()}");
        }
    }


    /**
     * @param int $user_id
     * @param string $bearer_token
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getAdData(int $facebook_external_ad_id, string $bearer_token,$bunker_campaign_id)
    {
        $request = new ApiRequest(
            ApiRequest::METHOD_GET,
            "paid/facebook_business/objects/ads?limit=1&filters=external_facebook_id^[{$facebook_external_ad_id}]",
            null,
            ["Authorization" => $bearer_token,'bapi-context'=>json_encode(['otype'=>'campaign','oid'=> $bunker_campaign_id])]
        );
        $response = $request->setApi($this->api)->execute();
        if ($response->getStatusCode() == 200) {
            $ad_data = current($response->getContent()['data']);
            if (empty($ad_data)) {
                throw new Exception("Empty response from Bunker Api to get the ad_data", 404);
            }
            return $ad_data;
        } else {
            throw new Exception("Service BunkerAPI not response properly: Path: paid/facebook_business/objects/ads?limit=1&filters=external_facebook_id^[{$facebook_external_ad_id}] | Method: GET |  Code: {$response->getStatusCode()} | Error: {$response->getError()} | Content: {$response->getContent()}");
        }
    }


    /**
     * @param string $email
     * @param string $bearer_token
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getMailchimpDataByEmail(string $email, string $bearer_token)
    {
        $request = new ApiRequest(
            ApiRequest::METHOD_GET,
            "social/mailchimp/feedConsumers/{$email}",
            null,
            ["Authorization" => $bearer_token]
        );
        $response = $request->setApi($this->api)->execute();
        if ($response->getStatusCode() == 200) {
            $mailchimp_data = $response->getContent()['data'];
            if (empty($mailchimp_data)) {
                throw new Exception("Empty response from Bunker Api to get the mailchimp data", 404);
            }
            return $mailchimp_data;
        } else {
            throw new Exception("Service BunkerAPI not response properly: Code: {$response->getStatusCode()} | Error: {$response->getError()} | Content: {$response->getContent()}");
        }
    }
}