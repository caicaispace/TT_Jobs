<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Pipe;

use Core\Swoole\Server;

class Send
{
    public static function send(Message $message, $workerId)
    {
        return Server::getInstance()->getServer()->sendMessage($message->__toString(), $workerId);
    }
}
