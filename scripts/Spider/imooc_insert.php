<?php
/**
 * 将要爬取的信息每10000行生成一个 csv 文件, 写入data task 文件夹
 *
 * File: imooc_insert.php
 * Time: 2016/4/29 19:20
 * Author: No One
 */
define('TASK_NAME', 'imooc');
define('TASK_ID', 1);

require_once dirname(__FILE__) . '/../../autoload.php';

$file_name = PATH_TASK . TASK_NAME . '_' . TASK_ID . '.csv';
$tasks     = [];
$base_url  = 'http://www.baidu.com/';

for ($i = 0, $count = 0; $i < 300000; $i++, $count++) {
    $arr = [$base_url . $i, TASK_ID];
    file_put_contents($file_name, implode(',', $arr), FILE_APPEND);
}

