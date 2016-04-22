<?php
/**
 * Date: 2016/4/9
 * Time: 9:07
 */

require_once "lib/Crawler.php";

$crawler = new Crawler();

$doulist_url = "https://www.douban.com/doulist/41073897/";

//获取豆列总数
$pattern_total    = "/全部.*?\<span\>\((\d+)\)\<\/span\>/is";
$crawler->pattern = $pattern_total;
$crawler->url     = $doulist_url;
$total_num        = $crawler->getResponse()->getResult();

if (false === $total_num) {
    die("failed to get total numbers!");
}

$doulist          = [];
$doulist_page     = 'https://www.douban.com/doulist/41073897/?sort=time&start=';
$pattern_page     = "/class=\"title\".*?href=\"(.*?)\"/is";
$crawler->pattern = $pattern_page;

//$total_num = 100;

for ($i = 0; $i <= $total_num; $i += 25) {
    $crawler->url = $doulist_page . $i;
    $doulist      = array_merge($doulist, $crawler->getAllResults());
    continue;
}

$results = [];

foreach ($doulist as $subject) {
    $details        = [];
    $details['url'] = $subject;
    if (strstr($subject, 'book')) {
        $crawler->url = $subject;
        $crawler->getResponse();
        $subject_patterns = [
            'author'           => '/class=\"pl\">\s*作者\<\/span\>:?\s*(.*?)\<br\/?\>/is',
            'publisher'        => '/class=\"pl\"\>出版社:\<\/span\>\s*(.*?)\<br\/?\>/is',
            'author_real_name' => '/class=\"pl\"\>原作名:\<\/span\>\s*(.*?)\<br\/?\>/is',
            'publish_date'     => '/class=\"pl\"\>出版年:\<\/span\>\s*(.*?)\<br\/?\>/is',
            'pages'            => '/class=\"pl\"\>页数:\<\/span\>\s*(.*?)\<br\/?\>/is',
            'img_url'          => '/(https:\/\/img3\.doubanio\.com\/mpic\/.*?)\"/is',
            'rate'             => '/rating_num.*?\>\s*(.*?)\s*\<\/strong\>/is',
            'about'            => '/class=\"intro\"\>(.*?)\<\/div\>/is',
        ];
        foreach ($subject_patterns as $k => $subject_pattern) {
            $crawler->pattern = $subject_pattern;
            $details['type']  = 'book';
            $details[$k]      = $crawler->getResult();

            if ('author' == $k) {
                $details[$k] = trim(preg_replace("/\<.*?\>/is", "", $details[$k]));
            }
        }
    } elseif (strstr($subject, 'movie')) {
        $crawler->url = $subject;
        $crawler->getResponse();
        $subject_patterns = [
            'director'     => '/v:directedBy\"\>(.*?)\<\/a\>/is',
            'screenwriter' => '/class=\'pl\'\>编剧\<\/span\>:.*?\<a.*?\>(.*?)\<\/a\>.*?\<br\/?\>/is',
            'actor'        => '/v:starring\"\>(.*?)\<\/a\>/is',
            'genre'        => '/v:genre\"\>(.*?)\<\/span\>/is',
            'release_date' => '/v:initialReleaseDate\"\s*content=\"(.*?)\"/is',
            'runtime'      => '/v:runtime\"\s*content=\"(.*?)\"/is',
            'img_url'      => '/id=\"mainpic\".*?(https:\/\/img3\.doubanio\.com.*?)\"/is',
            'rate'         => '/rating_num.*?\>\s*(.*?)\s*\<\/strong\>/is',
            'about'        => '/v:summary\"\>(.*?)\<\/div\>/is',
        ];
        foreach ($subject_patterns as $k => $subject_pattern) {
            $crawler->pattern = $subject_pattern;
            $details['type']  = 'movie';
            if ('actor' == $k || 'genre' == $k) {
                $details[$k] = $crawler->getAllResults();
            } else {
                $details[$k] = $crawler->getResult();
            }
        }
    }
    $results[] = $details;
}

file_put_contents('doulist.json', json_encode($results));

//var_dump(count($results));





