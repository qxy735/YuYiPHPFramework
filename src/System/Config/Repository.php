<?php namespace System\Config;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use ArrayAccess;

class Repository implements ArrayAccess
{
    /**
     * 操作载入文件目录对象
     *
     * @var
     */
    protected $loader;

    /**
     * 环境变量
     *
     * @var
     */
    protected $env;

    /**
     * 已解析的 Key 值
     *
     * @var array
     */
    protected $parsed = array();

    /**
     * 配置项信息
     *
     * @var array
     */
    protected $items = array();

    /**
     * 初始化配置操作
     *
     * @param FileLoader $loader
     * @param            $env
     */
    public function __construct(FileLoader $loader, $env)
    {
        $this->loader = $loader;

        $this->env = $env;
    }

    /**
     * 载入配置项
     *
     * @param $group
     */
    protected function load($group)
    {
        // 获取环境值
        $env = $this->env;

        // 如果配置项已获取，则不再重新获取
        if (isset($this->items[$group])) {
            return;
        }

        $items = $this->loader->load($group, $env);

        $this->items[$group] = $items;
    }

    /**
     * 解析 Key 信息
     *
     * @param $key
     *
     * @return array
     */
    protected function parseKey($key)
    {
        // 返回已解析的 Key
        if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }

        $segments = explode('.', $key);

        $group = $segments[0];

        if (count($segments) == 1) {
            $parsed = array($group, null);
        } else {
            $item = implode('.', array_slice($segments, 1));

            $parsed = array($group, $item);
        }

        return $this->parsed[$key] = $parsed;
    }

    /**
     * 获取配置项
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($group, $item) = $this->parseKey($key);

        $this->load($group);

        return array_get($this->items[$group], $item, $default);
    }

    /**
     * 临时设置配置项
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        list($group, $item) = $this->parseKey($key);

        $this->load($group);

        if (is_null($item)) {
            $this->items[$group] = $value;
        } else {
            array_set($this->items[$group], $item, $value);
        }
    }

    /**
     * 判断配置项是否存在
     *
     * @param $key
     *
     * @return mixed
     */
    public function has($key)
    {
        list($group, $item) = $this->parseKey($key);

        if (!isset($this->items[$group])) {
            return false;
        }

        $config = $this->items[$group];

        foreach (explode('.', $item) as $name) {
            if (!isset($config[$name])) {
                return false;
            }

            $config = $config[$name];
        }

        return true;
    }

    /**
     * 判断指定值是否存在
     *
     * @param mixed $key
     *
     * @return bool|mixed
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * 获取指定值
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * 设置指定值
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * 卸载指定值
     *
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}