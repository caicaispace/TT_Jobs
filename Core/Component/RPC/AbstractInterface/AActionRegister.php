<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\AbstractInterface;

use Core\Component\RPC\Common\ActionList;

abstract class AActionRegister
{
    abstract public function register(ActionList $actionList);
}
