<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility\Validate;

use Core\Component\Spl\SplArray;
use Core\Utility\Judge;

class Func
{
    public static function __callStatic($name, $arguments)
    {
        trigger_error("validate rule {$name} not support");
        return false;
    }
    public static function ACTIVE_URL($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_string($item)) {
                        if (! checkdnsrr(parse_url($item, PHP_URL_HOST))) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (! empty($data) && is_string($data)) {
            return checkdnsrr(parse_url($data, PHP_URL_HOST));
        }
        return false;
    }

    public static function ALPHA($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_string($item)) {
                        if (! preg_match('/^[a-zA-Z]+$/', $item)) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (! empty($data) && is_string($data)) {
            return preg_match('/^[a-zA-Z]+$/', $data);
        }
        return false;
    }

    public static function BETWEEN($column, SplArray $array, array $args)
    {
        $min  = array_shift($args);
        $max  = array_shift($args);
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item) || is_string($item)) {
                        if ($item <= $max && $item >= $min) {
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data) || is_string($data)) {
            if ($data <= $max && $data >= $min) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function BOOLEAN($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (($item == 1) || ($item == 0)) {
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (($data == 1) || ($data == 0)) {
            return true;
        }
        return false;
    }

    public static function DATE($column, SplArray $array, array $args)
    {
        $data   = $array->get($column);
        $format = array_shift($args) ?: 'Y-m-d H:i:s';
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_string($item)) {
                        $unixTime  = strtotime($item);
                        $checkDate = date($format, $unixTime);
                        if ($checkDate != $item) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_string($data)) {
            $unixTime  = strtotime($data);
            $checkDate = date($format, $unixTime);
            if ($checkDate == $data) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function DATE_AFTER($column, SplArray $array, array $args)
    {
        $data          = $array->get($column);
        $after         = array_shift($args);
        $afterUnixTime = empty($after) ? strtotime($after) : time();
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_string($item)) {
                        $unixTime = strtotime($item);
                        if ($unixTime < $afterUnixTime) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_string($data)) {
            $unixTime = strtotime($data);
            if ($unixTime > $afterUnixTime) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function DATE_BEFORE($column, SplArray $array, array $args)
    {
        $data           = $array->get($column);
        $before         = array_shift($args);
        $beroreUnixTime = empty($after) ? strtotime($before) : time();
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_string($item)) {
                        $unixTime = strtotime($item);
                        if ($unixTime > $beroreUnixTime) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_string($data)) {
            $unixTime = strtotime($data);
            if ($unixTime < $beroreUnixTime) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function DIFFERENT($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    foreach ($args as $col) {
                        if ($item === $array->get($col)) {
                            return false;
                        }
                    }
                }
                return true;
            }
            return false;
        }
        foreach ($args as $col) {
            if ($data === $array->get($col)) {
                return false;
            }
        }
        return true;
    }

    public static function FLOAT($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! filter_var($item, FILTER_VALIDATE_FLOAT)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return filter_var($data, FILTER_VALIDATE_FLOAT);
    }

    public static function IN($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! in_array($item, $args)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return in_array($data, $args);
    }

    public static function INTEGER($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! filter_var($item, FILTER_VALIDATE_INT)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return filter_var($data, FILTER_VALIDATE_INT);
    }

    public static function IP($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! filter_var($item, FILTER_VALIDATE_IP)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return filter_var($data, FILTER_VALIDATE_IP);
    }

    public static function ARRAY_($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! is_array($item)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return is_array($data);
    }

    public static function LEN($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        $len  = array_shift($args);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item) || is_string($item)) {
                        if (strlen($item) != $len) {
                            return false;
                        }
                    } elseif (is_array($data)) {
                        if (count($data) != $len) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data) || is_string($data)) {
            if (strlen($data) == $len) {
                return true;
            }
            return false;
        }
        if (is_array($data)) {
            if (count($data) == $len) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function NOT_EMPTY($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (Judge::isEmpty($item)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return ! Judge::isEmpty($data);
    }

    public static function NOT_IN($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (in_array($item, $args)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return ! in_array($data, $args);
    }

    public static function NUMERIC($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! is_numeric($item)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return is_numeric($data);
    }

    public static function MAX($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        $com  = array_shift($args);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item) || is_string($item)) {
                        if ($item > $com) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data) || is_string($data)) {
            if ($data <= $com) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function MAX_LEN($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        $len  = array_shift($args);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item) || is_string($item)) {
                        if (strlen($item) > $len) {
                            return false;
                        }
                    } elseif (is_array($data)) {
                        if (count($data) > $len) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data) || is_string($data)) {
            if (strlen($data) <= $len) {
                return true;
            }
            return false;
        }
        if (is_array($data)) {
            if (count($data) <= $len) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function MIN($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        $com  = array_shift($args);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item) || is_string($item)) {
                        if ($item < $com) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data) || is_string($data)) {
            if ($data >= $com) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function MIN_LEN($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        $len  = array_shift($args);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item) || is_string($item)) {
                        if (strlen($item) < $len) {
                            return false;
                        }
                    } elseif (is_array($data)) {
                        if (count($data) < $len) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data) || is_string($data)) {
            if (strlen($data) >= $len) {
                return true;
            }
            return false;
        }
        if (is_array($data)) {
            if (count($data) >= $len) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function REGEX($column, SplArray $array, array $args)
    {
        $data  = $array->get($column);
        $regex = array_shift($args);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item) || is_string($item)) {
                        if (! preg_match($regex, $item)) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data) || is_string($data)) {
            return preg_match($regex, $data);
        }
        return false;
    }

    public static function REQUIRED($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if ($item === null) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return $data === null ? false : true;
    }

    public static function SAME($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    foreach ($args as $col) {
                        if ($item !== $array->get($col)) {
                            return false;
                        }
                    }
                }
                return true;
            }
            return false;
        }
        foreach ($args as $col) {
            if ($data !== $array->get($col)) {
                return false;
            }
        }
        return true;
    }

    public static function TIMESTAMP($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item)) {
                        if (strtotime(date('d-m-Y H:i:s', $item)) !== (int) $item) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        if (is_numeric($data)) {
            if (strtotime(date('d-m-Y H:i:s', $data)) === (int) $data) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function URL($column, SplArray $array, array $args)
    {
        $data = $array->get($column);
        if (self::isMultiSearch($column)) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! filter_var($item, FILTER_VALIDATE_URL)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return filter_var($data, FILTER_VALIDATE_URL);
    }

    public static function OPTIONAL($column, SplArray $array, array $args)
    {
        return true;
    }

    private static function isMultiSearch($column)
    {
        if (strpos($column, '*') !== false) {
            return true;
        }
        return false;
    }
}
