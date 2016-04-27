<?php
/**
 * Date: 2016/4/26
 * Time: 16:34
 */

date_default_timezone_set('UTC');

if (!defined('DS')) {
    define('DS', '/');
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__FILE__));
}

if (!defined('PATH_LIB')) {
    define('PATH_LIB', BASE_PATH . DS . 'lib');
}

if (!defined('PATH_LOG')) {
    define('PATH_LOG', BASE_PATH . DS . 'logs');
}

if (!defined('PATH_CONF')) {
    define('PATH_CONF', BASE_PATH . DS . 'config');
}

require_once BASE_PATH . '/vendor/autoload.php';

function autoload_function($class)
{
    $class = implode(DS, explode('\\', $class));
    $path  = BASE_PATH . DS . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
}

spl_autoload_register('autoload_function');

\lib\Config::set('mysql', require_once PATH_CONF . '/crawler.php');