<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace EasySwoole\Core\Component\Spl;

class SplFileStream extends SplStream
{
    public function __construct($file, $mode = 'c+')
    {
        $fp = fopen($file, $mode);
        parent::__construct($fp);
    }

    public function lock($mode = LOCK_EX)
    {
        return flock($this->getStreamResource(), $mode);
    }

    public function unlock($mode = LOCK_UN)
    {
        return flock($this->getStreamResource(), $mode);
    }
}
