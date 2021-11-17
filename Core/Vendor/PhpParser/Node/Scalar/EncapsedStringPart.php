<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class EncapsedStringPart extends Scalar
{
    /** @var string String value */
    public $value;

    /**
     * Constructs a node representing a string part of an encapsed string.
     *
     * @param string $value String value
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
