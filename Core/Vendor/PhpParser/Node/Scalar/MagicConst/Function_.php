<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Function_ extends MagicConst
{
    public function getName()
    {
        return '__FUNCTION__';
    }
}
