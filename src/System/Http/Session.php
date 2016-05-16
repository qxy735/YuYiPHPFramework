<?php namespace System\Http;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class Session
{
    /**
     * 获取所有 Session 值
     *
     * @return mixed
     */
    public function all()
    {
        return $_SESSION;
    }

    /**
     * 判断指定 Session 是否存在
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * 获取 Session
     *
     * @param $key
     *
     * @return null
     */
    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * 设置 Session
     *
     * @param        $key
     * @param        $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     */
    public function set($key, $value, $expire = 0, $path = '/', $domain = '')
    {
        $_SESSION[$key] = $value;

        if ($expire) {
            setcookie($key, $value, time() + $expire, $path, $domain, null, true);
        }
    }

    /**
     * 清除 Session
     *
     * @param null $key
     *
     * @return $this
     */
    public function clear($key = null)
    {
        if ($key) {
            unset($_SESSION[$key]);
        } else {
            $_SESSION = array();

            session_destroy();
        }

        return $this;
    }

    /**
     * 清除 Cookie
     *
     * @param null   $key
     * @param string $path
     * @param string $domain
     */
    public function cookie($key = null, $path = '/', $domain = '')
    {
        setcookie($key, '', time() - 1, $path, $domain);
    }
}