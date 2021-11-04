<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http;

use Core\Http\Message\ServerRequest;
use Core\Http\Message\Stream;
use Core\Http\Message\UploadFile;
use Core\Http\Message\Uri;
use Core\Http\Session\Request as SessionRequest;
use Core\Utility\Validate\Validate;

class Request extends ServerRequest
{
    public const REST_SPECIFICATION = 'REST_SPECIFICATION';

    private static $instance;
    private $swoole_http_request;
    private $session;
    private $specification;

    public function __construct(\swoole_http_request $request)
    {
        $this->swoole_http_request = $request;
        $this->initHeaders();
        $protocol = str_replace('HTTP/', '', $this->swoole_http_request->server['server_protocol']);
        $body     = new Stream($this->swoole_http_request->rawContent());
        $uri      = $this->initUri();
        $files    = $this->initFiles();
        $method   = $this->swoole_http_request->server['request_method'];
        parent::__construct($method, $uri, null, $body, $protocol, $this->swoole_http_request->server);
        $this->withCookieParams($this->initCookie())->withQueryParams($this->initGet())->withParsedBody($this->initPost())->withUploadedFiles($files);
    }

    public static function getInstance(\swoole_http_request $request = null)
    {
        if ($request !== null) {
            self::$instance = new Request($request);
        }
        return self::$instance;
    }

    public function getRequestParam($keyOrKeys = null, $default = null)
    {
        if ($keyOrKeys !== null) {
            if (is_string($keyOrKeys)) {
                if (null === $ret = $this->getParsedBody($keyOrKeys)) {
                    if (null === $ret = $this->getQueryParam($keyOrKeys)) {
                        if ($default !== null) {
                            $ret = $default;
                        }
                    }
                }
                return $ret;
            }
            if (is_array($keyOrKeys)) {
                if (! is_array($default)) {
                    $default = [];
                }
                $data     = $this->getRequestParam();
                $keysNull = array_fill_keys(array_values($keyOrKeys), null);
                if ($keysNull === null) {
                    $keysNull = [];
                }
                $all = array_merge($keysNull, $default, $data);
                return array_intersect_key($all, $keysNull);
            }
            return null;
        }
        return array_merge($this->getParsedBody(), $this->getQueryParams());
    }

    public function requestParamsValidate(Validate $validate)
    {
        return $validate->validate($this->getRequestParam());
    }

    public function setExtendSpecification($specification)
    {
        $this->specification = $specification;
    }

    public function getSwooleRequest()
    {
        return $this->swoole_http_request;
    }

    public function getPostData($name = null)
    {
        if (
            $this->specification === self::REST_SPECIFICATION
            && in_array($this->getMethod(), ['PUT', 'PATCH', 'POST'], true)
        ) {
            $data = json_decode($this->getSwooleRequest()->rawContent(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $this->withParsedBody($data);
        }
        return $this->getParsedBody($name);
    }

    public function session()
    {
        if (! isset($this->session)) {
            $this->session = new SessionRequest();
        }
        return $this->session;
    }

    private function initUri()
    {
        $uri = new Uri();
        $uri->withScheme('http');
        $uri->withPath($this->swoole_http_request->server['path_info']);
        $query = isset($this->swoole_http_request->server['query_string']) ? $this->swoole_http_request->server['query_string'] : '';
        $uri->withQuery($query);
        $host = $this->swoole_http_request->header['host'];
        $host = explode(':', $host);
        $uri->withHost($host[0]);
        $port = isset($host[1]) ? $host[1] : 80;
        $uri->withPort($port);
        return $uri;
    }

    private function initHeaders()
    {
        $headers = $this->swoole_http_request->header;
        foreach ($headers as $header => $val) {
            $this->withAddedHeader($header, $val);
        }
    }

    private function initFiles()
    {
        if (isset($this->swoole_http_request->files)) {
            $normalized = [];
            foreach ($this->swoole_http_request->files as $key => $value) {
                $normalized[$key] = new UploadFile(
                    $value['tmp_name'],
                    (int) $value['size'],
                    (int) $value['error'],
                    $value['name'],
                    $value['type']
                );
            }
            return $normalized;
        }
        return [];
    }

    private function initCookie()
    {
        return isset($this->swoole_http_request->cookie) ? $this->swoole_http_request->cookie : [];
    }

    private function initPost()
    {
        return isset($this->swoole_http_request->post) ? $this->swoole_http_request->post : [];
    }

    private function initGet()
    {
        return isset($this->swoole_http_request->get) ? $this->swoole_http_request->get : [];
    }
}
