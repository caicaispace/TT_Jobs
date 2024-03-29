<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Message;

class Message
{
    private $protocolVersion = '1.1';
    private $headers         = [];
    private $body;

    public function __construct(array $headers = null, Stream $body = null, $protocolVersion = '1.1')
    {
        if ($headers != null) {
            $this->headers = $headers;
        }
        if ($body != null) {
            $this->body = $body;
        }
        $this->protocolVersion = $protocolVersion;
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        if ($this->protocolVersion === $version) {
            return $this;
        }
        $this->protocolVersion = $version;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return array_key_exists($name, $this->headers);
    }

    public function getHeader($name)
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
        return [];
    }

    public function getHeaderLine($name)
    {
        if (array_key_exists($name, $this->headers)) {
            return implode('; ', $this->headers[$name]);
        }
        return '';
    }

    public function withHeader($name, $value)
    {
        if (! is_array($value)) {
            $value = [$value];
        }
        if (isset($this->headers[$name]) && $this->headers[$name] === $value) {
            return $this;
        }
        $this->headers[$name] = $value;
        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        if (! is_array($value)) {
            $value = [$value];
        }
        if (isset($this->headers[$name])) {
            $this->headers[$name] = array_merge($this->headers[$name], $value);
        } else {
            $this->headers[$name] = $value;
        }
        return $this;
    }

    public function withoutHeader($name)
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
            return $this;
        }
        return $this;
    }

    public function getBody()
    {
        if ($this->body == null) {
            $this->body = new Stream('');
        }
        return $this->body;
    }

    public function withBody(Stream $body)
    {
        $this->body = $body;
        return $this;
    }
}
