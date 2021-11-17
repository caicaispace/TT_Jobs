<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http;

class SessionFacade
{
    /**
     * Set Session.
     * @param $name
     * @param $value
     * @return bool
     */
    public static function set($name, $value = null)
    {
        $SessionInstance = Response::getInstance()->session();
        if (is_array($name)) {
            try {
                foreach ($name as $sessionName => $sessionValue) {
                    $SessionInstance->set($sessionName, $sessionValue);
                }
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        } else {
            return $SessionInstance->set($name, $value);
        }
    }

    /**
     * Get Session.
     * @param $name
     * @param $default
     * @return null|mixed
     */
    public static function find($name, $default = null)
    {
        $SessionInstance = Request::getInstance()->session();
        return $SessionInstance->get($name, $default);
    }

    /**
     * Check Session exists.
     * @param $name
     * @return bool
     */
    public static function has($name)
    {
        return static::find($name, null) !== null;
    }

    /**
     * Delete Session Values.
     * @param $name
     * @return bool|int
     */
    public static function delete($name)
    {
        $SessionInstance = Response::getInstance()->session();
        return $SessionInstance->set($name, null);
    }

    /**
     * Clear Session.
     */
    public static function clear()
    {
        $Response        = Response::getInstance();
        $SessionInstance = $Response->session();
        $SessionInstance->destroy();
        $Response->setCookie($SessionInstance->sessionName(), null, 0);
    }
}
