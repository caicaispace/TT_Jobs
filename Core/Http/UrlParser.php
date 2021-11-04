<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http;

class UrlParser
{
    public static function pathInfo($path = null)
    {
        if ($path == null) {
            $path = Request::getInstance()->getUri()->getPath();
        }
        $basePath = dirname($path);
        $info     = pathinfo($path);
        if ($info['filename'] != 'index') {
            if ($basePath == '/') {
                $basePath = $basePath . $info['filename'];
            } else {
                $basePath = $basePath . '/' . $info['filename'];
            }
        }
        return $basePath;
    }

    public static function generateURL($controllerClass, $action = 'index', $query = [])
    {
        $controllerClass = substr($controllerClass, 14);
        $controllerClass = explode('\\', $controllerClass);
        $path            = implode('/', $controllerClass);
        if ($action == 'index') {
            $path = $path . '/index.html';
        } else {
            $path = $path . "/{$action}/index.html";
        }
        if (! empty($query)) {
            return $path . '?' . http_build_cookie($query);
        }
        return $path;
    }
}
