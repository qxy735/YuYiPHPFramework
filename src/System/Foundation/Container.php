<?php namespace System\Foundation;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use ArrayAccess;
use Countable;
use Closure;

class  Container implements ArrayAccess, Countable
{
    /**
     * 实例别名
     *
     * @var array
     */
    protected $aliases = array();

    /**
     * 实例对象
     *
     * @var array
     */
    protected $instances = array();

    /**
     * 绑定的对象
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * 对象是否已解决
     *
     * @var array
     */
    protected $resolved = array();

    /**
     * 注册实例对象
     *
     * @param $abstract
     * @param $instance
     */
    public function instance($abstract, $instance)
    {
        if (is_array($abstract)) {
            list($abstract, $alias) = $this->convertToIndexArray($abstract);

            $this->setAlias($abstract, $alias);
        }

        $this->instances[$abstract] = $instance;
    }

    /**
     * 设置实例别名
     *
     * @param $abstract
     * @param $alias
     */
    protected function setAlias($abstract, $alias)
    {
        $this->aliases[$abstract] = $alias;
    }

    /**
     * 获取设置的实例别名
     *
     * @return array
     */
    public function getAlias()
    {
        return $this->aliases;
    }

    /**
     * 卸载指定实例对象
     *
     * @param $abstract
     */
    public function forgetInstance($abstract)
    {
        unset($this->instances[$abstract]);
    }

    /**
     * 重置实例对象
     */
    public function forgetInstances()
    {
        $this->instances = array();
    }

    /**
     * 转为索引数组
     *
     * @param array $array
     *
     * @return array
     */
    protected function convertToIndexArray(array $array)
    {
        return array_values($array);
    }

    /**
     * Whether A Offset Exists
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->bindings[$key]);
    }

    /**
     * 获取绑定或注册的实例对象
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     * 绑定或实例的对象
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        if ($value instanceof Closure) {
            $this->bindings[$key] = $value;
        } else {
            $this->instances[$key] = $value;
        }
    }

    /**
     * 卸载绑定或注册的实例对象
     *
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->bindings[$key]);

        unset($this->instances[$key]);
    }

    /**
     * 创建对象
     *
     * @param       $abstract
     * @param array $params
     *
     * @return null
     */
    public function make($abstract, $params = array())
    {
        $abstract = $this->getAlia($abstract);

        $this->resolved[$abstract] = true;

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        return $this->bulid($abstract, $params);
    }

    /**
     * 获取构建的对象
     *
     * @param $abstract
     * @param $params
     *
     * @return null
     */
    protected function bulid($abstract, $params)
    {
        $object = isset($this->bindings[$abstract]) ? $this->bindings[$abstract] : null;

        if ($object instanceof Closure) {
            return $object($this, $params);
        }

        if (null === $object) {
            $abstract = $this->getAbstract($abstract);

            return new $abstract();
        }

        return $object;
    }

    /**
     * 获取特殊别名
     *
     * @param $abstract
     *
     * @return mixed
     */
    protected function getAbstract($abstract)
    {
        if ($alia = array_search($abstract, $this->aliases)) {
            return $alia;
        }

        return $abstract;
    }

    /**
     * 获取别名
     *
     * @param $abstract
     *
     * @return mixed
     */
    protected function getAlia($abstract)
    {
        return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
    }

    /**
     * 返回实例对象数
     *
     * @return int
     */
    public function count()
    {
        return count($this->instances);
    }

    /**
     * 添加别名信息
     *
     * @param $abstract
     * @param $alias
     */
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
    }
}