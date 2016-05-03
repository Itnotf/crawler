<?php
/**
 * 5分钟执行一次
 * 定期处理并删除task文件夹下的任务文件
 * 1.遍历 task 文件夹下的修改时间大于5分钟的文件(认为该文件已经写入完)
 * 2.根据项目Id获取正则表达式
 * 3.多进程或者多线程curl 获取html,并根据正则表达式匹配得到结果
 * 4.结果写入文件,10000 个文件打包成一个zip文件,调用系统tar命令
 *
 * Date: 2016/4/26
 * Time: 18:56
 */

use lib\Crawler;

require_once dirname(__FILE__) . '/../../autoload.php';

$crawler = new Crawler();

$handle = opendir(PATH_TASK);
while (FALSE !== ($file = readdir($handle))) {
    if (strpos($file, '.') !== 0) {
        $task_files[] = $file;
    }
}
closedir($handle);

foreach ($task_files as $task_file) {
    $tasks = [];
    $file  = fopen(PATH_TASK . $task_file, 'r');
    while (!feof($file)) {
        $tasks[] = fgetcsv($file);
    }
    fclose($file);
    getResults($crawler, $tasks);
    //unlink(PATH_TASK . $task_file);
}

function getResults(Crawler $crawler, $tasks)
{
    $t = xdebug_time_index();
    foreach ($tasks as $task) {
        $urls[] = $task[1];
        if (count($urls) > 1000) {
            $responses = $crawler->getMultiResponse($urls);
            echo xdebug_time_index() - $t;
            $urls = [];
            exit;
        }
    }
}
