<?php

/**
 * Date: 2016/4/9
 * Time: 9:08
 */
class Crawler
{
    const TOTAL_HANDLES = 60;

    public static $default_curl_opts = [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'User-Agent:Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0;Trident/4.0)',
        ],
    ];

    public function getResponse($url, $curl_opts = false)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, $curl_opts === false ? self::$default_curl_opts : $curl_opts);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function getMultiResponse($urls, $curl_opts = false)
    {
        $con = $responses = [];
        $mh  = curl_multi_init();
        foreach ($urls as $i => $url) {
            $con[$i] = curl_init($url);
            curl_setopt_array($con[$i], $curl_opts === false ? self::$default_curl_opts : $curl_opts);
            curl_multi_add_handle($mh, $con[$i]);
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            //一直为-1
            //if (curl_multi_select($mh) != -1) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            //}
        }

        foreach ($con as $i => $ch) {
            $responses[$i] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
        }
        curl_multi_close($mh);
        return $responses;
    }

    public function getAllResults($url, $pattern, $curl_opts = false)
    {
        return $this->getMatch($this->getResponse($url, $curl_opts), $pattern, true);
    }

    public function getResult($url, $pattern, $curl_opts = false)
    {
        return $this->getMatch($this->getResponse($url, $curl_opts), $pattern);
    }

    public function getMatch($response, $pattern, $match_all = false)
    {
        if (false === $match_all) {
            if (preg_match($pattern, $response, $matches)) {
                return isset($matches[1]) ? $matches[1] : false;
            }
        } else {
            if (preg_match_all($pattern, $response, $matches)) {
                return isset($matches[1]) ? $matches[1] : false;
            }
        }
        return false;
    }
}