<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

class FuncCall extends Expr
{
    /** @var Expr|Node\Name Function name */
    public $name;
    /** @var Node\Arg[] Arguments */
    public $args;

    /**
     * Constructs a function call node.
     *
     * @param Expr|Node\Name $name Function name
     * @param Node\Arg[] $args Arguments
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $args = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name = $name;
        $this->args = $args;
    }

    public function getSubNodeNames()
    {
        return ['name', 'args'];
    }
}
