<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility\Validate;

class Rule
{
    public const ACTIVE_URL  = 'ACTIVE_URL';
    public const ALPHA       = 'ALPHA';
    public const BETWEEN     = 'BETWEEN';
    public const BOOLEAN     = 'BOOLEAN';
    public const DATE        = 'DATE';
    public const DATE_AFTER  = 'DATE_AFTER';
    public const DATE_BEFORE = 'DATE_BEFORE';
    public const DIFFERENT   = 'DIFFERENT';
    public const FLOAT       = 'FLOAT';
    public const IN          = 'IN';
    public const INTEGER     = 'INTEGER';
    public const IP          = 'IP';
    public const ARRAY_      = 'ARRAY_';
    public const LEN         = 'LEN';
    public const NOT_EMPTY   = 'NOT_EMPTY';
    public const NOT_IN      = 'NOT_IN';
    public const NUMERIC     = 'NUMERIC';
    public const MAX         = 'MAX';
    public const MAX_LEN     = 'MAX_LEN';
    public const MIN         = 'MIN';
    public const MIN_LEN     = 'MIN_LEN';
    public const OPTIONAL    = 'OPTIONAL';
    public const REGEX       = 'REGEX';
    public const REQUIRED    = 'REQUIRED';
    public const SAME        = 'SAME';
    public const TIMESTAMP   = 'TIMESTAMP';
    public const URL         = 'URL';
}
