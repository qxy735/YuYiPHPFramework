<?php namespace System\Support\Facades;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use RuntimeException;

abstract class Facade
{
    /**
     * 已实例的对象
     *
     * @var
     */
    protected static $resolvedInstance;

    /**
     * Application 应用对象
     *
     * @var
     */
    protected static $app;

    /**
     * 清除已实例的对象
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = array();
    }

    /**
     * 设置 Application 应用对象到 Facade
     *
     * @param $app
     */
    public static function setFacadeApplication($app)
    {
        self::$app = $app;
    }

    /**
     * 获取 Application 应用对象
     *
     * @return mixed
     */
    public static function getFacadeApplication()
    {
        return self::$app;
    }

    protected static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * 获取指定对象
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * 获取指定对象实例子
     *
     * @param $name
     *
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$app[$name];
    }

    /**
     * 清除对应已解决的实例
     *
     * @param $name
     */
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstance[$name]);
    }

    /**
     * 自动调用指定对象方法
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        return call_user_func_array(array($instance, $method), $args);
    }
}