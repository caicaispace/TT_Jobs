<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class UseUse extends Node\Stmt
{
    /** @var int One of the Stmt\Use_::TYPE_* constants. Will only differ from TYPE_UNKNOWN for mixed group uses */
    public $type;
    /** @var Node\Name Namespace, class, function or constant to alias */
    public $name;
    /** @var string Alias */
    public $alias;

    /**
     * Constructs an alias (use) node.
     *
     * @param Node\Name $name Namespace/Class to alias
     * @param null|string $alias Alias
     * @param int $type Type of the use element (for mixed group use declarations only)
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Name $name, $alias = null, $type = Use_::TYPE_UNKNOWN, array $attributes = [])
    {
        if ($alias === null) {
            $alias = $name->getLast();
        }

        parent::__construct($attributes);
        $this->type  = $type;
        $this->name  = $name;
        $this->alias = $alias;
    }

    public function getSubNodeNames()
    {
        return ['type', 'name', 'alias'];
    }
}
