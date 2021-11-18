<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class List_ extends Expr
{
    /** @var ArrayItem[] List of items to assign to */
    public $items;

    /**
     * Constructs a list() destructuring node.
     *
     * @param ArrayItem[] $items List of items to assign to
     * @param array $attributes Additional attributes
     */
    public function __construct(array $items, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->items = $items;
    }

    public function getSubNodeNames()
    {
        return ['items'];
    }
}
