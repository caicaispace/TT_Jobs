<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace SuperClosure\Exception;

/**
 * This exception is thrown when there is a problem serializing a closure.
 */
class ClosureSerializationException extends \RuntimeException implements SuperClosureException
{
}
