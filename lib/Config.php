<?php

/**
 * Date: 2016/4/27
 * Time: 16:21
 */
namespace lib;

class Config
{

    private static $config = [];

    public static function get($key, $default = NULL)
    {
        $config = self::$config;

        if (is_null($key) || $key === '') {
            return $config;
        }

        $path = explode('.', $key);
        foreach ($path as $key) {
            $key = trim($key);
            if (empty($config) || !isset($config[$key])) {
                return $default;
            }
            $config = $config[$key];
        }

        return $config[$key];
    }

    public static function set($key, $value)
    {
        $config   = &self::$config;
        $segments = explode('.', $key);
        $key      = array_pop($segments);
        foreach ($segments as $segment) {
            if (!isset($config[$segment])) {
                $config[$segment] = array();
            }
            $config = &$config[$segment];
        }
        $config[$key] = $value;
    }

    public static function add($config)
    {
        self::$config = self::_merge($config, self::$config);
    }

    private static function _merge($source, $target)
    {
        foreach ($source as $key => $val) {
            if (!is_array($val) || !isset($target[$key])) {
                $target[$key] = $val;
            } else {
                $target[$key] = self::_merge($val, $target[$key]);
            }
        }
        return $target;
    }

}