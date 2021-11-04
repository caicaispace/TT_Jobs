<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser;

/**
 * @deprecated
 */
interface Unserializer
{
    /**
     * Unserializes a string in some format into a node tree.
     *
     * @param string $string Serialized string
     *
     * @return mixed Node tree
     */
    public function unserialize($string);
}
