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
     * 文件信息
     *
     * @var array
     */
    private static $files = array();
    /**
     * 错误信息
     *
     * @var string
     */
    private static $error = '';

    /**
     * 文件上传处理
     *
     * @param string $name
     *
     * @return bool
     */
    protected static function deal($name = 'file')
    {
        // 判断文件是否上传
        if (!isset($_FILES[$name])) {
            self::$error = '上传文件不能为空!';

            return false;
        }

        // 获取上传文件相关信息
        self::$files = $_FILES[$name];

        // 判断文件上传是否成功
        if ($msg = self::getFailed()) {
            self::$error = $msg;

            return false;
        }

        // 判断上传文件类型是否合法
        if (!self::isAllowType()) {
            self::$error = '上传文件类型不允许!';

            return false;
        }

        // 判断上传文件大小是否合法
        if (!self::isAllowSize()) {
            self::$error = '上传文件大小不允许!';

            return false;
        }

        return true;
    }

    /**
     * 获取上传失败信息
     *
     * @return string
     */
    public static function getFailed()
    {
        // 获取错误号
        $errno = self::$files['error'];

        $msg = '';

        switch ($errno) {
            case 1 :
                $msg = '上传文件大小超过限定大小';
                break;
            case 2 :
                $msg = '上传文件大小超过表单限制大小';
                break;
            case 3 :
                $msg = '部分文件被上传';
                break;
            case 4 :
                $msg = '文件上传失败';
                break;
            case 6 :
                $msg = '上传文件找不到服务器目录';
                break;
            case 7 :
                $msg = '上传文件写入服务器目录失败';
        }

        return $msg;
    }

    /**
     * 文件大小是否允许
     *
     * @return bool
     */
    public static function isAllowSize()
    {
        // 获取上传文件大小
        $size = self::$files['size'];

        // 设置允许上传的文件大小
        $maxSize = pow('1024', 2);

        return $maxSize > $size ? true : false;
    }

    /**
     * 文件类型是否允许
     *
     * @return bool
     */
    public static function isAllowType()
    {
        // 获取上传文件类型
        $type = explode('/', self::$files['type']);
        $type = $type ? end($type) : '';

        return in_array($type, array('png', 'bmp', 'jpg', 'jpeg', 'gif'));
    }

    /**
     * 文件上传
     *
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    public function upload($path = '', $name = 'file')
    {
        // 定义需要返回的字段信息
        $return = array(
            'path' => $path,
            'name' => array_get(self::$files, 'name', ''),
        );

        // 文件上传处理
        $result = self::deal($name);

        if (false === $result) {
            $return['success'] = false;
            $return['message'] = self::$error;

            return $return;
        }

        // 创建上传文件目录
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // 移动上传文件
        if (!move_uploaded_file(self::$files['tmp_name'], $path)) {
            $return['success'] = false;
            $return['message'] = '文件上传失败';

            return $return;
        }

        // 返回上传成功信息
        $return['success'] = true;
        $return['message'] = '文件上传成功';

        return $return;
    }
    
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