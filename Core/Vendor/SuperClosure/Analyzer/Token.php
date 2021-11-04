<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace SuperClosure\Analyzer;

/**
 * A Token object represents and individual token parsed from PHP code.
 *
 * Each Token object is a normalized token created from the result of the
 * `get_token_all()`. function, which is part of PHP's tokenizer.
 *
 * @see http://us2.php.net/manual/en/tokens.php
 */
class Token
{
    /**
     * @var string The token name. Always null for literal tokens.
     */
    public $name;

    /**
     * @var null|int The token's integer value. Always null for literal tokens.
     */
    public $value;

    /**
     * @var string the PHP code of the token
     */
    public $code;

    /**
     * @var null|int the line number of the token in the original code
     */
    public $line;

    /**
     * Constructs a token object.
     *
     * @param string $code
     * @param null|int $value
     * @param null|int $line
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($code, $value = null, $line = null)
    {
        if (is_array($code)) {
            [$value, $code, $line] = array_pad($code, 3, null);
        }

        $this->code  = $code;
        $this->value = $value;
        $this->line  = $line;
        $this->name  = $value ? token_name($value) : null;
    }

    public function __toString()
    {
        return $this->code;
    }

    /**
     * Determines if the token's value/code is equal to the specified value.
     *
     * @param mixed $value the value to check
     *
     * @return bool true if the token is equal to the value
     */
    public function is($value)
    {
        return $this->code === $value || $this->value === $value;
    }
}
