<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Return_ extends Node\Stmt
{
    /** @var null|Node\Expr Expression */
    public $expr;

    /**
     * Constructs a return node.
     *
     * @param null|Node\Expr $expr Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr = null, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->expr = $expr;
    }

    public function getSubNodeNames()
    {
        return ['expr'];
    }
}
