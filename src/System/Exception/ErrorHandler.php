<?php namespace System\Exception;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class ErrorHandler extends Handler
{
    /**
     * 自定义错误处理机制
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public static function error($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_USER_ERROR :
                self::displayDebugPanel('Fatal Error', $errstr, $errfile, $errline);
                exit(1);
            case E_USER_WARNING :
            case E_WARNING :
                self::displayDebugPanel('Warning Error', $errstr, $errfile, $errline);
                break;
            case E_USER_NOTICE :
            case E_NOTICE :
                self::displayDebugPanel('Notice Error', $errstr, $errfile, $errline);
                break;
        }
    }
}