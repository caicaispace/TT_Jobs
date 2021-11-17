<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Session;

class Response extends Base
{
    public function set($key, $default)
    {
        if (! $this->session->isStart()) {
            $this->session->start();
        }
        $data = $this->session->read();
        $data = unserialize($data);
        if (! is_array($data)) {
            $data = [];
        }
        $data[$key] = $default;
        return $this->session->write(serialize($data));
    }
}
