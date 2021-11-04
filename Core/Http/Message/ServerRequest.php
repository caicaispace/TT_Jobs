<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Message;

class ServerRequest extends Request
{
    private $attributes   = [];
    private $cookieParams = [];
    private $parsedBody;
    private $queryParams = [];
    private $serverParams;
    private $uploadedFiles = [];

    public function __construct(
        $method = 'GET',
        Uri $uri = null,
        array $headers = null,
        Stream $body = null,
        $protocolVersion = '1.1',
        $serverParams = []
    ) {
        $this->serverParams = $serverParams;
        parent::__construct($method, $uri, $headers, $body, $protocolVersion);
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function setServerParam($key, $value)
    {
        $this->serverParams[$key] = $value;
        return true;
    }

    public function getServerParam($key = null)
    {
        if ($key === null) {
            return null;
        }
        if (isset($this->serverParams[$key]) === false) {
            return null;
        }
        return $this->serverParams[$key];
    }

    public function getCookieParams($name = null)
    {
        if ($name === null) {
            return $this->cookieParams;
        }
        if (isset($this->cookieParams[$name])) {
            return $this->cookieParams[$name];
        }
        return null;
    }

    public function withCookieParams(array $cookies)
    {
        $this->cookieParams = $cookies;
        return $this;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getQueryParam($name)
    {
        $data = $this->getQueryParams();
        if (isset($data[$name])) {
            return $data[$name];
        }
        return null;
    }

    public function withQueryParams(array $query)
    {
        $this->queryParams = $query;
        return $this;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function getUploadedFile($name)
    {
        if (isset($this->uploadedFiles[$name])) {
            return $this->uploadedFiles[$name];
        }
        return null;
    }

    /**
     * @param array $uploadedFiles must be array of UploadFile Instance
     * @return ServerRequest
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
        return $this;
    }

    public function getParsedBody($name = null)
    {
        if ($name !== null) {
            if (isset($this->parsedBody[$name])) {
                return $this->parsedBody[$name];
            }
            return null;
        }
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        $this->parsedBody = $data;
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        if (array_key_exists($name, $this->attributes) === false) {
            return $default;
        }
        return $this->attributes[$name];
    }

    public function withAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function withoutAttribute($name)
    {
        if (array_key_exists($name, $this->attributes) === false) {
            return $this;
        }
        unset($this->attributes[$name]);
        return $this;
    }
}
