<?php

namespace Daxia\Core;

class DB
{
    private $conn;
    private static $instance = null;

    public $sql = '';

    // Active Record variables
    public $ar_select               = array();
    public $ar_distinct             = FALSE;
    public $ar_from                 = array();
    public $ar_join                 = array();
    public $ar_where                = array();
    public $ar_like                 = array();
    public $ar_groupby              = array();
    public $ar_having               = array();
    public $ar_keys                 = array();
    public $ar_limit                = FALSE;
    public $ar_offset               = FALSE;
    public $ar_order                = FALSE;
    public $ar_orderby              = array();
    public $ar_set                  = array();
    public $ar_wherein              = array();
    public $ar_aliased_tables       = array();
    public $ar_store_array          = array();

    public $result_array            = null;

    public function __construct()
    {
        $this->connect();
    }

    /**
     * 链接
     */
    public function connect()
    {
        $db_config = Config::get('database');
        $conn = new \mysqli(
            $db_config['db_host'],
            $db_config['db_user'],
            $db_config['db_pwd'],
            $db_config['db_name']
        );
        if (mysqli_connect_error()) {
            return mysqli_connect_error();
        }
        $this->conn = $conn;
    }

    /**
     * 获取db
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    /**
     * sql查询
     *
     * @param  $sql string 查询语句
     *
     * @return array
     */
    public function query($sql)
    {
        $this->conn->set_charset("utf8"); // 设置编码
        $result = $this->conn->query($sql);
        if ($result === false) {
            return $this->conn->error;
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * 关闭链接
     */
    public function close()
    {
        $this->conn->colse();
    }

    /**
     * 生成查询的 select 部分
     *
     * @param  $select sring 查询域
     *
     * @return object
     */
    public function select($select = '*')
    {
        if (is_string($select)) {
            $select = explode(',', $select);
        }

        foreach ($select as $val) {

            $val = trim($val);

            if (!empty($val)) {
                $this->ar_select[] = $val;
                // $this->ar_no_escape[] = $escape;
            }
        }

        return $this;
    }

    /**
     * 生成查询的 from 部分
     *
     * @param  $from sring 要查询的表
     *
     * @return object
     */
    public function from($from)
    {
        if (is_string($from)) {

            $from = trim($from);

            if (!empty($from)) {
                $this->ar_from[] = $from;
            }
        }

        return $this;
    }

    /**
     * 生成查询的 where 部分
     *
     * @param  $where array 查询条件
     *
     * @return object
     */
    public function where($key, $value = null, $type = 'AND')
    {
        if (!is_array($key)) {

            $key = array($key => $value);
        }

        foreach ($key as $k => $v) {

            $prefix = count($this->ar_where) == 0 ? '' : $type . ' ';

            if (is_null($v)) {

                $k .= ' IS NULL';
            } else {

                $k .= ' = ';
            }

            $this->ar_where[] = $prefix . $k . $v;
        }

        return $this;
    }

    /**
     * 生成查询的 group_by 部分
     *
     * @param  $by sring 分组
     *
     * @return object
     */
    public function group_by($by)
    {
        if (is_string($by)) {

            $by = explode(',', $by);
        }

        foreach ($by as $val) {

            $val = trim($val);

            if (!empty($val)) {

                $this->ar_groupby[] = $val;
            }
        }

        return $this;
    }

    /**
     * 生成查询的 order by 部分
     *
     * @param  $orderby sring 排序列
     * @param  $direction sring 排序方向 asc or desc
     *
     * @return object
     */
    public function order_by($orderby, $direction = '')
    {
        $condition = !empty(trim($direction)) &&
            !in_array(
                strtoupper(trim($direction)),
                ['ASC', 'DESC'],
                true
            );

        if ($condition) {

            $direction = 'ASC';
        }
        $this->ar_orderby[] = $orderby . ' ' . $direction;

        return $this;
    }

    /**
     * 生成查询的 limit 部分
     *
     * @param  $value int 查询域
     * @param  $value int 查询域
     *
     * @return object
     */
    public function limit($value, $offset = '')
    {
        $this->ar_limit = intval($value);

        if ($offset !== '') {

            $this->ar_offset = intval($offset);
        }

        return $this;
    }

    /**
     * 编写查询语句
     *
     * @return string
     */
    protected function _compile_select()
    {
        $sql = empty($this->ar_distinct) ? 'SELECT' : 'SELECT DISTINCT';

        if (count($this->ar_select) == 0) {

            $sql .= ' * ';

        } else {

            $sql .= ' ' . implode(', ', $this->ar_select);
        }

        // ----------------------------------------------------------------

        // Write the "FROM" portion of the query

        if (count($this->ar_from) > 0) {

            $sql .= "\nFROM ";

            $sql .= '(' . implode(', ', $this->ar_from) . ')';
        }

        // ----------------------------------------------------------------

        // Write the "JOIN" portion of the query

        if (count($this->ar_join) > 0) {

            $sql .= "\n";

            $sql .= implode("\n", $this->ar_join);
        }

        // ----------------------------------------------------------------

        // Write the "WHERE" portion of the query

        if (count($this->ar_where) > 0) {

            $sql .= "\nWHERE ";
        }

        $sql .= implode("\n", $this->ar_where);

        // ----------------------------------------------------------------

        // Write the "GROUP BY" portion of the query

        if (count($this->ar_groupby) > 0) {

            $sql .= "\nGROUP BY ";

            $sql .= implode(', ', $this->ar_groupby);
        }

        // ----------------------------------------------------------------

        // Write the "ORDER BY" portion of the query

        if (count($this->ar_orderby) > 0) {

            $sql .= "\nORDER BY ";
            $sql .= implode(', ', $this->ar_orderby);

            // if ($this->ar_order !== FALSE)
            // {
            //     $sql .= ($this->ar_order == 'desc') ? ' DESC' : ' ASC';
            // }
        }

        // ----------------------------------------------------------------

        // Write the "LIMIT" portion of the query

        if (is_numeric($this->ar_limit)) {

            $sql .= "\n";
            $sql .= "LIMIT " . $this->ar_limit;

            if ($this->ar_offset > 0) {

                $sql .= " OFFSET " . $this->ar_offset;
            }
        }

        $this->sql = $sql;
        return $this;
    }

    /**
     * 获取查询结果
     *
     * @return object
     */
    public function get()
    {
        $this->_compile_select();
        $result = $this->query($this->sql);
        $this->_reset_select();
        $this->result_array = $result;
        return $this;
    }

    /**
     * 返回单条结果
     *
     * @param  $n int 结果集下标
     *
     * @return array
     */
    public function row_array($n = 0)
    {
        $current_row = 0;

        if (isset($this->result_array[$n])) {

            $current_row = $n;
        }

        return $this->result_array[$current_row];
    }

    /**
     * 返回全部结果
     *
     * @return array
     */
    public function result_array()
    {
        return $this->result_array;
    }

    protected function _reset_select()
    {
        $ar_reset_items = array(
            'ar_select'         => array(),
            'ar_from'           => array(),
            'ar_join'           => array(),
            'ar_where'          => array(),
            'ar_like'           => array(),
            'ar_groupby'        => array(),
            'ar_having'         => array(),
            'ar_orderby'        => array(),
            'ar_wherein'        => array(),
            'ar_aliased_tables' => array(),
            'ar_no_escape'      => array(),
            'ar_distinct'       => FALSE,
            'ar_limit'          => FALSE,
            'ar_offset'         => FALSE,
            'ar_order'          => FALSE,
        );

        $this->_reset_run($ar_reset_items);
    }

    /**
     * Resets the active record values
     *
     * @param  $ar_reset_items array 需要重置的数组项
     *
     * @return void
     */
    protected function _reset_run($ar_reset_items)
    {
        foreach ($ar_reset_items as $item => $default_value) {

            if (!in_array($item, $this->ar_store_array)) {

                $this->$item = $default_value;
            }
        }
    }
}
