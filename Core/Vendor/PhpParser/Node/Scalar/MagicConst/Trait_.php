<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Trait_ extends MagicConst
{
    public function getName()
    {
        return '__TRAIT__';
    }
}
