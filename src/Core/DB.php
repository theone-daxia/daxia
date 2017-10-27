<?php

namespace Daxia\Core;

class DB
{
    private $mysqli;

    public function __construct()
    {
        $this->connect();
    }

    public function connect()
    {
        $db_config = Config::get('database');
        $mysqli = new \mysqli(
            $db_config['db_host'],
            $db_config['db_user'],
            $db_config['db_pwd'],
            $db_config['db_name']
        );
        if (mysqli_connect_error()) {
            return mysqli_connect_error();
        }
        $this->mysqli = $mysqli;
        return $this;
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
        $this->mysqli->set_charset("utf8"); // 设置编码
        $result = $this->mysqli->query($sql);
        if ($result === false) {
            return $this->mysqli->error;
        }
        return $result->fetch_array();
    }

    /**
     * 关闭链接
     */
    public function close()
    {
        $this->mysqli->colse();
    }
}
