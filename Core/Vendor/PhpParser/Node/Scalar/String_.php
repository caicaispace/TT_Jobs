<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar;

use PhpParser\Error;
use PhpParser\Node\Scalar;

class String_ extends Scalar
{
    /* For use in "kind" attribute */
    public const KIND_SINGLE_QUOTED = 1;
    public const KIND_DOUBLE_QUOTED = 2;
    public const KIND_HEREDOC       = 3;
    public const KIND_NOWDOC        = 4;

    /** @var string String value */
    public $value;

    protected static $replacements = [
        '\\' => '\\',
        '$'  => '$',
        'n'  => "\n",
        'r'  => "\r",
        't'  => "\t",
        'f'  => "\f",
        'v'  => "\v",
        'e'  => "\x1B",
    ];

    /**
     * Constructs a string scalar node.
     *
     * @param string $value Value of the string
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
     * Parses a string token
     *
     * @param string $str String token content
     * @param bool $parseUnicodeEscape Whether to parse PHP 7 \u escapes
     *
     * @return string The parsed string
     */
    public static function parse($str, $parseUnicodeEscape = true)
    {
        $bLength = 0;
        if ($str[0] === 'b' || $str[0] === 'B') {
            $bLength = 1;
        }

        if ($str[$bLength] === '\'') {
            return str_replace(
                ['\\\\', '\\\''],
                ['\\',   '\''],
                substr($str, $bLength + 1, -1)
            );
        }
        return self::parseEscapeSequences(
            substr($str, $bLength + 1, -1),
            '"',
            $parseUnicodeEscape
        );
    }

    /**
     * @internal
     *
     * Parses escape sequences in strings (all string types apart from single quoted)
     *
     * @param string $str String without quotes
     * @param null|string $quote Quote type
     * @param bool $parseUnicodeEscape Whether to parse PHP 7 \u escapes
     *
     * @return string String with escape sequences parsed
     */
    public static function parseEscapeSequences($str, $quote, $parseUnicodeEscape = true)
    {
        if ($quote !== null) {
            $str = str_replace('\\' . $quote, $quote, $str);
        }

        $extra = '';
        if ($parseUnicodeEscape) {
            $extra = '|u\{([0-9a-fA-F]+)\}';
        }

        return preg_replace_callback(
            '~\\\\([\\\\$nrtfve]|[xX][0-9a-fA-F]{1,2}|[0-7]{1,3}' . $extra . ')~',
            function ($matches) {
                $str = $matches[1];

                if (isset(self::$replacements[$str])) {
                    return self::$replacements[$str];
                }
                if ($str[0] === 'x' || $str[0] === 'X') {
                    return chr(hexdec($str));
                }
                if ($str[0] === 'u') {
                    return self::codePointToUtf8(hexdec($matches[2]));
                }
                return chr(octdec($str));
            },
            $str
        );
    }

    /**
     * @internal
     *
     * Parses a constant doc string
     *
     * @param string $startToken Doc string start token content (<<<SMTHG)
     * @param string $str String token content
     * @param bool $parseUnicodeEscape Whether to parse PHP 7 \u escapes
     *
     * @return string Parsed string
     */
    public static function parseDocString($startToken, $str, $parseUnicodeEscape = true)
    {
        // strip last newline (thanks tokenizer for sticking it into the string!)
        $str = preg_replace('~(\r\n|\n|\r)\z~', '', $str);

        // nowdoc string
        if (strpos($startToken, '\'') !== false) {
            return $str;
        }

        return self::parseEscapeSequences($str, null, $parseUnicodeEscape);
    }

    private static function codePointToUtf8($num)
    {
        if ($num <= 0x7F) {
            return chr($num);
        }
        if ($num <= 0x7FF) {
            return chr(($num >> 6) + 0xC0) . chr(($num & 0x3F) + 0x80);
        }
        if ($num <= 0xFFFF) {
            return chr(($num >> 12) + 0xE0) . chr((($num >> 6) & 0x3F) + 0x80) . chr(($num & 0x3F) + 0x80);
        }
        if ($num <= 0x1FFFFF) {
            return chr(($num >> 18) + 0xF0) . chr((($num >> 12) & 0x3F) + 0x80)
                 . chr((($num >> 6) & 0x3F) + 0x80) . chr(($num & 0x3F) + 0x80);
        }
        throw new Error('Invalid UTF-8 codepoint escape sequence: Codepoint too large');
    }
}
