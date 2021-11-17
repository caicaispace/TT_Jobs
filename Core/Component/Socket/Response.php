<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Socket;

use Core\Component\Socket\AbstractInterface\AClient;
use Core\Component\Socket\Client\TcpClient;
use Core\Component\Socket\Client\UdpClient;
use Core\Swoole\Server;

class Response
{
    public static function response(AClient $client, $data, $eof = '')
    {
        if ($client instanceof TcpClient) {
            if ($client->getClientType() == Type::WEB_SOCKET) {
                return Server::getInstance()->getServer()->push($client->getFd(), $data);
            }
            return Server::getInstance()->getServer()->send($client->getFd(), $data . $eof, $client->getReactorId());
        }
        if ($client instanceof UdpClient) {
            return Server::getInstance()->getServer()->sendto($client->getAddress(), $client->getPort(), $data . $eof);
        }
        trigger_error('client is not validate');
        return false;
    }
}
