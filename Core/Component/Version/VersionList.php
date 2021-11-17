<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Version;

class VersionList
{
    private $list = [];

    public function add($name, callable $judge)
    {
        $version           = new Version($name, $judge);
        $this->list[$name] = $version;
        return $version;
    }

    public function get($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        return null;
    }

    public function all()
    {
        return $this->list;
    }
}
