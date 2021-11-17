<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class Instanceof_ extends Expr
{
    /** @var Expr Expression */
    public $expr;
    /** @var Expr|Name Class name */
    public $class;

    /**
     * Constructs an instanceof check node.
     *
     * @param Expr $expr Expression
     * @param Expr|Name $class Class name
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $expr, $class, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->expr  = $expr;
        $this->class = $class;
    }

    public function getSubNodeNames()
    {
        return ['expr', 'class'];
    }
}
