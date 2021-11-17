<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;

class Function_ extends Node\Stmt implements FunctionLike
{
    /** @var bool Whether function returns by reference */
    public $byRef;
    /** @var string Name */
    public $name;
    /** @var Node\Param[] Parameters */
    public $params;
    /** @var null|Node\Name|Node\NullableType|string Return type */
    public $returnType;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a function node.
     *
     * @param string $name Name
     * @param array $subNodes Array of the following optional subnodes:
     *                        'byRef'      => false  : Whether to return by reference
     *                        'params'     => array(): Parameters
     *                        'returnType' => null   : Return type
     *                        'stmts'      => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->byRef      = isset($subNodes['byRef']) ? $subNodes['byRef'] : false;
        $this->name       = $name;
        $this->params     = isset($subNodes['params']) ? $subNodes['params'] : [];
        $this->returnType = isset($subNodes['returnType']) ? $subNodes['returnType'] : null;
        $this->stmts      = isset($subNodes['stmts']) ? $subNodes['stmts'] : [];
    }

    public function getSubNodeNames()
    {
        return ['byRef', 'name', 'params', 'returnType', 'stmts'];
    }

    public function returnsByRef()
    {
        return $this->byRef;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getReturnType()
    {
        return $this->returnType;
    }

    public function getStmts()
    {
        return $this->stmts;
    }
}
