<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\Common;

use Core\Component\RPC\AbstractInterface\APackageParser;
use Core\Component\Socket\Client\TcpClient;

class DefaultPackageParser extends APackageParser
{
    public function decode(Package $result, TcpClient $client, $rawData)
    {
        $rawData = pack('H*', base_convert($rawData, 2, 16));
        $js      = json_decode($rawData, 1);
        $js      = is_array($js) ? $js : [];
        $result->arrayToBean($js);
    }

    public function encode(Package $res)
    {
        $data  = $res->__toString();
        $value = unpack('H*', $data);
        return $value[1];
    }
}
