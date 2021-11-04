<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Socket\AbstractInterface;

use Core\Component\Socket\Common\CommandList;

abstract class ACommandRegister
{
    abstract public function register(CommandList $commandList);
}
