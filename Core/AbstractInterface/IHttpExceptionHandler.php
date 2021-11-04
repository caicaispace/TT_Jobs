<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

use Core\Http\Request;
use Core\Http\Response;

interface IHttpExceptionHandler
{
    public function handler(\Exception $exception, Request $request, Response $response);
}
