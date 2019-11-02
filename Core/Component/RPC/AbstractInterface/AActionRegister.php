<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/10/23
 * Time: 下午4:21
 */

namespace Core\Component\RPC\AbstractInterface;


use Core\Component\RPC\Common\ActionList;

abstract class AActionRegister
{
    abstract function register(ActionList $actionList);
}