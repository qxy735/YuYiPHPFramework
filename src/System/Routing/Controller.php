<?php namespace System\Routing;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use \Smarty;

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

        // 初始化模板引擎
        parent::__construct();
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
}