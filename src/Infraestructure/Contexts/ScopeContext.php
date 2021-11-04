<?php
declare(strict_types=1);

namespace App\Context;

use App\Domain\DAO\BrandDAO;
use App\Domain\DAO\CampaignDAO;
use App\Domain\DAO\ConsumerFieldDAO;
use App\Persistence\DataBase;
use Cratia\ORM\DBAL\Adapter\Interfaces\IAdapter;
use Exception;
use JsonSerializable;
use phpDocumentor\Reflection\Types\Self_;
use Psr\Container\ContainerInterface;

class ScopeContext implements JsonSerializable
{
    CONST CAMPAIGN_O_TYPE = 'campaign';
    CONST BRAND_O_TYPE = 'brand';


    private static $priv_instance;

    /** @var string */
    private $host;
    /** @var BrandDAO */
    private $brand = null;
    /** @var CampaignDAO */
    private $campaign = null;
    /** @var string [CAMPAIGN|BRAND] */
    private $oType = null;
    /** @var integer */
    private $oId = null;
    /** @var string */
    private $instanceName = null;
    /** @var BrandDAO[]  */
    private $brands_filtered=null;
    /** @var CampaignDAO[]  */
    private $campaigns_filtered=null;
    /** @var ConsumerFieldDAO */
    private $scopeField;

    /** @var bool  */
    private $isMultibrand=false;

    /**
     * @var string
     */
    private $organizationId;
    /**
     * @var string
     */
    private $periodId;
    /**
     * @var string
     */
    private $periodStart;
    /**
     * @var string
     */
    private $periodEnd;

    private function __construct()
    {
    }

    /**
     * @return ScopeContext
     */
    public static function getInstance(): ScopeContext
    {
        if (!self::$priv_instance instanceof ScopeContext) {
            self::$priv_instance = new self();
        }
        return self::$priv_instance;
    }

    /**
     * @return int|null
     */
    public function getBrandid(): ?int
    {
        if (is_null($this->brand)) {
            throw new Exception("Brand not defined");
        }
        return intval($this->brand->getId());
    }


    /**
     * @return bool
     */
    public function isMultibrand(): bool
    {
        return $this->isMultibrand;
    }

    /**
     * @param bool $isMultibrand
     * @return ScopeContext
     */
    public function setIsMultibrand(bool $isMultibrand): self
    {
        $this->isMultibrand = $isMultibrand;
        return $this;
    }


    /**
     * @param BrandDAO $brand
     * @return ScopeContext
     */
    public function setBrand(BrandDAO $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @param CampaignDAO $campaign
     * @return ScopeContext
     */
    public function setCampaign(CampaignDAO $campaign): self
    {
        $this->campaign = $campaign;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return ScopeContext
     */
    public function setHost(string $host): self
    {
        if(substr($host,-1)!="/"){
            $host.="/";
        }
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getOId(): ?int
    {
        return $this->oId;
    }

    /**
     * @param int $oId
     * @return ScopeContext
     */
    public function setOId(int $oId): self
    {
        $this->oId = $oId;
        return $this;
    }

    /**
     * @return string
     */
    public function getOType(): ?string
    {
        return $this->oType;
    }

    /**
     * @param string $oType
     * @return ScopeContext
     */
    public function setOType(string $oType): self
    {
        if (!in_array($oType, [self::CAMPAIGN_O_TYPE, self::BRAND_O_TYPE])) {
            throw new Exception("Invalid O Type");
        }
        $this->oType = $oType;
        return $this;
    }


    /**
     * @return int
     */
    public function getCampaignId(): int
    {
        if(is_null($this->campaign)){
            throw new Exception("Campaign is not defined" );
        }
        return $this->campaign->getId();
    }

    /**
     * @return string|null
     */
    public function getInstanceName(): ?string
    {
        return $this->instanceName;
    }


    /**
     * @param string $instanceName
     * @return ScopeContext
     */
    public function setInstanceName(string $instanceName): ScopeContext
    {
        $this->instanceName = $instanceName;
        return $this;
    }


    /**
     * @return BrandDAO|null
     */
    public function getBrand(): ?BrandDAO
    {
        return $this->brand;
    }

    /**
     * @return CampaignDAO|null
     */
    public function getCampaign(): ?CampaignDAO
    {
        return $this->campaign;
    }

    /**
     * @return array|null
     */
    public function getBrandsFiltered(): ?array
    {
        return $this->brands_filtered;
    }

    /**
     * @param BrandDAO[] $brands_filtered
     * @return ScopeContext
     */
    public function setBrandsFiltered(array $brands_filtered): self
    {
        $this->brands_filtered = $brands_filtered;
        return $this;
    }

    /**
     * @return  CampaignDAO[]|null
     */
    public function getCampaignsFiltered(): ?array
    {
        return $this->campaigns_filtered;
    }

    /**
     * @param CampaignDAO[] $campaigns_filtered
     * @return ScopeContext
     */
    public function setCampaignsFiltered(array $campaigns_filtered): self
    {
        $this->campaigns_filtered = $campaigns_filtered;
        return $this;
    }

    /**
     * @return ConsumerFieldDAO
     */
    public function getScopeField(): ConsumerFieldDAO
    {
        return $this->scopeField;
    }

    /**
     * @param ConsumerFieldDAO $scopeField
     * @return ScopeContext
     */
    public function setScopeField(ConsumerFieldDAO $scopeField): self
    {
        $this->scopeField = $scopeField;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    /**
     * @param string $organizationId
     * @return $this
     */
    public function setOrganizationId(string $organizationId): ScopeContext
    {
        $this->organizationId = $organizationId;
        return $this;
    }

    /**
     * @return string
     */
    public function getPeriodId(): string
    {
        return $this->periodId;
    }

    /**
     * @param string $periodId
     * @return $this
     */
    public function setPeriodId(string $periodId): ScopeContext
    {
        $this->periodId = $periodId;
        return $this;
    }

    /**
     * @return string
     */
    public function getPeriodStart(): string
    {
        return $this->periodStart;
    }

    /**
     * @param string $periodStart
     * @return $this
     */
    public function setPeriodStart(string $periodStart): ScopeContext
    {
        $this->periodStart = $periodStart;
        return $this;
    }

    /**
     * @return string
     */
    public function getPeriodEnd(): string
    {
        return $this->periodEnd;
    }

    /**
     * @param string $periodEnd
     * @return ScopeContext
     */
    public function setPeriodEnd(string $periodEnd): ScopeContext
    {
        $this->periodEnd = $periodEnd;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize()
    {
        return [
            'host' => $this->host,
            'brand_id' => $this->brand instanceof BrandDAO ? $this->brand->getId() : null,
            'campaign_id' => $this->campaign instanceof CampaignDAO ? $this->campaign->getId() : null,
            'oType' => $this->oType,
            'oId' => $this->oId,
            'instanceName' => $this->instanceName,
            'brands_filtered_ids' => array_map(function($b){
                    return $b instanceof BrandDAO
                        ? $b->getId()
                        : null;
                }, $this->brands_filtered),
            'campaigns_filtered_ids' => array_map(function($c){
                    return $c instanceof CampaignDAO
                        ? $c->getId()
                        : null;
                    }, $this->campaigns_filtered),
            'scopeField_id' => $this->scopeField instanceof ConsumerFieldDAO ? (int) $this->scopeField->getId() : null,
            'isMultibrand' => $this->isMultibrand
        ];
    }

    /**
     * Load instance from Serialized array
     * @param array $array
     * @param ContainerInterface $container
     * @return ScopeContext
     * @throws Exception
     */
    public function loadFromSerialized(array $array, ContainerInterface $container): self
    {
        $this
            ->setHost($array['host'])
            ->setInstanceName($array['instanceName'])
            ->setIsMultibrand($array['isMultibrand'])
            ->setOId($array['oId'])
            ->setOType($array['oType']);

        if (!is_null($array['brand_id'])) {
            ScopeContext::getInstance()->setBrand((new BrandDAO($array['brand_id']))->inject($container->get(DataBase::class))->load());
        }
        if (!is_null($array['campaign_id'])) {
            ScopeContext::getInstance()->setCampaign((new CampaignDAO($array['campaign_id']))->inject($container->get(DataBase::class))->load());
        }
        if (!is_null($array['scopeField_id'])){
            ScopeContext::getInstance()->setScopeField((new ConsumerFieldDAO($array['scopeField_id']))->inject($container->get(DataBase::class))->load());
        }
        if (is_array($array['brands_filtered_ids']) && count($array['brands_filtered_ids']) > 0){
            ScopeContext::getInstance()->setBrandsFiltered(
                array_map(function($id) use ($container){
                    return (new BrandDAO($id))->inject($container->get(DataBase::class))->load();
                }, $array['brands_filtered_ids'])
            );
        }
        if (is_array($array['campaigns_filtered_ids']) && count($array['campaigns_filtered_ids']) > 0){
            ScopeContext::getInstance()->setCampaignsFiltered(
                array_map(function($id) use ($container){
                    return (new CampaignDAO($id))->inject($container->get(DataBase::class))->load();
                }, $array['campaigns_filtered_ids'])
            );
        }
        return $this;
    }
}