<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\AbstractInterface;

use Core\Component\RPC\Common\Package;
use Core\Component\Socket\Client\TcpClient;

abstract class APackageParser
{
    abstract public function decode(Package $result, TcpClient $client, $rawData);

    /*
     * must return string
     */
    abstract public function encode(Package $res);
}
