<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar;

use PhpParser\Error;
use PhpParser\Node\Scalar;

class LNumber extends Scalar
{
    /* For use in "kind" attribute */
    public const KIND_BIN = 2;
    public const KIND_OCT = 8;
    public const KIND_DEC = 10;
    public const KIND_HEX = 16;

    /** @var int Number value */
    public $value;

    /**
     * Constructs an integer number scalar node.
     *
     * @param int $value Value of the number
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

    /**
     * Constructs an LNumber node from a string number literal.
     *
     * @param string $str String number literal (decimal, octal, hex or binary)
     * @param array $attributes Additional attributes
     * @param bool $allowInvalidOctal Whether to allow invalid octal numbers (PHP 5)
     *
     * @return LNumber The constructed LNumber, including kind attribute
     */
    public static function fromString($str, array $attributes = [], $allowInvalidOctal = false)
    {
        if ($str[0] !== '0' || $str === '0') {
            $attributes['kind'] = LNumber::KIND_DEC;
            return new LNumber((int) $str, $attributes);
        }

        if ($str[1] === 'x' || $str[1] === 'X') {
            $attributes['kind'] = LNumber::KIND_HEX;
            return new LNumber(hexdec($str), $attributes);
        }

        if ($str[1] === 'b' || $str[1] === 'B') {
            $attributes['kind'] = LNumber::KIND_BIN;
            return new LNumber(bindec($str), $attributes);
        }

        if (! $allowInvalidOctal && strpbrk($str, '89')) {
            throw new Error('Invalid numeric literal', $attributes);
        }

        // use intval instead of octdec to get proper cutting behavior with malformed numbers
        $attributes['kind'] = LNumber::KIND_OCT;
        return new LNumber(intval($str, 8), $attributes);
    }
}
