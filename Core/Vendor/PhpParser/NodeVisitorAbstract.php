<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser;

/**
 * @codeCoverageIgnore
 */
class NodeVisitorAbstract implements NodeVisitor
{
    public function beforeTraverse(array $nodes)
    {
    }
    public function enterNode(Node $node)
    {
    }
    public function leaveNode(Node $node)
    {
    }
    public function afterTraverse(array $nodes)
    {
    }
}
