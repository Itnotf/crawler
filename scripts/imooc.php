<?php
/**
 * Date: 2016/4/21
 * Time: 22:32
 */
error_reporting(E_ALL);

require_once dirname(__FILE__) . '../autoload.php';


//提供 url ，正则，插入数据库
//比较多进程，多线程，分布式爬虫
//将url与正则插入数据库，


$crawler = new Crawler();

for ($urls = [], $id = 3226881; $id > 0; $id--) {
    $urls[$id] = "http://www.imooc.com/u/{$id}/courses";
    if (count($urls) >= 40) {
        process($crawler, $urls);
        $urls = [];
    }
}

function process(Crawler $crawler, $urls)
{
    $responses = $crawler->getMultiResponse($urls);

    if (FALSE === $responses) {
        return FALSE;
    }

    foreach ($responses as $i => $response) {
        $name = $crawler->getMatch($response, '/user-name.*?\<span\>(.*?)\<\/span\>/is');
        if (FALSE === $name) {
            continue;
        }
        $sign_icon  = $crawler->getMatch($response, '/class=\"signicon_iden\"\>\s*(.*?)\s*\</is');
        $user_desc  = $crawler->getMatch($response, '/class=\"user-desc\"\>\s*(.*?)\s*\</is');
        $study_time = $crawler->getMatch($response, '/study-time.*?\<em\>(.*?)\<\/em\>/is');

        $integral   = $crawler->getMatch($response, '/integral.*?\<em\>(.*?)\<\/em\>/is');
        $experience = $crawler->getMatch($response, '/experience.*?\<em\>(.*?)\<\/em\>/is');
        $user_img   = $crawler->getMatch($response, '/class=\"user-pic\"\>.*?src=\"(.*?)\"/is');

        $insert = [
            'id'         => $i,
            'name'       => $name,
            'sign_icon'  => $sign_icon,
            'user_desc'  => $user_desc,
            'study_time' => $study_time,
            'integral'   => $integral,
            'experience' => $experience,
            'user_img'   => $user_img,
            //mysql 5.6 之后一个表里面支持多个timestamp的值为当前时间戳
        ];

        if (FALSE === T('imooc_user')->insert($insert)) {
            echo "failed to insert ";
            continue;
        }
    }
    return FALSE;
}






