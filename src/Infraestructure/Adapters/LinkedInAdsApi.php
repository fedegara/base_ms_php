<?php


namespace App\Infraestructure\Adapters;


use App\Context\ScopeContext;
use CSApi\Api;
use CSApi\ApiRequest;
use Exception;
use Psr\SimpleCache\InvalidArgumentException;

//TODO: Implement HttpInterface
final class LinkedInAdsApi
{
    const DEFAULT_QUERY_PARAMS = [
        'fdow' => 'monday',
        'tdc' => ',',
        'ttp' => '.',
        'currency' => 'USD',
        'investment_fee' => 1,
        'lang' => 'en',
    ];

    /** @var Api */
    private $api;

    /**
     * @param string $currency_url
     * @throws Exception
     */
    public function __construct(string $currency_url)
    {
        $this->api = new Api(
            "{$currency_url}/",
            [
                Api::OPT_DEBUG => $_ENV['DEBUG_ON']
            ]
        );
    }

    /**
     * @param array $media_ids
     * @param array|null $params
     * @return mixed
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getShareAds(array $media_ids, ?array $params)
    {
        $request = new ApiRequest(
            ApiRequest::METHOD_POST,
            $this->buildUrl('creatives', $params),
            $media_ids
        );

        $response = $request->setApi($this->api)->execute();

        if ($response->getError()) {
            throw new Exception("Error on LinkedInAdsApi: {$response->getError()}", 500);
        }

        if (empty($response->getContent()) || (isset($response->getContent()['error']) && !empty($response->getContent()['error']))) {
            throw new Exception("Error on LinkedInAdsApi: {$response->getContent()['error']}", 412);
        }

        return $response->getContent();
    }

    /**
     * @param string $endpoint
     * @param array|null $params
     * @return string
     */
    private function buildUrl(string $endpoint, ?array $params): string
    {
        $scopeContext = ScopeContext::getInstance();

        return "/instances/{$scopeContext->getInstanceName()}/otype/{$scopeContext->getOType()}/oid/{$scopeContext->getOId()}/period/{$scopeContext->getPeriodId()}/startDate/{$scopeContext->getPeriodStart()}/endDate/{$scopeContext->getPeriodEnd()}/{$endpoint}/?" . $this->buildQueryParams($params);
    }

    /**
     * @param array|null $params
     * @return string
     */
    private function buildQueryParams(?array $params): string
    {
        $scopeContext = ScopeContext::getInstance();

        return urldecode(http_build_query([
            'orders' => $params['order'] ?? "",
            'fdow' => $params['fdow'] ?? self::DEFAULT_QUERY_PARAMS['fdow'],
            'tdc' => $params['tdc'] ?? self::DEFAULT_QUERY_PARAMS['tdc'],
            'ttp' => $params['ttp'] ?? self::DEFAULT_QUERY_PARAMS['ttp'],
            'currency' => $params['currency'] ?? self::DEFAULT_QUERY_PARAMS['currency'],
            'investment_fee' => $params['investment_fee'] ?? self::DEFAULT_QUERY_PARAMS['investment_fee'],
            'lang' => $params['lang'] ?? self::DEFAULT_QUERY_PARAMS['lang'],
            'from_date_object' => $scopeContext->getPeriodStart(),
            'to_date_object' => $scopeContext->getPeriodEnd(),
            'brand_id' => $scopeContext->getOId(),
        ]));
    }
}