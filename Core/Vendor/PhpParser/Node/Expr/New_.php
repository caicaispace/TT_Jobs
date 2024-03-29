<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

class New_ extends Expr
{
    /** @var Expr|Node\Name|Node\Stmt\Class_ Class name */
    public $class;
    /** @var Node\Arg[] Arguments */
    public $args;

    /**
     * Constructs a function call node.
     *
     * @param Expr|Node\Name|Node\Stmt\Class_ $class Class name (or class node for anonymous classes)
     * @param Node\Arg[] $args Arguments
     * @param array $attributes Additional attributes
     */
    public function __construct($class, array $args = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->class = $class;
        $this->args  = $args;
    }

    public function getSubNodeNames()
    {
        return ['class', 'args'];
    }
}
