<?php namespace System\Support\Facades;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class Session extends Facade
{
    /**
     * 获取操作者对象
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'session';
    }
}