<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class Encapsed extends Scalar
{
    /** @var Expr[] list of string parts */
    public $parts;

    /**
     * Constructs an encapsed string node.
     *
     * @param array $parts Encaps list
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
