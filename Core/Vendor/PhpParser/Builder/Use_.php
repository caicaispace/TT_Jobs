<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Builder;

use PhpParser\BuilderAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt;

/**
 * @method $this as(string $alias) Sets alias for used name.
 */
class Use_ extends BuilderAbstract
{
    protected $name;
    protected $type;
    protected $alias;

    /**
     * Creates a name use (alias) builder.
     *
     * @param Node\Name|string $name Name of the entity (namespace, class, function, constant) to alias
     * @param int $type One of the Stmt\Use_::TYPE_* constants
     */
    public function __construct($name, $type)
    {
        $this->name = $this->normalizeName($name);
        $this->type = $type;
    }
    public function __call($name, $args)
    {
        if (method_exists($this, $name . '_')) {
            return call_user_func_array([$this, $name . '_'], $args);
        }

        throw new \LogicException(sprintf('Method "%s" does not exist', $name));
    }

    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode()
    {
        $alias = $this->alias !== null ? $this->alias : $this->name->getLast();
        return new Stmt\Use_([
            new Stmt\UseUse($this->name, $alias),
        ], $this->type);
    }

    /**
     * Sets alias for used name.
     *
     * @param string $alias Alias to use (last component of full name by default)
     *
     * @return $this The builder instance (for fluid interface)
     */
    protected function as_($alias)
    {
        $this->alias = $alias;
        return $this;
    }
}
