<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Else_ extends Node\Stmt
{
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs an else node.
     *
     * @param Node[] $stmts Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(array $stmts = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->stmts = $stmts;
    }

    public function getSubNodeNames()
    {
        return ['stmts'];
    }
}
