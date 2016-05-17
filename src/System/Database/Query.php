<?php namespace System\Database;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use BadMethodCallException;

class Query
{
    /**
     * 数据库链接句柄
     *
     * @var
     */
    public $connection;

    /**
     * where 条件
     *
     * @var string
     */
    protected $where = '';

    /**
     * limit 条件
     *
     * @var string
     */
    protected $forPage = '';

    /**
     * order by 条件
     *
     * @var string
     */
    protected $orderBy = '';

    /**
     * group by 条件
     *
     * @var string
     */
    protected $groupBy = '';

    /**
     * 默认每页返回 20 条数据
     *
     * @var int
     */
    private $pageNum = 20;

    /**
     * 查询资源
     *
     * @var
     */
    public $resource;

    /**
     * 数据库操作初始化
     *
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * 获取 Sql 条件语句部分
     *
     * @return string
     */
    public function getCondation()
    {
        return $this->where . $this->groupBy . $this->forPage . $this->orderBy;
    }

    /**
     * 字段类型处理
     *
     * @param null $value
     *
     * @return null|string
     */
    public function dealFieldType($value = null)
    {
        return is_string($value) ? ("'" . $value . "' ") : $value;
    }

    /**
     * 获取 whereIn 字段条件方法
     *
     * @param string $params
     *
     * @return string
     */
    public function getWhereInString($params = '')
    {
        if (is_array($params)) {
            $result = '';

            foreach ($params as $param) {
                $result .= $this->dealFieldType($param) . ',';
            }

            $result = rtrim($result, ',');

            return $result;
        }

        return $params;
    }

    /**
     * where and 条件组装
     *
     * @return $this
     */
    public function where()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' and');
                break;
            case 1 :
                $where .= ' id=' . $this->dealFieldType($params[0]);
                break;
            case 2 :
                $where .= $params[0] . '=' . $this->dealFieldType($params[1]);
                break;
            case 3 :
                $where .= $params[0] . ' ' . $params[1] . ' ' . $this->dealFieldType($params[2]);
        }

        $this->where = $where;

        return $this;
    }

    /**
     * where or 条件组装
     *
     * @return $this
     */
    public function orwhere()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' or ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' or');
                break;
            case 1 :
                $where .= ' id=' . $this->dealFieldType($params[0]);
                break;
            case 2 :
                $where .= $params[0] . '=' . $this->dealFieldType($params[1]);
                break;
            case 3 :
                $where .= $params[0] . $params[1] . $this->dealFieldType($params[2]);
        }

        $this->where = $where;

        return $this;
    }

    /**
     * where In 条件组装
     *
     * @return $this
     */
    public function whereIn()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' and');
                break;
            case 1 :
                $where .= ' id in(' . $this->getWhereInString($params[0]) . ') ';
                break;
            default :
                $where .= $params[0] . ' in(' . $this->getWhereInString($params[1]) . ') ';
        }

        $this->where = $where;

        return $this;
    }

    /**
     * where not in 条件组装
     *
     * @return $this
     */
    public function whereNotIn()
    {
        $params = func_get_args();

        $where = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 0 :
                $where = rtrim($where, ' and');
                break;
            case 1 :
                $where .= ' id not in(' . $this->getWhereInString($params[0]) . ') ';
                break;
            default :
                $where .= $params[0] . ' not in(' . $this->getWhereInString($params[1]) . ') ';
        }

        $this->where = $where;

        return $this;
    }

    /**
     * limit 条件组装
     *
     * @return $this
     */
    public function forPage()
    {
        $params = func_get_args();

        $forPage = '';

        switch (func_num_args()) {
            case 1 :
                $forPage = ' limit ' . ($params[0] - 1) * $this->pageNum . ',' . $this->pageNum;
                break;
            case 2 :
                $forPage = ' limit ' . ($params[0] - 1) * $params[1] . ',' . $params[1];
        }

        $this->forPage = $forPage ? : $this->forPage;

        return $this;
    }

    /**
     * order by 条件组装
     *
     * @return $this
     */
    public function orderBy()
    {
        $params = func_get_args();

        $orderBy = $this->orderBy ? $this->orderBy . ' ,' : ' order by ';

        switch (func_num_args()) {
            case 0 :
                $orderBy = rtrim($orderBy, ' ,');
                break;
            case 1 :
                $orderBy .= $params[0] . ' asc ';
                break;
            case 2 :
                $orderBy .= $params[0] . ' ' . $params[1];
        }

        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * group by 条件组装
     *
     * @return $this
     */
    public function groupBy()
    {
        $params = func_get_args();

        switch (func_num_args()) {
            case 0 :
                $groupBy = ' group by id ';
                break;
            case 1 :
                $groupBy = ' group by ' . $params[0];
                break;
            default :
                $groupBy = ' group by ' . $params[0] . ',' . $params[1];
        }

        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * between and 条件组装
     *
     * @return $this
     */
    public function betweenAnd()
    {
        $params = func_get_args();

        $betweenAnd = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 3 :
                $betweenAnd .= $params[0] . ' between ' . $params[1] . ' and ' . $params[2];
                break;
            case 2 :
                $betweenAnd .= ' id between ' . $params[0] . ' and ' . $params[1];
                break;
            default :
                $betweenAnd = rtrim($betweenAnd, ' and');
                $betweenAnd = ltrim($betweenAnd, ' where');
        }

        $this->where = $betweenAnd;

        return $this;
    }

    /**
     * not between and 条件组装
     *
     * @return $this
     */
    public function notBetweenAnd()
    {
        $params = func_get_args();

        $notBetweenAnd = $this->where ? $this->where . ' and ' : ' where ';

        switch (func_num_args()) {
            case 3 :
                $notBetweenAnd .= $params[0] . ' not between ' . $params[1] . ' and ' . $params[2];
                break;
            case 2 :
                $notBetweenAnd .= ' id not between ' . $params[0] . ' and ' . $params[1];
                break;
            default :
                $notBetweenAnd = rtrim($notBetweenAnd, ' and');
                $notBetweenAnd = ltrim($notBetweenAnd, ' where');
        }

        $this->where = $notBetweenAnd;

        return $this;
    }

    /**
     * 执行数据库操作命令
     *
     * @param string $sql
     */
    public function query($sql = '')
    {
        $result = mysqli_query($this->connection, $sql);

        $result or trigger_error('数据库操作失败：' . mysqli_error($this->connection), E_USER_ERROR);

        $this->resource = $result;
    }

    /**
     * 获取受影响行数
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return mysqli_affected_rows($this->connection);
    }

    /**
     * 自动调用方法处理
     *
     * @param $method
     * @param $paramters
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $paramters)
    {
        if (!method_exists($this, $method)) {
            // 获取类名称
            $className = get_class($this);

            // 抛出异常
            throw new BadMethodCallException("Call To Undefined Method {$className}::{$method}()");
        }
    }
}
