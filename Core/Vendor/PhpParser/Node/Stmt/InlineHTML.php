<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class InlineHTML extends Stmt
{
    /** @var string String */
    public $value;

    /**
     * Constructs an inline HTML node.
     *
     * @param string $value String
     * @param array $attributes Additional attributes
     */
    public function __construct($value, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->value = $value;
    }

    public function getSubNodeNames()
    {
        return ['value'];
    }
}
