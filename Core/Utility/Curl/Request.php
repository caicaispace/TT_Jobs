<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility\Curl;

class Request
{
    protected $cookies = [];
    protected $curlOPt = [
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E)',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HEADER         => true,
    ];

    public function __construct($url = null, array $opt = [])
    {
        $this->curlOPt[CURLOPT_URL] = $url;
        if (! empty($opt)) {
            $this->curlOPt = $opt + $this->curlOPt;
        }
    }

    public function setPost($data)
    {
        $this->curlOPt[CURLOPT_POST]       = true;
        $this->curlOPt[CURLOPT_POSTFIELDS] = $data;
        return $this;
    }

    public function setOpt(array $opt)
    {
        $this->curlOPt = $opt + $this->curlOPt;
        return $this;
    }

    public function setUrl($url)
    {
        $this->curlOPt[CURLOPT_URL] = $url;
        return $this;
    }

    public function getOpt()
    {
        return $this->curlOPt;
    }

    public function addCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }

    public function exec(\Closure $callBack = null)
    {
        $curl = curl_init();
        $opt  = $this->getOpt();
        if (! empty($this->cookies)) {
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
        }
        return $response;
    }
}
