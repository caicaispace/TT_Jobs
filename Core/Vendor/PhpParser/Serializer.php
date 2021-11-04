<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser;

/**
 * @deprecated
 */
interface Serializer
{
    /**
     * Serializes statements into some string format.
     *
     * @param array $nodes Statements
     *
     * @return string Serialized string
     */
    public function serialize(array $nodes);
}
