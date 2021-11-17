<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

class DNumber extends Scalar
{
    /** @var float Number value */
    public $value;

    /**
     * Constructs a float number scalar node.
     *
     * @param float $value Value of the number
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
     * @internal
     *
     * Parses a DNUMBER token like PHP would
     *
     * @param string $str A string number
     *
     * @return float The parsed number
     */
    public static function parse($str)
    {
        // if string contains any of .eE just cast it to float
        if (strpbrk($str, '.eE') !== false) {
            return (float) $str;
        }

        // otherwise it's an integer notation that overflowed into a float
        // if it starts with 0 it's one of the special integer notations
        if ($str[0] === '0') {
            // hex
            if ($str[1] === 'x' || $str[1] === 'X') {
                return hexdec($str);
            }

            // bin
            if ($str[1] === 'b' || $str[1] === 'B') {
                return bindec($str);
            }

            // oct
            // substr($str, 0, strcspn($str, '89')) cuts the string at the first invalid digit (8 or 9)
            // so that only the digits before that are used
            return octdec(substr($str, 0, strcspn($str, '89')));
        }

        // dec
        return (float) $str;
    }
}
