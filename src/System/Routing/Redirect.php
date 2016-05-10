<?php namespace System\Routing;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class Redirect
{
    /**
     * 跳转到指定地址
     *
     * @param string $url
     */
    public static function to($url = '')
    {
        if (!$url) {
            return;
        }

        // 处理跳转地址
        $url = '/' . trim($url, '/');

        // 跳转到指定地址
        Go($url);
    }
}