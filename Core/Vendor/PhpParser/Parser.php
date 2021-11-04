<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser;

interface Parser
{
    /**
     * Parses PHP code into a node tree.
     *
     * @param string $code The source code to parse
     * @param null|ErrorHandler $errorHandler error handler to use for lexer/parser errors, defaults
     *                                        to ErrorHandler\Throwing
     *
     * @return null|Node[] array of statements (or null if the 'throwOnError' option is disabled and the parser was
     *                     unable to recover from an error)
     */
    public function parse($code, ErrorHandler $errorHandler = null);
}
