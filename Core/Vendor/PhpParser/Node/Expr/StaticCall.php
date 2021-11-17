<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

class StaticCall extends Expr
{
    /** @var Expr|Node\Name Class name */
    public $class;
    /** @var Expr|string Method name */
    public $name;
    /** @var Node\Arg[] Arguments */
    public $args;

    /**
     * Constructs a static method call node.
     *
     * @param Expr|Node\Name $class Class name
     * @param Expr|string $name Method name
     * @param Node\Arg[] $args Arguments
     * @param array $attributes Additional attributes
     */
    public function __construct($class, $name, array $args = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->class = $class;
        $this->name  = $name;
        $this->args  = $args;
    }

    public function getSubNodeNames()
    {
        return ['class', 'name', 'args'];
    }
}
