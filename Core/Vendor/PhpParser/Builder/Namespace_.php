<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Namespace_ extends PhpParser\BuilderAbstract
{
    private $name;
    private $stmts = [];

    /**
     * Creates a namespace builder.
     *
     * @param null|Node\Name|string $name Name of the namespace
     */
    public function __construct($name)
    {
        $this->name = $name !== null ? $this->normalizeName($name) : null;
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
     * Adds multiple statements.
     *
     * @param array $stmts The statements to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmts(array $stmts)
    {
        foreach ($stmts as $stmt) {
            $this->addStmt($stmt);
        }

        return $this;
    }

    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode()
    {
        return new Stmt\Namespace_($this->name, $this->stmts);
    }
}
