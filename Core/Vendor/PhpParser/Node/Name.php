<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Name extends NodeAbstract
{
    /**
     * @var string[] Parts of the name
     */
    public $parts;

    /**
     * Constructs a name node.
     *
     * @param array|self|string $name Name as string, part array or Name instance (copy ctor)
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->parts = self::prepareName($name);
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with the
     * namespace separator.
     *
     * @return string String representation
     */
    public function __toString()
    {
        return implode('\\', $this->parts);
    }

    public function getSubNodeNames()
    {
        return ['parts'];
    }

    /**
     * Gets the first part of the name, i.e. everything before the first namespace separator.
     *
     * @return string First part of the name
     */
    public function getFirst()
    {
        return $this->parts[0];
    }

    /**
     * Gets the last part of the name, i.e. everything after the last namespace separator.
     *
     * @return string Last part of the name
     */
    public function getLast()
    {
        return $this->parts[count($this->parts) - 1];
    }

    /**
     * Checks whether the name is unqualified. (E.g. Name).
     *
     * @return bool Whether the name is unqualified
     */
    public function isUnqualified()
    {
        return count($this->parts) == 1;
    }

    /**
     * Checks whether the name is qualified. (E.g. Name\Name).
     *
     * @return bool Whether the name is qualified
     */
    public function isQualified()
    {
        return 1 < count($this->parts);
    }

    /**
     * Checks whether the name is fully qualified. (E.g. \Name).
     *
     * @return bool Whether the name is fully qualified
     */
    public function isFullyQualified()
    {
        return false;
    }

    /**
     * Checks whether the name is explicitly relative to the current namespace. (E.g. namespace\Name).
     *
     * @return bool Whether the name is relative
     */
    public function isRelative()
    {
        return false;
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with the
     * namespace separator.
     *
     * @return string String representation
     */
    public function toString()
    {
        return implode('\\', $this->parts);
    }

    /**
     * Gets a slice of a name (similar to array_slice).
     *
     * This method returns a new instance of the same type as the original and with the same
     * attributes.
     *
     * If the slice is empty, null is returned. The null value will be correctly handled in
     * concatenations using concat().
     *
     * Offset and length have the same meaning as in array_slice().
     *
     * @param int $offset Offset to start the slice at (may be negative)
     * @param null|int $length Length of the slice (may be negative)
     *
     * @return null|static Sliced name
     */
    public function slice($offset, $length = null)
    {
        $numParts = count($this->parts);

        $realOffset = $offset < 0 ? $offset + $numParts : $offset;
        if ($realOffset < 0 || $realOffset > $numParts) {
            throw new \OutOfBoundsException(sprintf('Offset %d is out of bounds', $offset));
        }

        if ($length === null) {
            $realLength = $numParts - $realOffset;
        } else {
            $realLength = $length < 0 ? $length + $numParts - $realOffset : $length;
            if ($realLength < 0 || $realLength > $numParts) {
                throw new \OutOfBoundsException(sprintf('Length %d is out of bounds', $length));
            }
        }

        if ($realLength === 0) {
            // Empty slice is represented as null
            return null;
        }

        return new static(array_slice($this->parts, $realOffset, $realLength), $this->attributes);
    }

    /**
     * Concatenate two names, yielding a new Name instance.
     *
     * The type of the generated instance depends on which class this method is called on, for
     * example Name\FullyQualified::concat() will yield a Name\FullyQualified instance.
     *
     * If one of the arguments is null, a new instance of the other name will be returned. If both
     * arguments are null, null will be returned. As such, writing
     *     Name::concat($namespace, $shortName)
     * where $namespace is a Name node or null will work as expected.
     *
     * @param null|array|self|string $name1 The first name
     * @param null|array|self|string $name2 The second name
     * @param array $attributes Attributes to assign to concatenated name
     *
     * @return null|static Concatenated name
     */
    public static function concat($name1, $name2, array $attributes = [])
    {
        if ($name1 === null && $name2 === null) {
            return null;
        }
        if ($name1 === null) {
            return new static(self::prepareName($name2), $attributes);
        }
        if ($name2 === null) {
            return new static(self::prepareName($name1), $attributes);
        }
        return new static(
                array_merge(self::prepareName($name1), self::prepareName($name2)), $attributes
            );
    }

    /**
     * Prepares a (string, array or Name node) name for use in name changing methods by converting
     * it to an array.
     *
     * @param array|self|string $name Name to prepare
     *
     * @return array Prepared name
     */
    private static function prepareName($name)
    {
        if (\is_string($name)) {
            return explode('\\', $name);
        }
        if (\is_array($name)) {
            return $name;
        }
        if ($name instanceof self) {
            return $name->parts;
        }

        throw new \InvalidArgumentException(
            'Expected string, array of parts or Name instance'
        );
    }
}
