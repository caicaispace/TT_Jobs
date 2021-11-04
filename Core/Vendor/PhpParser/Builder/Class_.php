<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Class_ extends Declaration
{
    protected $name;

    protected $extends;
    protected $implements = [];
    protected $flags      = 0;

    protected $uses       = [];
    protected $constants  = [];
    protected $properties = [];
    protected $methods    = [];

    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Extends a class.
     *
     * @param Name|string $class Name of class to extend
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function extend($class)
    {
        $this->extends = $this->normalizeName($class);

        return $this;
    }

    /**
     * Implements one or more interfaces.
     *
     * @param Name|string ...$interfaces Names of interfaces to implement
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function implement()
    {
        foreach (func_get_args() as $interface) {
            $this->implements[] = $this->normalizeName($interface);
        }

        return $this;
    }

    /**
     * Makes the class abstract.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeAbstract()
    {
        $this->setModifier(Stmt\Class_::MODIFIER_ABSTRACT);

        return $this;
    }

    /**
     * Makes the class final.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeFinal()
    {
        $this->setModifier(Stmt\Class_::MODIFIER_FINAL);

        return $this;
    }

    /**
     * Adds a statement.
     *
     * @param PhpParser\Builder|Stmt $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt)
    {
        $stmt = $this->normalizeNode($stmt);

        $targets = [
            'Stmt_TraitUse'    => &$this->uses,
            'Stmt_ClassConst'  => &$this->constants,
            'Stmt_Property'    => &$this->properties,
            'Stmt_ClassMethod' => &$this->methods,
        ];

        $type = $stmt->getType();
        if (! isset($targets[$type])) {
            throw new \LogicException(sprintf('Unexpected node of type "%s"', $type));
        }

        $targets[$type][] = $stmt;

        return $this;
    }

    /**
     * Returns the built class node.
     *
     * @return Stmt\Class_ The built class node
     */
    public function getNode()
    {
        return new Stmt\Class_($this->name, [
            'flags'      => $this->flags,
            'extends'    => $this->extends,
            'implements' => $this->implements,
            'stmts'      => array_merge($this->uses, $this->constants, $this->properties, $this->methods),
        ], $this->attributes);
    }
}
