<?php namespace System\Exception;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class ShutdownHandler extends Handler
{
    /**
     * 显示错误信息
     */
    public static function shutdown()
    {
        // 获取最后一个错误
        $error = error_get_last();

        // 显示错误信息
        if ($error) {
            self::displayDebugPanel('Fatal Error', $error['message'], $error['file'], $error['line']);
        }
    }
}