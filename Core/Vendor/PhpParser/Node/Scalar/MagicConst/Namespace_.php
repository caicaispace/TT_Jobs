<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Namespace_ extends MagicConst
{
    public function getName()
    {
        return '__NAMESPACE__';
    }
}
