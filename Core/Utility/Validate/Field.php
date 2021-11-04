<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility\Validate;

class Field
{
    protected $currentRule;
    protected $rule = [];
    protected $msg  = [
        '__default__' => null,
    ];

    public function withMsg($msg)
    {
        if (isset($this->currentRule)) {
            $this->msg[$this->currentRule] = $msg;
            $this->currentRule             = null;
        } else {
            $this->msg['__default__'] = $msg;
        }
        return $this;
    }

    public function withRule($rule, ...$arg)
    {
        $this->currentRule = $rule;
        $this->rule[$rule] = $arg;
        return $this;
    }

    public function getRule()
    {
        return $this->rule;
    }

    public function getMsg()
    {
        return $this->msg;
    }
}
