<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility;

class Random
{
    public static function randStr($length)
    {
        return substr(str_shuffle('abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789'), 0, $length);
    }

    public static function randNumStr($length)
    {
        $chars    = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $password = '';
        while (strlen($password) < $length) {
            $password .= $chars[rand(0, 9)];
        }
        return $password;
    }
}
