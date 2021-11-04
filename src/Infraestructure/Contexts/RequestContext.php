<?php


namespace App\Infraestructure\Contexts;


class RequestContext
{
    private $url;
    private $method;
    private $body;
    private $headers;

    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): RequestContext
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return RequestContext
     */
    public function setUrl($url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     * @return RequestContext
     */
    public function setMethod($method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return RequestContext
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     * @return RequestContext
     */
    public function setHeaders($headers): self
    {
        $this->headers=[];
        foreach($headers as $key=>$value){
            $this->headers[$key]=current($value);
        }
        return $this;
    }

}