<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class ElseIf_ extends Node\Stmt
{
    /** @var Node\Expr Condition */
    public $cond;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs an elseif node.
     *
     * @param Node\Expr $cond Condition
     * @param Node[] $stmts Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $stmts = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->cond  = $cond;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames()
    {
        return ['cond', 'stmts'];
    }
}
