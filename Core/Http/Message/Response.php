<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Message;

use Core\Utility\Curl\Cookie;

class Response extends Message
{
    private $statusCode   = 200;
    private $reasonPhrase = 'OK';
    private $cookies      = [];

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $code
     * @param string $reasonPhrase
     * @return $this
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        if ($code === $this->statusCode) {
            return $this;
        }
        $this->statusCode = $code;
        if (empty($reasonPhrase)) {
            $this->reasonPhrase = Status::getReasonPhrase($this->statusCode);
        } else {
            $this->reasonPhrase = $reasonPhrase;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @return $this
     */
    public function withAddedCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
        return $this;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }
}
