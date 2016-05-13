<?php namespace System\Database;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class Model
{
    /**
     * 数据库链接名称
     */
    const DATABASE_CONNECTION = 'default';

    /**
     * 表名称
     */
    const TABLE_NAME = '';

    /**
     * 数据库操作类对象
     *
     * @var
     */
    protected static $resolver;

    /**
     * 表字段
     *
     * @var array
     */
    public $columns = array();

    /**
     * 数据库链接对象
     *
     * @var
     */
    protected $connection;

    public function __construct()
    {
        // 获取数据库操作类对象
        self::$resolver = new Connection(static::DATABASE_CONNECTION);
    }

    /**
     * 获取数据库操作类对象
     *
     * @return Connection
     */
    public static function getResolver()
    {
        return self::$resolver;
    }

    /**
     * 实例查询操作对象
     *
     * @return Builder
     */
    protected function newQuery()
    {
        // 获取查询操作对象
        $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());

        return $builder;
    }

    /**
     * 构建结果操作对象
     *
     * @param $query
     *
     * @return Builder
     */
    protected function newEloquentBuilder($query)
    {
        return new Builder($query, static::TABLE_NAME, $this->columns);
    }

    /**
     * 构建查询对象
     *
     * @return Query
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        return new Query($conn);
    }

    /**
     * 获取数据库链接对象
     *
     * @return mixed
     */
    protected function getConnection()
    {
        return $this->connection = static::resolveConnection();
    }

    /**
     * 解决数据库链接
     *
     * @return mixed
     */
    protected static function resolveConnection()
    {
        return self::$resolver->connect();
    }

    /**
     * 操作方法调用处理
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        $query = $this->newQuery();

        return call_user_func_array(array($query, $method), $params);
    }

    /**
     * 静态方法调用处理
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = new static;

        return call_user_func_array(array($instance, $method), $params);
    }
}