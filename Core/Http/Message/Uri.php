<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Message;

class Uri
{
    private $host;
    private $userInfo;
    private $port = 80;
    private $path;
    private $query;
    private $fragment;
    private $scheme;

    public function __construct($url = '')
    {
        if ($url !== '') {
            $parts          = parse_url($url);
            $this->scheme   = isset($parts['scheme']) ? $parts['scheme'] : '';
            $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
            $this->host     = isset($parts['host']) ? $parts['host'] : '';
            $this->port     = isset($parts['port']) ? $parts['port'] : 80;
            $this->path     = isset($parts['path']) ? $parts['path'] : '';
            $this->query    = isset($parts['query']) ? $parts['query'] : '';
            $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
        }
    }

    public function __toString()
    {
        $uri = '';
        // weak type checks to also accept null until we can add scalar type hints
        if ($this->scheme != '') {
            $uri .= $this->scheme . ':';
        }
        if ($this->getAuthority() != '' || $this->scheme === 'file') {
            $uri .= '//' . $this->getAuthority();
        }
        $uri .= $this->path;
        if ($this->query != '') {
            $uri .= '?' . $this->query;
        }
        if ($this->fragment != '') {
            $uri .= '#' . $this->fragment;
        }
        return $uri;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        if ($this->scheme === $scheme) {
            return $this;
        }
        $this->scheme = $scheme;
        return $this;
    }

    public function withUserInfo($user, $password = null)
    {
        $info = $user;
        if ($password != '') {
            $info .= ':' . $password;
        }
        if ($this->userInfo === $info) {
            return $this;
        }
        $this->userInfo = $info;
        return $this;
    }

    public function withHost($host)
    {
        $host = strtolower($host);
        if ($this->host === $host) {
            return $this;
        }
        $this->host = $host;
        return $this;
    }

    public function withPort($port)
    {
        if ($this->port === $port) {
            return $this;
        }
        $this->port = $port;
        return $this;
    }

    public function withPath($path)
    {
        if ($this->path === $path) {
            return $this;
        }
        $this->path = $path;
        return $this;
    }

    public function withQuery($query)
    {
        if ($this->query === $query) {
            return $this;
        }
        $this->query = $query;
        return $this;
    }

    public function withFragment($fragment)
    {
        if ($this->fragment === $fragment) {
            return $this;
        }
        $this->fragment = $fragment;
        return $this;
    }
}
