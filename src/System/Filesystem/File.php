<?php namespace System\Filesystem;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class File
{
    /**
     * 文件载入
     *
     * @param string $path
     *
     * @return bool|mixed
     */
    public function load($path = '')
    {
        if (file_exists($path)) {
            return require $path;
        }

        return false;
    }

    /**
     * 判断文件是否存在
     *
     * @param $file
     *
     * @return bool
     */
    public function exists($file)
    {
        return file_exists($file);
    }
}