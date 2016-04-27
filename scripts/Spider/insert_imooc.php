<?php
/**
 *
 * Date: 2016/4/26
 * Time: 18:47
 */

use \lib\Factory;

require_once dirname(__FILE__) . '/../../autoload.php';

$project_id = 1;

$insert = [
    'id'          => $project_id,
    'pattern'     => '/.*/is',
    'description' => '慕课网用户详情',
];

$replace = [
    'pattern'     => '/.*/is',
    'description' => '慕课网用户详情',
];


Factory::$f->crawler->insertReplace('project', $insert, $replace);

for ($i = 0; $i < 3000000; $i++) {
    $inserts[] = [
        'url'        => "http://www.imooc.com/u/$i/courses",
        'project_id' => $project_id,
        'tag'        => "imooc_$i",
    ];
    if (count($inserts) >= 10000) {
        Factory::$f->crawler->insert('task', $inserts);
        $inserts = [];
    }
}
