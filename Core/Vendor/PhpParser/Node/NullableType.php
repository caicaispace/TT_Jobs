<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class NullableType extends NodeAbstract
{
    /** @var Name|string Type */
    public $type;

    /**
     * Constructs a nullable type (wrapping another type).
     *
     * @param Name|string $type Type
     * @param array $attributes Additional attributes
     */
    public function __construct($type, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->type = $type;
    }

    public function getSubNodeNames()
    {
        return ['type'];
    }
}
