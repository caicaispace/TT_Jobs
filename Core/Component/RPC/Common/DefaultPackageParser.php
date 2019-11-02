<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/10/23
 * Time: 下午4:15
 */

namespace Core\Component\RPC\Common;


use Core\Component\RPC\AbstractInterface\APackageParser;
use Core\Component\Socket\Client\TcpClient;

class DefaultPackageParser extends APackageParser
{
    function decode(Package $result, TcpClient $client, $rawData)
    {
        $rawData = pack('H*', base_convert($rawData, 2, 16));
        $js = json_decode($rawData,1);
        $js = is_array($js) ? $js :[];
        $result->arrayToBean($js);
    }

    function encode(Package $res)
    {
        $data = $res->__toString();
        $value = unpack('H*', $data);
        return $value[1];
    }

}