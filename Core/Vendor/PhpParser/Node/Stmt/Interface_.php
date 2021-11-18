<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Interface_ extends ClassLike
{
    /** @var Node\Name[] Extended interfaces */
    public $extends;

    /**
     * Constructs a class node.
     *
     * @param string $name Name
     * @param array $subNodes Array of the following optional subnodes:
     *                        'extends' => array(): Name of extended interfaces
     *                        'stmts'   => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name    = $name;
        $this->extends = isset($subNodes['extends']) ? $subNodes['extends'] : [];
        $this->stmts   = isset($subNodes['stmts']) ? $subNodes['stmts'] : [];
    }

    public function getSubNodeNames()
    {
        return ['name', 'extends', 'stmts'];
    }
}
