<?php namespace System\Routing;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use \Smarty;
use System\Support\Facades\Config;

class Controller extends Smarty
{
    /**
     * 初始化基础控制器
     */
    public function __construct()
    {
        // 设置模板文件目录
        $this->setTemplateDir(app_path() . '/views');

        // 设置模板缓存文件目录
        $this->setCacheDir(storage_path() . '/cache');

        // 设置模板编译文件目录
        $this->setCompileDir(storage_path() . '/compile');

        // 设置模板引擎配置
        $this->setTemplateConfig();
        
        // 初始化模板引擎
        parent::__construct();
    }

    /**
     * 设置模板引擎配置
     */
    protected function setTemplateConfig()
    {
        // 获取配置信息
        $config = Config::get('config');

        // 设置模版使用分隔符号
        $this->left_delimiter = $config['left_delimiter'];
        $this->right_delimiter = $config['right_delimiter'];

        // 设置模版缓存信息
        $this->caching = $config['cache_start'];
        $this->cache_lifetime = $config['cache_life_time'];
    }

    /**
     * 显示模板文件
     *
     * @param string $path
     */
    public function display($path = '')
    {
        parent::display($path);
    }

    /**
     * 操作成功跳转提示
     *
     * @param string $message
     * @param string $url
     * @param int    $time
     */
    protected function success($message = '', $url = '', $time = 3)
    {
        include Config::get('config.tpl_file_path') . '/' . Config::get('config.success_tpl_file');exit;
    }

    /**
     * 操作失败跳转提示
     *
     * @param string $message
     * @param string $url
     * @param int    $time
     */
    protected function error($message = '', $url = '', $time = 3)
    {
        include Config::get('config.tpl_file_path') . '/' . Config::get('config.error_tpl_file');exit;
    }
}