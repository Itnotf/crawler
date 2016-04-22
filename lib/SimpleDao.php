<?php

/**
 * Date: 2016/4/21
 * Time: 23:18
 */
class SimpleDao
{
    private        $_table = null;
    private static $_con   = null;

    public function __construct($host, $user, $password, $database_name)
    {
        if (null == self::$_con) {
            self::$_con = mysqli_connect($host, $user, $password);
            if (self::$_con == false) {
                echo("connect to db server failed.");
                self::$_con = null;
                return;
            }
            $this->useDatabase($database_name);
        }
    }

    public function useDatabase($database_name)
    {
        mysqli_select_db(self::$_con, $database_name);
    }

    public function Table($table_name)
    {
        $this->_table = $table_name;
        return $this;
    }

    public function query($sql)
    {
        $result = mysqli_query(self::$_con, $sql);
        $ret    = [];
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $ret[] = $row;
            }
        }
        return $ret;
    }

    public function get($where = null)
    {
        $sql = "select * from " . $this->_table;
        //$sql = $sql.$this->_getWhereString($where);
        //echo "[get]".$sql."<br>";
        return $this->query($sql);
    }

    public function insert($params)
    {
        if ($params == null || !is_array($params)) {
            return false;
        }
        $keys   = $this->_getParamKeyString($params);
        $values = $this->_getParamValString($params);
        $sql    = "insert into " . $this->_table . "(" . $keys . ") values(" . $values . ")";
        //echo "[insert]" . $sql . "<br>";
        $result = mysqli_query(self::$_con, $sql);
        if (!$result) {
            return false;
        }
        return mysqli_insert_id(self::$_con);
    }

    public function update($params, $where = null)
    {
        if ($params == null || !is_array($params)) {
            return -1;
        }
        $upvals = $this->_getUpdateString($params);
        $wheres = $this->_getWhereString($where);
        $sql    = "update " . $this->_table . " set " . $upvals . " " . $wheres;
        //echo "[update]".$sql."<br>";
        $result = mysqli_query(self::$_con, $sql);
        if (!$result) {
            return false;
        }
        return mysqli_affected_rows(self::$_con);
    }

    public function delete($where)
    {
        $wheres = $this->_getWhereString($where);
        $sql    = "delete from " . $this->_table . $wheres;
        //echo "[delete]".$sql."<br>";
        $result = mysqli_query(self::$_con, $sql);
        if (!$result) {
            return false;
        }
        return mysqli_affected_rows(self::$_con);
    }

    protected function _getParamKeyString($params)
    {
        $keys = array_keys($params);
        return implode(",", $keys);
    }

    protected function _getParamValString($params)
    {
        $values = array_values($params);
        return "'" . implode("','", $values) . "'";
    }

    private function _getUpdateString($params)
    {
        //echo "_getUpdateString";
        $sql = "";
        if (is_array($params)) {
            $sql = $this->_getKeyValString($params, ",");
        }
        return $sql;
    }

    private function _getWhereString($params)
    {
        //echo "_getWhereString";
        $sql = "";
        if (is_array($params)) {
            $sql   = " where ";
            $where = $this->_getKeyValString($params, " and ");
            $sql   = $sql . $where;
        }
        return $sql;
    }

    private function _getKeyValString($params, $split)
    {
        $str = "";
        if (is_array($params)) {
            $paramArr = array();
            foreach ($params as $key => $val) {
                $valstr = $val;
                if (is_string($val)) {
                    $valstr = "'" . $val . "'";
                }
                $paramArr[] = $key . "=" . $valstr;
            }
            $str = $str . implode($split, $paramArr);
        }
        return $str;
    }

    public function release()
    {
        mysqli_close(self::$_con);
    }
}

function T($table)
{
    $simple_dao = new SimpleDao('localhost', 'root', 'root', 'crawler');
    return $simple_dao->table($table);
}