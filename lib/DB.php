<?php

/**
 * Date: 2016/4/26
 * Time: 18:58
 */
namespace lib;

class DB extends \medoo
{
    public function __construct($database)
    {
        parent::__construct(
            [
                'database_type' => 'mysql',
                'database_name' => $database,
                'server'        => Config::get('mysql')['host'],
                'username'      => Config::get('mysql')['username'],
                'password'      => Config::get('mysql')['password'],
                'charset'       => Config::get('mysql')['charset'],
                'prefix'        => Config::get('mysql')['prefix'],
                'option'        => [
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                ],
            ]
        );
    }

    public function insert($table, $data)
    {
        $this->pdo->exec($this->genInsertSql($table, $data));

        return $this->pdo->lastInsertId();
    }

    public function insertReplace($table, $data, $replace)
    {
        $sql = $this->genInsertSql($table, $data);
        $sql .= ' on duplicate key update ' . preg_replace('@^\s*SET\s*@', '', $this->genUpdateSql($replace));
        $this->pdo->exec($sql);

        var_dump($sql);
        var_dump($this->pdo->errorInfo());

        return $this->pdo->lastInsertId();
    }

    public function genInsertSql($table, $data)
    {
        $values = '';
        if (!isset($data[0])) {
            $data = array($data);
        }
        $columns = array_map([$this, 'column_quote'], array_keys($data[0]));
        foreach ($data as $v) {
            $values .= '(' . implode(',', array_map([$this, 'quote'], $v)) . '),';
        }

        $values = rtrim($values, ',');

        return 'INSERT INTO "' . $this->prefix . $table . '" (' . implode(', ', $columns) . ') VALUES ' . $values;
    }

    public function genUpdateSql($data)
    {
        $sql = '';
        foreach ($data as $name => $val) {
            $name = $this->column_quote($name);
            $val  = $this->quote($val);
            $sql .= "$name=$val,";
        }
        return ' SET ' . trim($sql, ',');
    }
}