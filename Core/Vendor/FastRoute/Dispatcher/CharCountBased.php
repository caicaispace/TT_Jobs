<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace FastRoute\Dispatcher;

class CharCountBased extends RegexBasedAbstract
{
    public function __construct($data)
    {
        [$this->staticRouteMap, $this->variableRouteData] = $data;
    }

    protected function dispatchVariableRoute($routeData, $uri)
    {
        foreach ($routeData as $data) {
            if (! preg_match($data['regex'], $uri . $data['suffix'], $matches)) {
                continue;
            }

            [$handler, $varNames] = $data['routeMap'][end($matches)];

            $vars = [];
            $i    = 0;
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[++$i];
            }
            return [self::FOUND, $handler, $vars];
        }

        return [self::NOT_FOUND];
    }
}
