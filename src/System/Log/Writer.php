<?php namespace System\Log;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use System\Support\Facades\Config;

class Writer
{
    /**
     * 日志存放路径
     *
     * @var
     */
    protected static $path;

    /**
     * 设置日志存放路径
     *
     * @param $path
     */
    public function setPath($path)
    {
        self::$path = $path;
    }

    /**
     * 获取日志存放路径
     *
     * @return mixed
     */
    public function getPath()
    {
        return self::$path;
    }

    /**
     * 日志信息写入
     *
     * @param string $log
     * @param string $level
     * @param int    $type
     * @param null   $savePath
     */
    public static function error($log = '', $level = 'Error', $type = 3, $savePath = null)
    {
        // 日志写入功能是否开启
        if (false == Config::get('config.log_write_start')) {
            return;
        }

        // 设置默认日志信息保存文件及位置
        if (is_null($savePath)) {
            $savePath = self::$path;
        }

        // 获取脚本执行时间
        $time = round((microtime(true) - YUYI_START_TIME), 4);

        // 写入日志信息
        if (is_dir(storage_path() . '/logs')) {
            error_log("[Date]:" . date('Y-m-d H:i:s') . " {$level}:{$log} (runtime {$time} sec) \r\n", $type, $savePath);
        }
    }
}