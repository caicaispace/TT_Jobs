<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Session;

use Core\Component\IO\FileIO;
use Core\Utility\File;

class SessionHandler implements \SessionHandlerInterface
{
    private $sessionName;
    private $savePath;
    private $fileStream;
    private $saveFile;

    public function close()
    {
        if ($this->fileStream instanceof FileIO) {
            if ($this->fileStream->getStreamResource()) {
                $this->fileStream->unlock();
            }
            $this->fileStream = null;
            return true;
        }
        return true;
    }

    public function destroy($session_id)
    {
        $this->close();
        if (file_exists($this->saveFile)) {
            unlink($this->saveFile);
        }
        return true;
    }

    public function gc($maxlifetime)
    {
        $current = time();
        $res     = File::scanDir($this->savePath);
        if (is_array($res)) {
            foreach ($res as $file) {
                $time = fileatime($file);
                if ($current - $time > $maxlifetime) {
                    unlink($file);
                }
            }
        }
    }

    public function open($save_path, $name)
    {
        $this->savePath    = $save_path;
        $this->sessionName = $name;
        return true;
    }

    public function read($session_id)
    {
        if (! $this->fileStream) {
            $this->saveFile   = $this->savePath . "/{$this->sessionName}_{$session_id}";
            $this->fileStream = new FileIO($this->saveFile);
        }
        if (! $this->fileStream->getStreamResource()) {
            return '';
        }
        $this->fileStream->lock();
        return $this->fileStream->__toString();
    }

    public function write($session_id, $session_data)
    {
        if (! $this->fileStream) {
            $this->fileStream = new FileIO($this->saveFile);
        }
        if (! $this->fileStream->getStreamResource()) {
            return false;
        }
        $this->fileStream->truncate();
        $this->fileStream->rewind();
        return $this->fileStream->write($session_data);
    }
}
