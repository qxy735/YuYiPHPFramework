<?php namespace System\Database;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

class Builder
{
    /**
     * 查询对象
     *
     * @var
     */
    protected $query;

    /**
     * sql 语句
     *
     * @var string
     */
    protected $sql = '';

    /**
     * 表名
     *
     * @var string
     */
    protected $table = '';

    /**
     * 表字段信息
     *
     * @var array
     */
    protected $columns;

    /**
     * 查询结果操作初始化
     *
     * @param $query
     * @param $table
     * @param $columns
     */
    public function __construct($query, $table, array $columns = array())
    {
        $this->query = $query;

        $this->table = $this->getConfig('prefix') . $table;

        $this->columns = $columns ? implode(',', $columns) : '*';
    }

    /**
     * 获取数据库链接配置
     *
     * @param $name
     *
     * @return mixed
     */
    public function getConfig($name)
    {
        return array_get(Model::getResolver()->config, $name, '');
    }

    /**
     * 设置表名
     *
     * @param $tableName
     */
    public function setTableName($tableName)
    {
        $this->table = $this->getConfig('prefix') . $tableName;
    }

    /**
     * 获取表名
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * 获取 Sql 语句
     *
     * @return string
     */
    public function toSql()
    {
        return $this->sql;
    }

    /**
     * 获取数据列表信息
     *
     * @param array $fields
     *
     * @return array
     */
    public function get($fields = [])
    {
        $field = $fields ? implode(',', $fields) : $this->columns;

        $sql = "select {$field} from " . $this->table . $this->query->getCondation();

        $this->sql = $sql;

        $this->query->query($sql);

        return $this->returnData($fields);
    }

    /**
     * 获取第一条数据信息
     *
     * @param $fields
     *
     * @return mixed
     */
    public function first($fields = [])
    {
        $field = $fields ? implode(',', $fields) : $this->columns;

        $sql = "select {$field} from " . $this->table . $this->query->getCondation();

        $this->sql = $sql;

        $this->query->query($sql);

        return array_get($this->returnData(), 0, array());
    }

    /**
     * 获取单条数据记录
     *
     * @param null  $id
     * @param array $fields
     *
     * @return array
     */
    public function find($id = null, $fields = [])
    {
        if (is_null($id)) {
            return array();
        }

        $this->query->where($id);

        $field = $fields ? implode(',', $fields) : $this->columns;

        $sql = "select {$field} from " . $this->table . $this->query->getCondation();

        $this->sql = $sql;

        $this->query->query($sql);

        return $this->returnData($fields);
    }

    /**
     * 获取数据总记录数
     *
     * @return null
     */
    public function count()
    {
        $sql = 'select count(1) as count from ' . $this->table . $this->query->getCondation();

        $this->sql = $sql;

        $this->query->query($sql);

        return array_get($this->returnData(), '0.count', 0);
    }

    /**
     * 添加数据方法
     *
     * @param null $data
     *
     * @return bool|int
     */
    public function save($data = null)
    {
        if (is_null($data)) {
            return false;
        }

        $sql = 'insert into ' . $this->table . ' set ' . $this->getUpdateFiled($data);

        $this->sql = $sql;

        $this->query->query($sql);

        return $this->query->getAffectedRows();
    }

    /**
     * 添加数据方法
     *
     * @param null $data
     *
     * @return bool|int
     */
    public function create($data = null)
    {
        return $this->save($data);
    }

    /**
     * 更新数据信息
     *
     * @param null $data
     *
     * @return bool|int
     */
    public function update($data = null)
    {
        if (is_null($data)) {
            return false;
        }

        $sql = 'update ' . $this->table . ' set ' . $this->getUpdateFiled($data) . $this->query->getCondation();

        $this->sql = $sql;

        $this->query->query($sql);

        return $this->query->getAffectedRows();
    }

    /**
     * 删除数据信息
     *
     * @return int
     */
    public function delete()
    {
        $sql = 'delete from ' . $this->table . $this->query->getCondation();

        $this->sql = $sql;

        $this->query->query($sql);

        return $this->query->getAffectedRows();
    }

    /**
     * 获取更新或添加字段信息
     *
     * @param $data
     *
     * @return string
     */
    private function getUpdateFiled($data)
    {
        $field = '';

        foreach ($data as $key => $value) {
            $field .= $key . '=' . $this->query->dealFieldType($value) . ', ';
        }

        return rtrim($field, ', ');
    }

    /**
     * 获取查询结果
     *
     * @return array
     */
    private function returnData()
    {
        $data = array();

        while ($row = mysqli_fetch_assoc($this->query->resource)) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * 执行对数据库的增删改查操作命令
     *
     * @param string $sql
     *
     * @return array|int
     */
    public function select($sql = '')
    {
        $result = mysqli_query($this->query->connection, $sql);

        $result or trigger_error('数据库操作失败：' . mysqli_error($this->query->connection), E_USER_ERROR);

        $data = array();

        if (is_bool($result)) {
            return mysqli_affected_rows($this->query->connection);
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * 自动处理调用方法
     *
     * @param $method
     * @param $paramters
     *
     * @return $this
     */
    public function __call($method, $paramters)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}($paramters);
        }

        call_user_func_array(array($this->query, $method), $paramters);

        return $this;
    }
}