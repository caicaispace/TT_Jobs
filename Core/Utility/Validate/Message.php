<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility\Validate;

class Message
{
    private $error;

    public function __construct(array $error)
    {
        $this->error = $error;
    }

    public function hasError()
    {
        return ! empty($this->error);
    }

    public function getError($filed)
    {
        if (isset($this->error[$filed])) {
            return new Error($this->error[$filed]);
        }
        /*
         * 预防调用错误
         */
        return new Error([]);
    }

    public function all()
    {
        return $this->error;
    }

    public function first()
    {
        if ($this->hasError()) {
            return new Error(array_shift($this->error));
        }
        /*
         * 预防调用错误
         */
        return new Error([]);
    }
}
