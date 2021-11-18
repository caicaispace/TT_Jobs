<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component;

use SuperClosure\Serializer;

class SuperClosure
{
    protected $func;
    protected $serializer;
    protected $serializedJson;
    protected $isSerialized = 0;

    public function __construct(\Closure $func)
    {
        $this->func       = $func;
        $this->serializer = new Serializer();
    }

    public function __sleep()
    {
        $this->serializedJson = $this->serializer->serialize($this->func);
        $this->isSerialized   = 1;
        return ['serializedJson', 'isSerialized'];
    }

    public function __wakeup()
    {
        $this->serializer = new Serializer();
        $this->func       = $this->serializer->unserialize($this->serializedJson);
    }

    public function __invoke()
    {
        /*
         * prevent call before serialized
         */
        $args = func_get_args();
        if ($this->isSerialized) {
            $func = $this->serializer->unserialize($this->serializedJson);
        } else {
            $func = $this->func;
        }
        return call_user_func_array($func, $args);
    }
}
