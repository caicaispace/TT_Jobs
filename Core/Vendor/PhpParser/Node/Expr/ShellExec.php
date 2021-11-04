<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class ShellExec extends Expr
{
    /** @var array Encapsed string array */
    public $parts;

    /**
     * Constructs a shell exec (backtick) node.
     *
     * @param array $parts Encapsed string array
     * @param array $attributes Additional attributes
     */
    public function __construct(array $parts, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->parts = $parts;
    }

    public function getSubNodeNames()
    {
        return ['parts'];
    }
}
