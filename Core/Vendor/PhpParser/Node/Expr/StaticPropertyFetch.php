<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class StaticPropertyFetch extends Expr
{
    /** @var Expr|Name Class name */
    public $class;
    /** @var Expr|string Property name */
    public $name;

    /**
     * Constructs a static property fetch node.
     *
     * @param Expr|Name $class Class name
     * @param Expr|string $name Property name
     * @param array $attributes Additional attributes
     */
    public function __construct($class, $name, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->class = $class;
        $this->name  = $name;
    }

    public function getSubNodeNames()
    {
        return ['class', 'name'];
    }
}
