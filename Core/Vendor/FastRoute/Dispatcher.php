<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace FastRoute;

interface Dispatcher
{
    public const NOT_FOUND          = 0;
    public const FOUND              = 1;
    public const METHOD_NOT_ALLOWED = 2;

    /**
     * Dispatches against the provided HTTP method verb and URI.
     *
     * Returns array with one of the following formats:
     *
     *     [self::NOT_FOUND]
     *     [self::METHOD_NOT_ALLOWED, ['GET', 'OTHER_ALLOWED_METHODS']]
     *     [self::FOUND, $handler, ['varName' => 'value', ...]]
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @return array
     */
    public function dispatch($httpMethod, $uri);
}
