<?php
/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use System\Support\Facades\Facade;
use System\Config\Repository as Config;
use System\Foundation\AliasLoader;
use System\Http\Request;

// 绑定应用对象
$application->instance('app', $application);

// 创建应用项目目录
$application->createApplicationDirectories();

// 清除已解决的实例对象(初始化)
Facade::clearResolvedInstances();

// 设置 Application 应用对象到 Facade
Facade::setFacadeApplication($application);

// 注册框架核心内部类别名
$application->registerCoreContainerAliases();

// 实例 Config 对象
$application->instance('config', $config = new Config(
    $application->getConfigLoader(), $env
));

// 获取配置项信息
$config = $application['config']['config'];

// 设置时区
date_default_timezone_set($config['timezone']);

// 开启 session
if ($config['session_start']) {
    session_start();
}

// 获取外部应用类别名
$aliases = $config['aliases'];

// 注册外部应用类别名
AliasLoader::getInstance($aliases)->register();

// 开启 Http 请求参数重写
Request::enableHttpMethodParameterOverride();

// 框架启动处理
$application->booted(function () use ($application, $env) {
    // 获取全局处理文件路径
    $path = $application['path.base'] . '/YuYiPHP/start/global.php';

    // 加载全局处理文件
    file_exists($path) and require $path;

    // 获取路由处理文件路径
    $path = $application['path.base'] . '/routes.php';

    // 加载路由处理文件
    file_exists($path) and require $path;
});