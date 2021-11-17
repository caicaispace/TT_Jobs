<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Node\Stmt;

use PhpParser\Error;
use PhpParser\Node;

class Class_ extends ClassLike
{
    public const MODIFIER_PUBLIC    =  1;
    public const MODIFIER_PROTECTED =  2;
    public const MODIFIER_PRIVATE   =  4;
    public const MODIFIER_STATIC    =  8;
    public const MODIFIER_ABSTRACT  = 16;
    public const MODIFIER_FINAL     = 32;

    public const VISIBILITY_MODIFIER_MASK = 7; // 1 | 2 | 4
    /** @deprecated */
    public const VISIBILITY_MODIFER_MASK = self::VISIBILITY_MODIFIER_MASK;

    /** @var int Type */
    public $flags;
    /** @var null|Node\Name Name of extended class */
    public $extends;
    /** @var Node\Name[] Names of implemented interfaces */
    public $implements;

    /** @deprecated Use $flags instead */
    public $type;

    protected static $specialNames = [
        'self'   => true,
        'parent' => true,
        'static' => true,
    ];

    /**
     * Constructs a class node.
     *
     * @param null|string $name Name
     * @param array $subNodes Array of the following optional subnodes:
     *                        'flags'      => 0      : Flags
     *                        'extends'    => null   : Name of extended class
     *                        'implements' => array(): Names of implemented interfaces
     *                        'stmts'      => array(): Statements
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->flags = isset($subNodes['flags']) ? $subNodes['flags']
            : (isset($subNodes['type']) ? $subNodes['type'] : 0);
        $this->type       = $this->flags;
        $this->name       = $name;
        $this->extends    = isset($subNodes['extends']) ? $subNodes['extends'] : null;
        $this->implements = isset($subNodes['implements']) ? $subNodes['implements'] : [];
        $this->stmts      = isset($subNodes['stmts']) ? $subNodes['stmts'] : [];
    }

    public function getSubNodeNames()
    {
        return ['flags', 'name', 'extends', 'implements', 'stmts'];
    }

    public function isAbstract()
    {
        return (bool) ($this->flags & self::MODIFIER_ABSTRACT);
    }

    public function isFinal()
    {
        return (bool) ($this->flags & self::MODIFIER_FINAL);
    }

    public function isAnonymous()
    {
        return $this->name === null;
    }

    /**
     * @internal
     * @param mixed $a
     * @param mixed $b
     */
    public static function verifyModifier($a, $b)
    {
        if ($a & self::VISIBILITY_MODIFIER_MASK && $b & self::VISIBILITY_MODIFIER_MASK) {
            throw new Error('Multiple access type modifiers are not allowed');
        }

        if ($a & self::MODIFIER_ABSTRACT && $b & self::MODIFIER_ABSTRACT) {
            throw new Error('Multiple abstract modifiers are not allowed');
        }

        if ($a & self::MODIFIER_STATIC && $b & self::MODIFIER_STATIC) {
            throw new Error('Multiple static modifiers are not allowed');
        }

        if ($a & self::MODIFIER_FINAL && $b & self::MODIFIER_FINAL) {
            throw new Error('Multiple final modifiers are not allowed');
        }

        if ($a & 48 && $b & 48) {
            throw new Error('Cannot use the final modifier on an abstract class member');
        }
    }
}
