<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Scalar\MagicConst;

use PhpParser\Node\Scalar\MagicConst;

class Method extends MagicConst
{
    public function getName()
    {
        return '__METHOD__';
    }
}
