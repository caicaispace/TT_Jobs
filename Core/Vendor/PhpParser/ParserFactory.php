<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser;

class ParserFactory
{
    public const PREFER_PHP7 = 1;
    public const PREFER_PHP5 = 2;
    public const ONLY_PHP7   = 3;
    public const ONLY_PHP5   = 4;

    /**
     * Creates a Parser instance, according to the provided kind.
     *
     * @param int $kind One of ::PREFER_PHP7, ::PREFER_PHP5, ::ONLY_PHP7 or ::ONLY_PHP5
     * @param null|Lexer $lexer Lexer to use. Defaults to emulative lexer when not specified
     * @param array $parserOptions Parser options. See ParserAbstract::__construct() argument
     *
     * @return Parser The parser instance
     */
    public function create($kind, Lexer $lexer = null, array $parserOptions = [])
    {
        if ($lexer === null) {
            $lexer = new Lexer\Emulative();
        }
        switch ($kind) {
            case self::PREFER_PHP7:
                return new Parser\Multiple([
                    new Parser\Php7($lexer, $parserOptions), new Parser\Php5($lexer, $parserOptions),
                ]);
            case self::PREFER_PHP5:
                return new Parser\Multiple([
                    new Parser\Php5($lexer, $parserOptions), new Parser\Php7($lexer, $parserOptions),
                ]);
            case self::ONLY_PHP7:
                return new Parser\Php7($lexer, $parserOptions);
            case self::ONLY_PHP5:
                return new Parser\Php5($lexer, $parserOptions);
            default:
                throw new \LogicException(
                    'Kind must be one of ::PREFER_PHP7, ::PREFER_PHP5, ::ONLY_PHP7 or ::ONLY_PHP5'
                );
        }
    }
}
