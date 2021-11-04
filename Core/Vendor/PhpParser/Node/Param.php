<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Param extends NodeAbstract
{
    /** @var null|Name|NullableType|string Typehint */
    public $type;
    /** @var bool Whether parameter is passed by reference */
    public $byRef;
    /** @var bool Whether this is a variadic argument */
    public $variadic;
    /** @var string Name */
    public $name;
    /** @var null|Expr Default value */
    public $default;

    /**
     * Constructs a parameter node.
     *
     * @param string $name Name
     * @param null|Expr $default Default value
     * @param null|Name|NullableType|string $type Typehint
     * @param bool $byRef Whether is passed by reference
     * @param bool $variadic Whether this is a variadic argument
     * @param array $attributes Additional attributes
     */
    public function __construct($name, Expr $default = null, $type = null, $byRef = false, $variadic = false, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->type     = $type;
        $this->byRef    = $byRef;
        $this->variadic = $variadic;
        $this->name     = $name;
        $this->default  = $default;
    }

    public function getSubNodeNames()
    {
        return ['type', 'byRef', 'variadic', 'name', 'default'];
    }
}
