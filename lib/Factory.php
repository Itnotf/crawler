<?php

/**
 * 写个工厂数据库单例玩玩
 * F::$f->Database->insert();
 *
 * Date: 2016/4/26
 * Time: 19:11
 */
namespace lib;

class Factory
{
    private static $instances = array();

    public static $f = NULL;

    public static function init()
    {
        if (NULL === self::$f) {
            self::$f = new self();
        }
    }

    public function getInstance($database)
    {
        $key = $database;

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new DB($database);
        }

        return self::$instances[$key];
    }

    public function __get($database)
    {
        return $this->getInstance($database);
    }

}

Factory::init();