<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component;

use Core\Component\IO\FileIO;
use Core\Component\Spl\SplArray;

class ShareMemory
{
    public const SERIALIZE_TYPE_JSON      = 'SERIALIZE_TYPE_JSON';
    public const SERIALIZE_TYPE_SERIALIZE = 'SERIALIZE_TYPE_SERIALIZE';
    private $file;
    private $fileStream;
    private static $instance;
    private $ioTimeOut          = 200000;
    private $isStartTransaction = false;
    private $data;
    private $serializeType;

    /*
     * 通过文件+锁的方式来实现数据共享，建议将文件设置到/dev/shm下
     */
    public function __construct($serializeType = self::SERIALIZE_TYPE_JSON, $file = null)
    {
        $this->serializeType = $serializeType;
        if ($file == null) {
            $file = Di::getInstance()->get(SysConst::SHARE_MEMORY_FILE);
            if (empty($file)) {
                $file = Di::getInstance()->get(SysConst::TEMP_DIRECTORY) . '/shareMemory.men';
            }
        }
        $this->file = $file;
    }

    /*
     * 默认等待2秒
     */
    public static function getInstance($serializeType = self::SERIALIZE_TYPE_JSON, $file = null)
    {
        if (! isset(self::$instance)) {
            self::$instance = new static($serializeType, $file);
        }
        return self::$instance;
    }

    public function setIoTimeOut($ioTimeOut)
    {
        $this->ioTimeOut = $ioTimeOut;
    }

    public function startTransaction()
    {
        if ($this->isStartTransaction) {
            return true;
        }
        $this->fileStream = new FileIO($this->file);
        if ($this->fileStream->getStreamResource()) {
            //是否阻塞
            if ($this->ioTimeOut) {
                $takeTime = 0;
                while (! $this->fileStream->lock(LOCK_EX | LOCK_NB)) {
                    if ($takeTime > $this->ioTimeOut) {
                        $this->fileStream->close();
                        unset($this->fileStream);
                        return false;
                    }
                    usleep(5);
                    $takeTime = $takeTime + 5;
                }
                $this->isStartTransaction = true;
                $this->read();
                return true;
            }
            if ($this->fileStream->lock()) {
                $this->isStartTransaction = true;
                $this->read();
                return true;
            }
            $this->fileStream->close();
            unset($this->fileStream);
            return false;
        }
        return false;
    }

    public function commit()
    {
        if ($this->isStartTransaction) {
            $this->write();
            if ($this->fileStream->unlock()) {
                $this->data               = null;
                $this->isStartTransaction = false;
                $this->fileStream->close();
                unset($this->fileStream);
                return true;
            }
            return false;
        }
        return false;
    }

    public function rollback($autoCommit = false)
    {
        if ($this->isStartTransaction) {
            $this->read();
            if ($autoCommit) {
                $this->commit();
            }
            return true;
        }
        return false;
    }

    public function set($key, $val)
    {
        if ($this->isStartTransaction) {
            $this->data->set($key, $val);
            return true;
        }
        if ($this->startTransaction()) {
            $this->data->set($key, $val);
            return $this->commit();
        }
        return false;
    }

    public function del($key)
    {
        return $this->set($key, null);
    }

    public function get($key)
    {
        if ($this->isStartTransaction) {
            return $this->data->get($key);
        }
        if ($this->startTransaction()) {
            $data = $this->data->get($key);
            $this->commit();
            return $data;
        }
        return false;
    }

    public function clear()
    {
        if ($this->isStartTransaction) {
            $this->data = new SplArray();
            return true;
        }
        if ($this->startTransaction()) {
            $this->data = new SplArray();
            return $this->commit();
        }
        return false;
    }

    public function all()
    {
        if ($this->isStartTransaction) {
            return $this->data->getArrayCopy();
        }
        if ($this->startTransaction()) {
            $data = $this->data->getArrayCopy();
            $this->commit();
            return $data;
        }
        return null;
    }

    private function read()
    {
        if ($this->isStartTransaction) {
            $data = $this->fileStream->getContents();
            if ($this->serializeType == self::SERIALIZE_TYPE_JSON) {
                $data       = json_decode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $this->data = is_array($data) ? new SplArray($data) : new SplArray();
            } else {
                $data       = unserialize($data);
                $this->data = is_a($data, SplArray::class) ? $data : new SplArray();
            }
            return true;
        }
        return false;
    }

    private function write()
    {
        if ($this->isStartTransaction) {
            $this->fileStream->truncate();
            $this->fileStream->rewind();
            if ($this->serializeType == self::SERIALIZE_TYPE_JSON) {
                $data = json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $data = serialize($this->data);
            }
            $this->fileStream->write($data);
            return true;
        }
        return false;
    }
}
