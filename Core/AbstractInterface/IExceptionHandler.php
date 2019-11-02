<?php

namespace Core\AbstractInterface;


interface IExceptionHandler
{
    function handler(\Exception $exception);

    function display(\Exception $exception);

    function log(\Exception $exception);
}