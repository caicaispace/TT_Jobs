<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Class_ extends MagicConst
{
    public function getName()
    {
        return '__CLASS__';
    }
}
