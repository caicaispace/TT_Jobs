<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/** Nop/empty statement (;). */
class Nop extends Node\Stmt
{
    public function getSubNodeNames()
    {
        return [];
    }
}
