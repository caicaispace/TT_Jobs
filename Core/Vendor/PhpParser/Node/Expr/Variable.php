<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Variable extends Expr
{
    /** @var Expr|string Name */
    public $name;

    /**
     * Constructs a variable node.
     *
     * @param Expr|string $name Name
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name = $name;
    }

    public function getSubNodeNames()
    {
        return ['name'];
    }
}
