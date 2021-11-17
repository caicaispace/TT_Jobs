<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class HaltCompiler extends Stmt
{
    /** @var string Remaining text after halt compiler statement. */
    public $remaining;

    /**
     * Constructs a __halt_compiler node.
     *
     * @param string $remaining remaining text after halt compiler statement
     * @param array $attributes Additional attributes
     */
    public function __construct($remaining, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->remaining = $remaining;
    }

    public function getSubNodeNames()
    {
        return ['remaining'];
    }
}
