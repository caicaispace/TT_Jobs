<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility;

class Judge
{
    /*
     * 说明  了防止新人出现
     *
     * if(empty(0)){}
     *
     * if(md5("400035577431") == md5("mcfog_42r6i8"))
     *
     * 的问题
     */
    public static function isEqual($val, $val2)
    {
        if ($val == $val2) {
            return true;
        }
        return false;
    }

    public static function isStrictEqual($val, $val2)
    {
        if ($val === $val2) {
            return true;
        }
        return false;
    }

    public static function isNull($val)
    {
        return is_null($val);
    }

    /*
     * 注意  0不为空，为解决  php内0为空问题
     */
    public static function isEmpty($val)
    {
        if ($val === 0 || $val === '0') {
            return false;
        }
        return empty($val);
    }

    /*
     * 接受  0，1 true，false
     */
    public static function boolean($val, $strict = false)
    {
        if ($strict) {
            return is_bool($val);
        }
        if (is_bool($val) || $val == 0 || $val == 1) {
            return true;
        }
        return false;
    }

    public static function isTrue($val, $strict = false)
    {
        if ($strict) {
            if ($val === true) {
                return true;
            }
            return false;
        }
        if ($val == 1) {
            return true;
        }
        return false;
    }

    public static function isFalse($val, $strict = false)
    {
        if ($strict) {
            if ($val === false) {
                return true;
            }
            return false;
        }
        if ($val == 0) {
            return true;
        }
        return false;
    }
}
