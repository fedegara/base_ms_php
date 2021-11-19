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

class ScopeContext
{
    public const CAMPAIGN_O_TYPE = 'campaign';
    public const BRAND_O_TYPE = 'brand';


    /**
     * @var ScopeContext
     */
    private static $priv_instance;

    /** @var string */
    private $host;
    /** @var string [CAMPAIGN|BRAND] */
    private $oType = null;
    /** @var integer */
    private $oId = null;
    /** @var string */
    private $instanceName = null;

    /**
     * @var integer
     */
    private $entityId;
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
        if (substr($host, -1) != "/") {
            $host .= "/";
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
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param int $entityId
     * @return ScopeContext
     */
    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
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
}
