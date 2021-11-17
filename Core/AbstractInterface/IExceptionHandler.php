<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

interface IExceptionHandler
{
    public function handler(\Exception $exception);

    public function display(\Exception $exception);

    public function log(\Exception $exception);
}
