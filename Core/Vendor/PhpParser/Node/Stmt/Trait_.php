<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Trait_ extends ClassLike
{
    /**
     * Constructs a trait node.
     *
     * @param string $name Name
     * @param array $subNodes Array of the following optional subnodes:
     *                        'stmts' => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name  = $name;
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : [];
    }

    public function getSubNodeNames()
    {
        return ['name', 'stmts'];
    }
}
