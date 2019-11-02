<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/1/23
 * Time: 上午11:17
 */

namespace Core\Utility\Curl;


class Request
{
    protected $cookies = [];
    protected $curlOPt = [
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_AUTOREFERER    => TRUE,
        CURLOPT_USERAGENT      => "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E)",
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_HEADER         => TRUE,
    ];

    function __construct($url = NULL, array $opt = [])
    {
        $this->curlOPt[CURLOPT_URL] = $url;
        if (!empty($opt)) {
            $this->curlOPt = $opt + $this->curlOPt;
        }
    }

    function setPost($data)
    {
        $this->curlOPt[CURLOPT_POST]       = TRUE;
        $this->curlOPt[CURLOPT_POSTFIELDS] = $data;
        return $this;
    }

    function setOpt(array $opt)
    {
        $this->curlOPt = $opt + $this->curlOPt;
        return $this;
    }

    function setUrl($url)
    {
        $this->curlOPt[CURLOPT_URL] = $url;
        return $this;
    }

    function getOpt()
    {
        return $this->curlOPt;
    }

    function addCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }

    function exec(\Closure $callBack = NULL)
    {
        $curl = curl_init();
        $opt  = $this->getOpt();
        if (!empty($this->cookies)) {
            $str = '';
            foreach ($this->cookies as $cookie) {
                $str .= $cookie->__toString();
            }
            $opt[CURLOPT_COOKIE] = $str;
        }
        curl_setopt_array($curl, $opt);
        $result   = curl_exec($curl);
        $response = new Response($result, $curl, $this->cookies);
        if ($callBack) {
            return call_user_func($callBack, $response);
        } else {
            return $response;
        }
    }
}