<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\AbstractInterface;

use Core\Component\RPC\Common\ActionList;

abstract class AActionRegister
{
    abstract public function register(ActionList $actionList);
}
