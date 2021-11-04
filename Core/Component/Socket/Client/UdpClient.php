<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Socket\Client;

use Core\Component\Socket\AbstractInterface\AClient;
use Core\Component\Socket\Type;

class UdpClient extends AClient
{
    protected $server_socket;
    protected $server_port;
    protected $address;
    protected $port;

    /**
     * @return mixed
     */
    public function getServerSocket()
    {
        return $this->server_socket;
    }

    /**
     * @param mixed $server_socket
     */
    public function setServerSocket($server_socket)
    {
        $this->server_socket = $server_socket;
    }

    /**
     * @return mixed
     */
    public function getServerPort()
    {
        return $this->server_port;
    }

    /**
     * @param mixed $server_port
     */
    public function setServerPort($server_port)
    {
        $this->server_port = $server_port;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    public function initialize()
    {
        $this->clientType = Type::UDP;
    }
}
