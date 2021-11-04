<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt\TraitUseAdaptation;

use PhpParser\Node;

class Precedence extends Node\Stmt\TraitUseAdaptation
{
    /** @var Node\Name[] Overwritten traits */
    public $insteadof;

    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param Node\Name $trait Trait name
     * @param string $method Method name
     * @param Node\Name[] $insteadof Overwritten traits
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Name $trait, $method, array $insteadof, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->trait     = $trait;
        $this->method    = $method;
        $this->insteadof = $insteadof;
    }

    public function getSubNodeNames()
    {
        return ['trait', 'method', 'insteadof'];
    }
}
