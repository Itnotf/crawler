<?php

/**
 * //F::$f->Database_Table->insert();
 *
 * Date: 2016/4/26
 * Time: 19:11
 */
class Factory
{
    private static $instances = array();

    private static $f = NULL;

    public function __construct()
    {
        if (NULL === self::$f) {
            self::$f = new self();
        }
    }

    public function getInstance($name)
    {
        list($database, $table) = explode('_', $name);
        $key = $database . $table;

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new DB($database, $table);
        }

        return self::$instances[$key];
    }
    
    public function __get($name)
    {
        return $this->getInstance($name);
    }

}