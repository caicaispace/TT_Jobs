<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace SuperClosure\Exception;

/**
 * This exception is thrown when there is a problem unserializing a closure.
 */
class ClosureUnserializationException extends \RuntimeException implements SuperClosureException
{
}
