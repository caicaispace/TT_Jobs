<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Foreach_ extends Node\Stmt
{
    /** @var Node\Expr Expression to iterate */
    public $expr;
    /** @var null|Node\Expr Variable to assign key to */
    public $keyVar;
    /** @var bool Whether to assign value by reference */
    public $byRef;
    /** @var Node\Expr Variable to assign value to */
    public $valueVar;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a foreach node.
     *
     * @param Node\Expr $expr Expression to iterate
     * @param Node\Expr $valueVar Variable to assign value to
     * @param array $subNodes Array of the following optional subnodes:
     *                        'keyVar' => null   : Variable to assign key to
     *                        'byRef'  => false  : Whether to assign value by reference
     *                        'stmts'  => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr, Node\Expr $valueVar, array $subNodes = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->expr     = $expr;
        $this->keyVar   = isset($subNodes['keyVar']) ? $subNodes['keyVar'] : null;
        $this->byRef    = isset($subNodes['byRef']) ? $subNodes['byRef'] : false;
        $this->valueVar = $valueVar;
        $this->stmts    = isset($subNodes['stmts']) ? $subNodes['stmts'] : [];
    }

    public function getSubNodeNames()
    {
        return ['expr', 'keyVar', 'byRef', 'valueVar', 'stmts'];
    }
}
