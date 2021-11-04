<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class Goto_ extends Stmt
{
    /** @var string Name of label to jump to */
    public $name;

    /**
     * Constructs a goto node.
     *
     * @param string $name Name of label to jump to
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name = $name;
    }

    public function getSubNodeNames()
    {
        return ['name'];
    }
}
