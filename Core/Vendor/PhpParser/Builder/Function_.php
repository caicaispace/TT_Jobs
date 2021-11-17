<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Function_ extends FunctionLike
{
    protected $name;
    protected $stmts = [];

    /**
     * Creates a function builder.
     *
     * @param string $name Name of the function
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Adds a statement.
     *
     * @param Node|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt)
    {
        $this->stmts[] = $this->normalizeNode($stmt);

        return $this;
    }

    /**
     * Returns the built function node.
     *
     * @return Stmt\Function_ The built function node
     */
    public function getNode()
    {
        return new Stmt\Function_($this->name, [
            'byRef'      => $this->returnByRef,
            'params'     => $this->params,
            'returnType' => $this->returnType,
            'stmts'      => $this->stmts,
        ], $this->attributes);
    }
}
