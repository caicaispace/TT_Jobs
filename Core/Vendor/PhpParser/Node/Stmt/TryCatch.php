<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class TryCatch extends Node\Stmt
{
    /** @var Node[] Statements */
    public $stmts;
    /** @var Catch_[] Catches */
    public $catches;
    /** @var null|Finally_ Optional finally node */
    public $finally;

    /**
     * Constructs a try catch node.
     *
     * @param Node[] $stmts Statements
     * @param Catch_[] $catches Catches
     * @param null|Finally_ $finally Optionaly finally node
     * @param null|array $attributes Additional attributes
     */
    public function __construct(array $stmts, array $catches, Finally_ $finally = null, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->stmts   = $stmts;
        $this->catches = $catches;
        $this->finally = $finally;
    }

    public function getSubNodeNames()
    {
        return ['stmts', 'catches', 'finally'];
    }
}
