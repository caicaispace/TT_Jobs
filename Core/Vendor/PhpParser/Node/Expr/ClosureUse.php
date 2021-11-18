<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class ClosureUse extends Expr
{
    /** @var string Name of variable */
    public $var;
    /** @var bool Whether to use by reference */
    public $byRef;

    /**
     * Constructs a closure use node.
     *
     * @param string $var Name of variable
     * @param bool $byRef Whether to use by reference
     * @param array $attributes Additional attributes
     */
    public function __construct($var, $byRef = false, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->var   = $var;
        $this->byRef = $byRef;
    }

    public function getSubNodeNames()
    {
        return ['var', 'byRef'];
    }
}
