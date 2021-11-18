<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Spl;

class SplString
{
    private $rawString;

    public function __construct($rawString = null)
    {
        $this->rawString = (string) $rawString;
    }

    public function __toString()
    {
        return (string) $this->rawString;
    }

    public function setString($string)
    {
        $this->rawString = (string) $string;
        return $this;
    }

    public function split($length = 1)
    {
        $this->rawString = str_split($this->rawString, $length);
        return $this;
    }

    public function encodingConvert($desEncoding, $detectList = [
        'UTF-8',
        'ASCII',
        'GBK',
        'GB2312',
        'LATIN1',
        'BIG5',
        'UCS-2',
    ])
    {
        $fileType = mb_detect_encoding($this->rawString, $detectList);
        if ($fileType != $desEncoding) {
            $this->rawString = mb_convert_encoding($this->rawString, $desEncoding, $fileType);
            return $this;
        }
        return $this;
    }

    public function toUtf8()
    {
        return $this->encodingConvert('UTF-8');
    }

    /*
     * special function for unicode
     */
    public function unicodeToUtf8()
    {
        $this->rawString = preg_replace_callback(
            '/\\\\u([0-9a-f]{4})/i',
            function ($matches) {
                return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UCS-2BE');
            },
            $this->rawString
        );
        return $this;
    }

    public function toUnicode()
    {
        $raw = (string) $this->encodingConvert('UCS-2');
        $len = strlen($raw);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c  = $raw[$i];
            $c2 = $raw[$i + 1];
            if (ord($c) > 0) {   //两个字节的文字
                $str .= '\u' . base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u' . str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
        $this->rawString = strtoupper($str); //转换为大写
        return $this;
    }

    /*
     * special function for unicode end
    */

    public function explode($separator)
    {
        return new SplArray(explode($separator, $this->rawString));
    }

    public function subString($start, $length)
    {
        $this->rawString = substr($this->rawString, $start, $length);
        return $this;
    }

    public function compare($str, $ignoreCase = 0)
    {
        if ($ignoreCase) {
            return strcasecmp($str, $this->rawString);
        }
        return strcmp($str, $this->rawString);
    }

    public function lTrim($charList = " \t\n\r\0\x0B")
    {
        $this->rawString = ltrim($this->rawString, $charList);
        return $this;
    }

    public function rTrim($charList = " \t\n\r\0\x0B")
    {
        $this->rawString = rtrim($this->rawString, $charList);
        return $this;
    }

    public function trim($charList = " \t\n\r\0\x0B")
    {
        $this->rawString = trim($this->rawString, $charList);
        return $this;
    }

    public function pad($length, $padString = null, $pad_type = STR_PAD_RIGHT)
    {
        $this->rawString = str_pad($this->rawString, $length, $padString, $pad_type);
        return $this;
    }

    public function repeat($times)
    {
        $this->rawString = str_repeat($this->rawString, $times);
        return $this;
    }

    public function length()
    {
        return strlen($this->rawString);
    }

    public function toUpper()
    {
        $this->rawString = strtoupper($this->rawString);
        return $this;
    }

    public function toLower()
    {
        $this->rawString = strtolower($this->rawString);
        return $this;
    }

    public function stripTags($allowable_tags = null)
    {
        $this->rawString = strip_tags($this->rawString, $allowable_tags);
        return $this;
    }

    public function replace($find, $replaceTo)
    {
        $this->rawString = str_replace($find, $replaceTo, $this->rawString);
        return $this;
    }

    public function betweenInStr($startStr, $endStr)
    {
        $st = stripos($this->rawString, $startStr);
        $ed = stripos($this->rawString, $endStr);
        if (($st == false || $ed == false) || $st >= $ed) {
            $this->rawString = '';
        } else {
            $this->rawString = substr($this->rawString, ($st + 1), ($ed - $st - 1));
        }
        return $this;
    }

    public function regex($regex, $rawReturn = false)
    {
        preg_match($regex, $this->rawString, $result);
        if (! empty($result)) {
            if ($rawReturn) {
                return $result;
            }
            return $result[0];
        }
        return null;
    }

    public function exist($find, $ignoreCase = true)
    {
        if ($ignoreCase) {
            $label = stripos($this->rawString, $find);
        } else {
            $label = strpos($this->rawString, $find);
        }
        return $label === false ? false : true;
    }
}
