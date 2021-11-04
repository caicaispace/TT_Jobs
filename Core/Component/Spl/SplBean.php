<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Spl;

use Core\Utility\Judge;

abstract class SplBean implements \JsonSerializable
{
    public const FILTER_TYPE_NOT_NULL  = 1;
    public const FILTER_TYPE_NOT_EMPTY = 2;
    private $__varList                 = [];

    final public function __construct($beanArray = [])
    {
        $this->__varList = $this->allVarKeys();
        $this->arrayToBean($beanArray);
        $this->initialize();
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    final public function jsonSerialize()
    {
        $data = [];
        foreach ($this->__varList as $var) {
            $data[$var] = $this->{$var};
        }
        return $data;
    }

    public function toArray($filterType = false, array $columns = null)
    {
        if ($columns) {
            $data = $this->jsonSerialize();
            $ret  = array_intersect_key($data, array_flip($columns));
        } else {
            $ret = $this->jsonSerialize();
        }
        if ($filterType === self::FILTER_TYPE_NOT_NULL) {
            return array_filter($ret, function ($val) {
                return ! is_null($val);
            });
        } elseif ($filterType === self::FILTER_TYPE_NOT_EMPTY) {
            return array_filter($ret, function ($val) {
                //0不为空
                return ! Judge::isEmpty($val);
            });
        } else {
            return $ret;
        }
    }

    public function arrayToBean(array $data)
    {
        $data = array_intersect_key($data, array_flip($this->__varList));
        foreach ($data as $var => $val) {
            $this->{$var} = $val;
        }
        return $this;
    }

    final protected function setDefault(&$property, $val)
    {
        $property = $val;
        return $this;
    }

    abstract protected function initialize();

    private function allVarKeys()
    {
        $data = get_class_vars(static::class);
        unset($data['__varList']);
        return array_keys($data);
    }
}
