<?php
declare(strict_types=1);

namespace App\Infraestructure\Contexts;

use JsonSerializable;

class UserContext implements JsonSerializable
{
    /** @var UserContext */
    static $_instance = null;

    /** @var int */
    private $user_id;
    /** @var string */
    private $user_email;
    /** @var array<string> */
    private $brand_user_access;
    /** @var array<string> */
    private $campaign_user_access;
    /** @var string|null */
    private $authorizationToken;
    /** @var string|null */
    private $lang;

    /**
     * UserContext constructor.
     */
    private function __construct()
    {
    }


    /**
     * UserContext constructor.
     */
    public static function getInstance(): UserContext
    {
        if (!self::$_instance instanceof UserContext) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return UserContext
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->user_email;
    }

    /**
     * @param string $user_email
     * @return UserContext
     */
    public function setUserEmail(string $user_email): self
    {
        $this->user_email = $user_email;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getBrandUserAccess(): array
    {
        return $this->brand_user_access;
    }

    /**
     * @param array<string> $brand_user_access
     * @return UserContext
     */
    public function setBrandUserAccess(array $brand_user_access): self
    {
        $this->brand_user_access = $brand_user_access;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getCampaignUserAccess(): array
    {
        return $this->campaign_user_access;
    }

    /**
     * @param array<string> $campaign_user_access
     * @return UserContext
     */
    public function setCampaignUserAccess(array $campaign_user_access): self
    {
        $this->campaign_user_access = $campaign_user_access;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLang(): ?string
    {
        return $this->lang;
    }

    /**
     * @param string|null $lang
     * @return UserContext
     */
    public function setLang(?string $lang): self
    {
        $this->lang = $lang;
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
        return[
            'token' => $this->authorizationToken,
            'user_id' => $this->user_id,
            'user_email' => $this->user_email,
            'brand_user_access' => $this->brand_user_access,
            'campaign_user_access' => $this->campaign_user_access
        ];
    }

    /**
     * Load instance from Serialized array
     * @param array<mixed> $array
     * @return UserContext
     */
    public function loadFromSerialized(array $array): self
    {
        $this
            ->setBrandUserAccess($array['brand_user_access'])
            ->setCampaignUserAccess($array['campaign_user_access'])
            ->setUserEmail($array['user_email'])
            ->setUserId($array['user_id'])
            ->setAuthorizationToken($array['token']);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthorizationToken(): ?string
    {
        return $this->authorizationToken;
    }

    /**
     * @param string $token
     * @return UserContext
     */
    public function setAuthorizationToken(string $token): self
    {
        $this->authorizationToken = $token;
        return $this;
    }
}
