<?php namespace System\Foundation;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class AliasLoader
{
    /**
     * 别名实例对象
     *
     * @var
     */
    protected static $instance;

    /**
     * 别名信息
     *
     * @var array
     */
    protected $aliases = array();

    /**
     * 是否已注册
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * 获取别名处理对象
     *
     * @param array $aliases
     *
     * @return mixed
     */
    public static function getInstance(array $aliases = array())
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($aliases);
        }

        $aliases = array_merge(static::$instance->getAliases(), $aliases);

        static::$instance->setAliases($aliases);

        return static::$instance;
    }

    /**
     * 设置别名信息
     *
     * @param array $aliases
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * 获取别名信息
     *
     * @return mixed
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * 注册别名
     */
    public function register()
    {
        if (!$this->registered) {
            $this->prependToLoaderStack();

            $this->registered = true;
        }
    }

    /**
     * 放入到自动加载的队列中
     */
    protected function prependToLoaderStack()
    {
        spl_autoload_register(array($this, 'load'), true, true);
    }

    /**
     * 自动载入别名类
     *
     * @param $alias
     *
     * @return bool
     */
    public function load($alias)
    {
        if (isset($this->aliases[$alias])) {
            return class_alias($this->aliases[$alias], $alias);
        }
    }
}