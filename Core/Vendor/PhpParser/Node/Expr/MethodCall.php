<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;

class MethodCall extends Expr
{
    /** @var Expr Variable holding object */
    public $var;
    /** @var Expr|string Method name */
    public $name;
    /** @var Arg[] Arguments */
    public $args;

    /**
     * Constructs a function call node.
     *
     * @param Expr $var Variable holding object
     * @param Expr|string $name Method name
     * @param Arg[] $args Arguments
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, $name, array $args = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->var  = $var;
        $this->name = $name;
        $this->args = $args;
    }

    public function getSubNodeNames()
    {
        return ['var', 'name', 'args'];
    }
}
