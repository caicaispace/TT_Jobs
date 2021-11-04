<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Socket\AbstractInterface;

use Core\Component\Socket\Common\Command;

abstract class ACommandParser
{
    abstract public function parser(Command $result, AClient $client, $rawData);
}
