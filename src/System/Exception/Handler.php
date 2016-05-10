<?php namespace System\Exception;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use System\Support\Facades\Log;

class Handler
{
    /**
     * Application 应用对象
     *
     * @var null
     */
    protected static $app = null;

    /**
     * Handler 类初始化
     *
     * @param null $app
     */
    public function __construct($app = null)
    {
        self::$app = $app;
    }

    /**
     * 注册错误处理机制
     */
    public static function registerErrorHandler()
    {
        set_error_handler(array('System\Exception\ErrorHandler', 'error'));
    }

    /**
     * 注册异常处理机制
     */
    public static function registerExceptionHandler()
    {
        set_exception_handler(array('System\Exception\ExceptionHandler', 'exception'));
    }

    /**
     * 注册致命错误处理机制
     */
    public static function registerShutdownHandler()
    {
        register_shutdown_function(array('System\Exception\ShutdownHandler', 'shutdown'));
    }

    /**
     * 显示错误信息面板
     *
     * @param $title
     * @param $msg
     * @param $file
     * @param $line
     */
    protected static function displayDebugPanel($title, $msg, $file, $line)
    {
        $isdie = false;

        // 定义错误文件加载地址
        if (APP_ENV_PRODUCTION === env() || false === APP_DEBUG) {
            $path = __DIR__ . '/file/error.html';

            $isdie = true;
        } else {
            $path = __DIR__ . '/file/debug.html';
        }

        // 记录日志信息
        if (APP_DEBUG) {
            if ($file) {
                $msg .= " Error File : {$file}";
            }

            if ($line) {
                $msg .= " Error Number : {$line}";
            }

            Log::error($msg, $title);
        }

        // 加载错误页面展示文件
        if (file_exists($path)) {
            require $path;

            if ($isdie) {
                exit(1);
            }
        } else {
            exit('Debug File Not Exists !');
        }
    }

    /**
     * 获取回溯信息
     *
     * @return string
     */
    protected static function getBackTrace()
    {
        ob_start();

        debug_print_backtrace();

        $trace = ob_get_contents();

        ob_end_clean();

        return $trace;
    }
}