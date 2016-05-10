<?php namespace System\Exception;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class ExceptionHandler extends handler
{
    /**
     * 显示异常错误信息
     *
     * @param $msg
     */
    public static function exception($msg)
    {
        self::displayDebugPanel('Fatal Error', $msg, '', '');
    }
}