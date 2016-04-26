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
    define('PATH_CONF', BASE_PATH . DS . 'conf');
}

require_once BASE_PATH . '/vendor/autoload.php';