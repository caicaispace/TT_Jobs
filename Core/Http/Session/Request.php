<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Session;

class Request extends Base
{
    public function get($key, $default = null)
    {
        if (! $this->session->isStart()) {
            $this->session->start();
        }
        $data = $this->session->read();
        $data = unserialize($data);
        if (is_array($data)) {
            if (isset($data[$key])) {
                return $data[$key];
            }
            return $default;
        }
        return $default;
    }

    public function toArray()
    {
        if (! $this->session->isStart()) {
            $this->session->start();
        }
        $data = $this->session->read();
        $data = unserialize($data);
        if (is_array($data)) {
            return $data;
        }
        return [];
    }
}
