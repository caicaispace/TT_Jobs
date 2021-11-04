<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Pipe;

abstract class ACommandRegister
{
    abstract public function register(CommandList $commandList);
}
