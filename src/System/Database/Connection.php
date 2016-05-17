<?php namespace System\Database;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use System\Support\Facades\Config;

class Connection
{
    /**
     * 数据库配置信息
     *
     * @var
     */
    public $config;

    /**
     * 数据库句柄
     *
     * @var
     */
    protected static $link;

    /**
     * 数据库链接名称
     *
     * @var
     */
    protected static $database;

    /**
     * 初始化链接处理
     *
     * @param $name
     */
    public function __construct($name)
    {
        if ('default' === $name) {
            $name = Config::get('database.default');
        }

        // 获取数据库链接配置信息
        $this->config = Config::get("database.{$name}") ? : array();
    }

    /**
     * 链接数据库
     *
     * @return \mysqli
     */
    public function connect()
    {
        // 链接数据库
        if (is_null(self::$link) || (self::$database != $this->config['database'])) {
            self::$link = $link = mysqli_connect($this->config['host'], $this->config['username'], $this->config['password']);

            $link or trigger_error('数据库链接失败: ' . mysqli_error($link), E_USER_ERROR);

            self::$database = $this->config['database'];
        }

        // 选择数据库
        $this->selectDatabase();

        // 设置字符编码
        $this->setCharset();

        // 返回链接句柄
        return self::$link;
    }

    /**
     * 选择数据库
     */
    protected function selectDatabase()
    {
        $result = mysqli_select_db(self::$link, $this->config['database']);

        $result or trigger_error('选择数据库失败: ' . mysqli_error(self::$link), E_USER_ERROR);
    }

    /**
     * 设置数据库字符编码
     */
    protected function setCharset()
    {
        $result = mysqli_set_charset(self::$link, $this->config['charset']);

        $result or trigger_error('设置数据库字符编码失败: ' . mysqli_error(self::$link), E_USER_ERROR);
    }
}