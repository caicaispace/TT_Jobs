<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/14
 * Time: 下午12:28
 */

namespace Core\Http\Message;


use Core\Utility\Curl\Cookie;

class Response extends Message
{
    private $statusCode = 200;
    private $reasonPhrase = 'OK';
    private $cookies = [];

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param        $code
     * @param string $reasonPhrase
     * @return $this
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        if ($code === $this->statusCode) {
            return $this;
        } else {
            $this->statusCode = $code;
            if (empty($reasonPhrase)) {
                $this->reasonPhrase = Status::getReasonPhrase($this->statusCode);
            } else {
                $this->reasonPhrase = $reasonPhrase;
            }
            return $this;
        }
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @param Cookie $cookie
     * @return $this
     */
    function withAddedCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
        return $this;
    }

    /**
     * @return array
     */
    function getCookies()
    {
        return $this->cookies;
    }
}