<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser\Unserializer;

use DomainException;
use PhpParser\Unserializer;
use XMLReader;

/**
 * @deprecated
 */
class XML implements Unserializer
{
    protected $reader;

    public function __construct()
    {
        $this->reader = new XMLReader();
    }

    public function unserialize($string)
    {
        $this->reader->XML($string);

        $this->reader->read();
        if ($this->reader->name !== 'AST') {
            throw new DomainException('AST root element not found');
        }

        return $this->read($this->reader->depth);
    }

    protected function read($depthLimit, $throw = true, &$nodeFound = null)
    {
        $nodeFound = true;
        while ($this->reader->read() && $depthLimit < $this->reader->depth) {
            if ($this->reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            if ($this->reader->prefix === 'node') {
                return $this->readNode();
            }
            if ($this->reader->prefix === 'scalar') {
                return $this->readScalar();
            }
            if ($this->reader->name === 'comment') {
                return $this->readComment();
            }
            throw new DomainException(sprintf('Unexpected node of type "%s"', $this->reader->name));
        }

        $nodeFound = false;
        if ($throw) {
            throw new DomainException('Expected node or scalar');
        }
    }

    protected function readNode()
    {
        $className = $this->getClassNameFromType($this->reader->localName);

        // create the node without calling it's constructor
        $node = unserialize(
            sprintf(
                "O:%d:\"%s\":1:{s:13:\"\0*\0attributes\";a:0:{}}",
                strlen($className),
                $className
            )
        );

        $depthLimit = $this->reader->depth;
        while ($this->reader->read() && $depthLimit < $this->reader->depth) {
            if ($this->reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            $type = $this->reader->prefix;
            if ($type !== 'subNode' && $type !== 'attribute') {
                throw new DomainException(
                    sprintf('Expected sub node or attribute, got node of type "%s"', $this->reader->name)
                );
            }

            $name  = $this->reader->localName;
            $value = $this->read($this->reader->depth);

            if ($type === 'subNode') {
                $node->{$name} = $value;
            } else {
                $node->setAttribute($name, $value);
            }
        }

        return $node;
    }

    protected function readScalar()
    {
        switch ($name = $this->reader->localName) {
            case 'array':
                $depth = $this->reader->depth;
                $array = [];
                while (true) {
                    $node = $this->read($depth, false, $nodeFound);
                    if (! $nodeFound) {
                        break;
                    }
                    $array[] = $node;
                }
                return $array;
            case 'string':
                return $this->reader->readString();
            case 'int':
                return $this->parseInt($this->reader->readString());
            case 'float':
                $text = $this->reader->readString();
                if (false === $float = filter_var($text, FILTER_VALIDATE_FLOAT)) {
                    throw new DomainException(sprintf('"%s" is not a valid float', $text));
                }
                return $float;
            case 'true':
            case 'false':
            case 'null':
                if (! $this->reader->isEmptyElement) {
                    throw new DomainException(sprintf('"%s" scalar must be empty', $name));
                }
                return constant($name);
            default:
                throw new DomainException(sprintf('Unknown scalar type "%s"', $name));
        }
    }

    protected function readComment()
    {
        $className = $this->reader->getAttribute('isDocComment') === 'true'
            ? 'PhpParser\Comment\Doc'
            : 'PhpParser\Comment';
        return new $className(
            $this->reader->readString(),
            $this->parseInt($this->reader->getAttribute('line'))
        );
    }

    protected function getClassNameFromType($type)
    {
        $className = 'PhpParser\\Node\\' . strtr($type, '_', '\\');
        if (! class_exists($className)) {
            $className .= '_';
        }
        if (! class_exists($className)) {
            throw new DomainException(sprintf('Unknown node type "%s"', $type));
        }
        return $className;
    }

    private function parseInt($text)
    {
        if (false === $int = filter_var($text, FILTER_VALIDATE_INT)) {
            throw new DomainException(sprintf('"%s" is not a valid integer', $text));
        }
        return $int;
    }
}
