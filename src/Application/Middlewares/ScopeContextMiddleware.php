<?php


namespace App\Application\Middlewares;

use App\Context\ScopeContext;
use App\Domain\DAO\BrandDAO;
use App\Domain\DAO\CampaignDAO;
use App\Domain\DTO\Fields\Consumers\FieldFactory;
use App\Domain\DTO\Fields\Consumers\Scope;
use App\Domain\DTO\Filters\FilterValue;
use App\Domain\Inheritances\QueryFilterAction;
use App\Domain\Repositories\ConsumerField;
use App\Infraestructure\Contexts\UserContext;
use App\Persistence\DataBase;
use Doctrine\Common\EventManager;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

class ScopeContextMiddleware implements MiddlewareInterface
{

    /** @var ContainerInterface */
    private $container;
    private $adapter;
    private $logger;
    private $eventManager;

    /**
     * HeaderContext constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() == "OPTIONS" || !isset($_ENV['cfg'])) {
            return $handler->handle($request);
        }
        $this->adapter = $this->container->get(DataBase::class);
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->eventManager = $this->container->get(EventManager::class);
        ScopeContext::getInstance()->setHost($_ENV['CONSUMERS_MS_URL']);

        $this->setRouteURLScope((RouteContext::fromRequest($request))->getRoute());
        $this->setParamsScope($request);
        $this->checkAccess();
        return $handler->handle($request);
    }


    private function checkAccess()
    {

        if (!ScopeContext::getInstance()->isMultibrand()) {
            if (ScopeContext::getInstance()->getOType() == ScopeContext::BRAND_O_TYPE) {
                if (!in_array(ScopeContext::getInstance()->getBrandid(), UserContext::getInstance()->getBrandUserAccess())) {
                    throw new Exception("Brand is not access by the user logged in", 401);
                }
            } elseif (ScopeContext::getInstance()->getOType() == ScopeContext::CAMPAIGN_O_TYPE) {
                if (!in_array(ScopeContext::getInstance()->getCampaignId(), UserContext::getInstance()->getCampaignUserAccess())) {
                    throw new Exception("Campaign is not access by the user logged in", 401);
                }
            }
        }


        if (
            !empty(ScopeContext::getInstance()->getCampaignsFiltered())
            &&
            !empty(array_diff(
                array_map(function (CampaignDAO $campaignDAO) {
                    return $campaignDAO->getId();
                }, ScopeContext::getInstance()->getCampaignsFiltered()),
                UserContext::getInstance()->getCampaignUserAccess()
            ))
        ) {
            throw new Exception("Some of the campaigns that are filtering the logged user has no access", 401);
        }


        if (
            !empty(ScopeContext::getInstance()->getBrandsFiltered())
            &&
            !empty(array_diff(
                array_map(function (BrandDAO $brandDAO) {
                    return $brandDAO->getId();
                }, ScopeContext::getInstance()->getBrandsFiltered()),
                UserContext::getInstance()->getBrandUserAccess()
            ))
        ) {
            throw new Exception("Some of the brands that are filtering the logged user has no access", 401);
        }


    }


    /**
     *
     * This function is to load to the scope if they are filtering for brand or campaigns
     * We need to know this to build the querys
     *
     * @param ServerRequestInterface $request
     * @return bool
     * @throws Exception
     */
    private function setParamsScope(ServerRequestInterface $request)
    {
        if (empty($request->getBody()->getContents())) {
            return false;
        }
        $params = json_decode($request->getBody()->getContents(), true);
        if (!empty($params)) {
            QueryFilterAction::checkBody($params);
            $params = (isset($params["filter"]) && !is_null($params["filter"])) ? $params["filter"] : [];
            $scope_filters = [];
            if (!empty($params)) {
                $scope_filters = array_filter(\App\Domain\Repositories\FilterValue::loadFiltersByParams($params['filters'], $this->container), function (FilterValue $filterValue) {
                    return $filterValue->getFilter()->getConsumerField()->getEdge() === FieldFactory::EDGE_SCOPE && in_array($filterValue->getFilter()->getConsumerField()->getColumn(),['campaign_id','brand_id']);
                });
            }
            if (!empty($scope_filters) && ScopeContext::getInstance()->getOType() == ScopeContext::CAMPAIGN_O_TYPE) {
                throw new Exception("Cannot set scope filters if in the url path the otype is campaign");
            }
            foreach ($scope_filters as $filter) {
                /** @var FilterValue $filter */
                switch ($filter->getFilter()->getConsumerField()->getColumn()) {
                    case Scope::CAMPAIGN_FIELD:
                        if (!is_null(ScopeContext::getInstance()->getCampaign()) || ScopeContext::getInstance()->getOType() == ScopeContext::CAMPAIGN_O_TYPE) {
                            throw new Exception("Cannot filter by campaign if campaign in context route.");
                        }
                        ScopeContext::getInstance()->setCampaignsFiltered(array_map(function ($id) {
                            return (new CampaignDAO($id))->inject($this->adapter, $this->logger, $this->eventManager)->load();
                        }, !is_array($filter->getValue()) ? [$filter->getValue()] : $filter->getValue()));
                        break;
                    case Scope::BRAND_FIELD:
                        if (!ScopeContext::getInstance()->isMultibrand()) {
                            throw new Exception("Cannot filter by brand if is not multibrand.");
                        }
                        ScopeContext::getInstance()->setBrandsFiltered(array_map(function ($id) {
                            return (new BrandDAO($id))->inject($this->adapter, $this->logger, $this->eventManager)->load();
                        }, !is_array($filter->getValue()) ? [$filter->getValue()] : $filter->getValue()));
                        break;
                }
            }
        }


    }

    private function setRouteURLScope(RouteInterface $route)
    {

        if (!empty($route->getArguments())) {
            if (!is_null($route->getArgument('oid'))) {
                if (ScopeContext::getInstance()->getOType() == ScopeContext::BRAND_O_TYPE) {
                    ScopeContext::getInstance()->setScopeField(ConsumerField::loadConsumerFieldByHumanKey(FieldFactory::EDGE_SCOPE . "-" . Scope::BRAND_FIELD, $this->container));
                    if ($route->getArgument('oid') == "-1") {
                        ScopeContext::getInstance()->setIsMultibrand(true);
                    } else {
                        try {
                            ScopeContext::getInstance()->setBrand((new BrandDAO($route->getArgument('oid')))->inject($this->adapter, $this->logger, $this->eventManager)->load());
                        } catch (Exception $exception) {
                            if ($exception->getCode() == 412) {
                                throw new Exception("Brand id({$route->getArgument('oid')}) indicated in url path doesnt exists", 412);
                            }
                        }
                    }

                } else {
                    ScopeContext::getInstance()->setScopeField(ConsumerField::loadConsumerFieldByHumanKey(FieldFactory::EDGE_SCOPE . "-" . Scope::CAMPAIGN_FIELD, $this->container));
                    try {
                        ScopeContext::getInstance()->setCampaign((new CampaignDAO($route->getArgument('oid')))->inject($this->adapter, $this->logger, $this->eventManager)->load());
                    } catch (Exception $exception) {
                        if ($exception->getCode() == 412) {
                            throw new Exception("Campaign id ({$route->getArgument('oid')}) indicated in url path doesnt exists", 412);
                        }
                    }
                    ScopeContext::getInstance()->setBrand((new BrandDAO(ScopeContext::getInstance()->getCampaign()->getIdBrand()))->inject($this->adapter, $this->logger, $this->eventManager)->load());
                }
            }
        }
    }
}