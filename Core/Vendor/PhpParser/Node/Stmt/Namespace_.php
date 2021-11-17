<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Namespace_ extends Node\Stmt
{
    /* For use in the "kind" attribute */
    public const KIND_SEMICOLON = 1;
    public const KIND_BRACED    = 2;

    /** @var null|Node\Name Name */
    public $name;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a namespace node.
     *
     * @param null|Node\Name $name Name
     * @param null|Node[] $stmts Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Name $name = null, $stmts = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name  = $name;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames()
    {
        return ['name', 'stmts'];
    }
}
