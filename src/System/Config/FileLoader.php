<?php namespace System\Config;

use System\Filesystem\File;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */
class FileLoader
{
    /**
     * 文件对象
     *
     * @var
     */
    protected $file;

    /**
     * 配置文件默认路径
     *
     * @var
     */
    protected $path;

    /**
     * 初始化文件目录
     *
     * @param File $file
     * @param      $path
     */
    public function __construct(File $file, $path)
    {
        $this->file = $file;

        $this->path = $path;
    }

    /**
     * 载入配置项
     *
     * @param $group
     * @param $env
     *
     * @return array
     */
    public function load($group, $env)
    {
        $globalConfigs = $envConfigs = array();

        // 获取用户配置项目录
        $path = $this->getPath();

        // 获取系统配置项
        $systemConfigs = (array)$this->getSystemConfig();

        if (!$path) {
            return $systemConfigs;
        }

        // 定义用户全局配置项路径
        $globalPath = "{$path}/{$group}.php";

        // 定义用户环境配置项目路径
        $envPath = "{$path}/{$env}/{$group}.php";

        // 判断用户全局配置项文件是否存在，存在则做加载
        if ($this->file->exists($globalPath)) {
            $globalConfigs = require $globalPath;
        }

        // 判断用户环境配置文件是否存在，存在则做加载
        if ($this->file->exists($envPath)) {
            $envConfigs = require $envPath;
        }

        // 返回配置项信息
        return array_merge($systemConfigs, $globalConfigs, $envConfigs);
    }

    /**
     * 获取配置文件目录路径
     *
     * @return mixed
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * 获取系统配置
     *
     * @return bool|mixed
     */
    protected function getSystemConfig()
    {
        $path = BASE_PATH . '/YuYiPHP/config/config.php';

        return $this->file->load($path);
    }
}