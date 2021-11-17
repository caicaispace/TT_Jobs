<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Exit_ extends Expr
{
    /* For use in "kind" attribute */
    public const KIND_EXIT = 1;
    public const KIND_DIE  = 2;

    /** @var null|Expr Expression */
    public $expr;

    /**
     * Constructs an exit() node.
     *
     * @param null|Expr $expr Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $expr = null, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->expr = $expr;
    }

    public function getSubNodeNames()
    {
        return ['expr'];
    }
}
