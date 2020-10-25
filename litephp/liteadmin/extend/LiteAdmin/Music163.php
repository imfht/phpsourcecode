<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/11
 * Time: 15:37
 */

namespace LiteAdmin;

use GuzzleHttp\Client;

class Music163
{
    public static function getMusic($id)
    {
        $uri = "https://music.163.com/song?id={$id}";

        $c = new Client();
        $html = $c->get($uri)->getBody()->getContents();

        $pattern = '/<script type="application\/ld\+json">([\s\S]+?)<\/script>/';
        preg_match($pattern,$html,$matches);

        $json = trim($matches[1]);
        $result = json_decode($json,true);

        $lrcuri = "http://music.163.com/api/song/lyric?id={$id}&lv=1&kv=1&tv=-1";
        $html = $c->get($lrcuri)->getBody()->getContents();
        $json_lrc = json_decode($html,true);

        $music = [
            'title'=>$result['title'],
            'author'=>$result['description'],
            'pic'=>$result['images'][0].'?param=130y130',
            'url'=>"http://music.163.com/song/media/outer/url?id={$id}.mp3",
            'lrc'=>$json_lrc['lrc']['lyric']
        ];
        return $music;
    }
}