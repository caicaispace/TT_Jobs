<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace SuperClosure;

use SuperClosure\Exception\ClosureUnserializationException;

/**
 * Interface for a serializer that is used to serialize Closure objects.
 */
interface SerializerInterface
{
    /**
     * Takes a Closure object, decorates it with a SerializableClosure object,
     * then performs the serialization.
     *
     * @param \Closure $closure closure to serialize
     *
     * @return string serialized closure
     */
    public function serialize(\Closure $closure);

    /**
     * Takes a serialized closure, performs the unserialization, and then
     * extracts and returns a the Closure object.
     *
     * @param string $serialized serialized closure
     *
     * @throws ClosureUnserializationException if unserialization fails
     * @return \Closure unserialized closure
     */
    public function unserialize($serialized);

    /**
     * Retrieves data about a closure including its code, context, and binding.
     *
     * The data returned is dependant on the `ClosureAnalyzer` implementation
     * used and whether the `$forSerialization` parameter is set to true. If
     * `$forSerialization` is true, then only data relevant to serializing the
     * closure is returned.
     *
     * @param \Closure $closure closure to analyze
     * @param bool $forSerialization include only serialization data
     *
     * @return \Closure
     */
    public function getData(\Closure $closure, $forSerialization = false);
}
