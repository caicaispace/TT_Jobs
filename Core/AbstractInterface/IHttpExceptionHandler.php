<?php

namespace Core\AbstractInterface;


use Core\Http\Request;
use Core\Http\Response;

interface IHttpExceptionHandler
{
    function handler(\Exception $exception, Request $request, Response $response);
}