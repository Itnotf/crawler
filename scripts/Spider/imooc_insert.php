<?php
/**
 * 将要爬取的信息每10000行生成一个 csv 文件, 写入data task 文件夹
 * url: http://www.imooc.com/u/1/courses
 * filename: {project_name}_{project_id}_{num}
 *
 * File: imooc_insert.php
 * Time: 2016/4/29 19:20
 * Author: No One
 */
define('TASK_NAME', 'imooc');
define('TASK_ID', 1);

require_once dirname(__FILE__) . '/../../autoload.php';

$file_name_prefix = PATH_TASK . TASK_NAME . '_' . TASK_ID . '_';

$rand = md5(date('Ymd') . rand());
for ($i = 0; $i < 20000; $i++) {
    $arr       = [
        $i,
        "http://www.imooc.com/u/{$i}/courses",
        TASK_ID,
    ];
    $file_name = $file_name_prefix . floor($i / 10000) . $rand;
    file_put_contents($file_name, implode(',', $arr) . "\n", FILE_APPEND);
}

