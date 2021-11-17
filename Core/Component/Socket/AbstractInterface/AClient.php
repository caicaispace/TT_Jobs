<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Socket\AbstractInterface;

use Core\Component\Spl\SplBean;

abstract class AClient extends SplBean
{
    protected $clientType;

    /**
     * @return mixed
     */
    public function getClientType()
    {
        return $this->clientType;
    }

    /**
     * @param mixed $clientType
     */
    public function setClientType($clientType)
    {
        $this->clientType = $clientType;
    }
}
