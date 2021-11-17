<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Spl;

class SplEnum
{
    public const __default = null;
    private $selfEnum;

    final public function __construct($enumVal)
    {
        $list           = static::enumList();
        $key            = array_search($enumVal, $list, true);
        $this->selfEnum = $key ? $key : '__default';
    }

    final public function __toString()
    {
        $list = static::enumList();
        $data = $list[$this->selfEnum];
        if (is_string($data)) {
            return $data;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    final public static function __callStatic($name, $arguments)
    {
        $list = static::enumList();
        $val  = isset($list[$name]) ? $list[$name] : null;
        return new static($val);
    }

    final public function equals($val)
    {
        $list = static::enumList();
        return $list[$this->selfEnum] === $val ? true : false;
    }

    public static function inEnum($enumVal)
    {
        $list = static::enumList();
        $key  = array_search($enumVal, $list, true);
        return $key ? $key : false;
    }

    final public static function enumList()
    {
        $ref = new \ReflectionClass(static::class);
        return $ref->getConstants();
    }
}
