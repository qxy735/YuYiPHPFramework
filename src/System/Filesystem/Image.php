<?php namespace System\Filesystem;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use System\Support\Facades\Session;
use System\Support\Facades\Config;

class Image
{
    /**
     * 获取验证码
     *
     * @param int    $model
     * @param int    $length
     * @param int    $width
     * @param int    $height
     * @param string $type
     */
    public function verify($model = 4, $length = 4, $width = 80, $height = 20, $type = 'png')
    {
        $verifyCode = self::getCode($model);

        $verifyCode = substr(str_shuffle($verifyCode), 0, $length);

        Session::set(Config::get('config.verify_code'), $verifyCode);

        $img = self::createImage($width, $height);

        $x = floor($width / $length);

        $y = floor(($height / 2) - 7);

        self::drawCode($img, $verifyCode, $x, $y);

        self::setPixel($img);

        self::setHeader($type);

        self::showPngImage($img);

        self::destroy($img);
    }

    /**
     * 创建图像
     *
     * @param $width
     * @param $height
     *
     * @return resource
     */
    private static function createImage($width, $height)
    {
        $img = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($img, 255, 255, 255);

        imagefill($img, 0, 0, $bgColor);

        return $img;
    }

    /**
     * 生成验证码
     *
     * @param $img
     * @param $verifyCode
     * @param $x
     * @param $y
     */
    private static function drawCode($img, $verifyCode, $x, $y)
    {
        foreach (str_split($verifyCode) as $key => $code) {
            imagestring($img, 7, ($key * $x), $y, $code, imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
        }
    }

    /**
     * 设置像素点
     *
     * @param $img
     */
    private static function setPixel($img)
    {
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($img, mt_rand() % 70, mt_rand() % 100, imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
        }
    }

    /**
     * 获取验证码字符串
     *
     * @param $model
     *
     * @return string
     */
    private static function getCode($model)
    {
        $code = '';

        switch ($model) {
            case 1 :
                $code = range(0, 9);
                break;
            case 2 :
                $code = range('a', 'z');
                break;
            case 3 :
                $code = range('A', 'Z');
                break;
            case 4 :
                $code = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        }

        return implode('', $code);
    }

    /**
     * 设置 Header 信息
     *
     * @param $type
     */
    private static function setHeader($type)
    {
        header('Content-Type:image/' . $type);
    }

    /**
     * 显示 Png 图片
     *
     * @param $img
     */
    private static function showPngImage($img)
    {
        imagepng($img);
    }

    /**
     * 销毁图像资源
     *
     * @param $img
     */
    private static function destroy($img)
    {
        imagedestroy($img);
    }
}