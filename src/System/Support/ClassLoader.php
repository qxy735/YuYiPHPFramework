<?php namespace System\Support;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

final class ClassLoader
{
    /**
     * 是否已注册框架自动加载方法
     *
     * @var bool
     */
    private static $registered = false;

    /**
     * 定义自动加载路径目录
     *
     * @var array
     */
    private static $directories = array();

    /**
     * 注册框架自动加载方法
     */
    public static function register()
    {
        // 未注册框架自动加载处理机制，则进行注册
        if (false === self::$registered) {
            self::$registered = spl_autoload_register(array(__CLASS__, 'autoload'), true);
        }
    }

    /**
     * 自动加载未找到的类文件
     *
     * @param $className
     */
    private static function autoload($className)
    {
        // 获取规范化后的类名称
        $className = self::normalizeClassName($className);

        // 在已注册的加载目录中加载类文件
        foreach (self::$directories as $directory) {
            // 判断类型文件是否存在，存在则做加载并结束查找，否则继续查找类文件
            if (file_exists($file = "{$directory}/{$className}.php")) {
                require_once $file;

                return;
            }
        }
    }

    /**
     * 规范化类名称(处理成路径形式)
     *
     * @param $className
     *
     * @return mixed
     */
    private static function normalizeClassName($className)
    {
        // 去除最前面的 \ 符号
        $className = ('\\' === $className[0]) ? substr($className, 1) : $className;

        // 将 \ 和 - 符号转为目录路径符
        return str_replace(array('\\', '-'), DIRECTORY_SEPARATOR, $className);
    }

    /**
     * 添加需要自动加载的目录配置信息
     *
     * @param array $directoies
     */
    public static function addDirectories(array $directoies)
    {
        // 合并增加已存在目录和传入的目录配置信息
        self::$directories = array_merge(self::$directories, $directoies);

        // 去除重复目录配置信息
        self::$directories = array_unique(self::$directories);
    }

    /**
     * 获取设置的自动加载目录配置信息
     *
     * @return array
     */
    public static function getDirectories()
    {
        return self::$directories;
    }

    /**
     * 去除指定或所有的自动加载目录配置信息
     *
     * @param array $directories
     */
    public static function removeDirectories($directories = null)
    {
        // 如果参数值为 null， 则移除所有自动加载目录配置信息，否则移除指定配置项
        if (null === $directories) {
            self::$directories = array();
        } else {
            self::$directories = array_diff(self::$directories, (array)$directories);
        }
    }
}