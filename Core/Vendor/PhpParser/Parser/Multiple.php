<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Parser;

use PhpParser\Error;
use PhpParser\ErrorHandler;
use PhpParser\Parser;

class Multiple implements Parser
{
    /** @var Parser[] List of parsers to try, in order of preference */
    private $parsers;

    /**
     * Create a parser which will try multiple parsers in an order of preference.
     *
     * Parsers will be invoked in the order they're provided to the constructor. If one of the
     * parsers runs without throwing, it's output is returned. Otherwise the exception that the
     * first parser generated is thrown.
     *
     * @param Parser[] $parsers
     */
    public function __construct(array $parsers)
    {
        $this->parsers = $parsers;
    }

    public function parse($code, ErrorHandler $errorHandler = null)
    {
        if ($errorHandler === null) {
            $errorHandler = new ErrorHandler\Throwing();
        }

        [$firstStmts, $firstError] = $this->tryParse($this->parsers[0], $errorHandler, $code);
        if ($firstError === null) {
            return $firstStmts;
        }

        for ($i = 1, $c = count($this->parsers); $i < $c; ++$i) {
            [$stmts, $error] = $this->tryParse($this->parsers[$i], $errorHandler, $code);
            if ($error === null) {
                return $stmts;
            }
        }

        throw $firstError;
    }

    private function tryParse(Parser $parser, ErrorHandler $errorHandler, $code)
    {
        $stmts = null;
        $error = null;
        try {
            $stmts = $parser->parse($code, $errorHandler);
        } catch (Error $error) {
        }
        return [$stmts, $error];
    }
}
