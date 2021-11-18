<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility\Validate;

class Error
{
    private $error;

    public function __construct(array $error)
    {
        $this->error = $error;
    }

    public function first()
    {
        return array_shift($this->error);
    }

    public function all()
    {
        return $this->error;
    }
}
