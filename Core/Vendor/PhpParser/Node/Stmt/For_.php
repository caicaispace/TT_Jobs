<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class For_ extends Node\Stmt
{
    /** @var Node\Expr[] Init expressions */
    public $init;
    /** @var Node\Expr[] Loop conditions */
    public $cond;
    /** @var Node\Expr[] Loop expressions */
    public $loop;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a for loop node.
     *
     * @param array $subNodes Array of the following optional subnodes:
     *                        'init'  => array(): Init expressions
     *                        'cond'  => array(): Loop conditions
     *                        'loop'  => array(): Loop expressions
     *                        'stmts' => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(array $subNodes = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->init  = isset($subNodes['init']) ? $subNodes['init'] : [];
        $this->cond  = isset($subNodes['cond']) ? $subNodes['cond'] : [];
        $this->loop  = isset($subNodes['loop']) ? $subNodes['loop'] : [];
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : [];
    }

    public function getSubNodeNames()
    {
        return ['init', 'cond', 'loop', 'stmts'];
    }
}
