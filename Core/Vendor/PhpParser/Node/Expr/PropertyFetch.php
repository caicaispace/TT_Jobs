<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class PropertyFetch extends Expr
{
    /** @var Expr Variable holding object */
    public $var;
    /** @var Expr|string Property name */
    public $name;

    /**
     * Constructs a function call node.
     *
     * @param Expr $var Variable holding object
     * @param Expr|string $name Property name
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, $name, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->var  = $var;
        $this->name = $name;
    }

    public function getSubNodeNames()
    {
        return ['var', 'name'];
    }
}
