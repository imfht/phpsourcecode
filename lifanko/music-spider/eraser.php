<?php
/**
 * Created by PhpStorm.
 * User: lifanko  lee
 * Date: 2017/10/1
 * Time: 21:25
 */
require 'vendor/autoload.php';

use Metowolf\Meting;

switch (get('o')) {
    case 'search':
        $keyword = get('k');
        $source = get('s');
        $api = new Meting($source);
        $data = $api->format(true)->search($keyword, ['limit' => 15]);

        $music = json_decode($data, true);

        $songs = [];
        $first = true;
        $url = $cover = $lrc = '';
        foreach ($music as $res) {
            $name = $res['name'];
            $artist = implode(',', $res['artist']);

            if ($first) {
                $url = json_decode($api->url($res['url_id']), true)['url'];

                $size = 400;
                if ($source == 'tencent') {
                    $size = 500;
                }
                $cover = json_decode($api->pic($res['pic_id'], $size, 'https://cdn.lifanko.cn/img/nocover.jpg'), true)['url'];
                $lrc = json_decode($api->lyric($res['lyric_id']), true)['lyric'];

                if (!empty($url)) {
                    $first = false;
                }
            } else {
                $url = $res['url_id'];
                $cover = $res['pic_id'];
                $lrc = $res['lyric_id'];
            }

            if (!empty($url)) {
                $buffer = ['name' => $name, 'artist' => $artist, 'url' => $url, 'cover' => $cover, 'lrc' => $lrc];

                array_push($songs, $buffer);
            }
        }

        $list = json_encode($songs);
        echo $list;

        // 搜索有效时才对关键词进行记录
        if (count($songs)) {
            record($keyword, $source);
        }

        break;
    case 'sd':
        $api = new Meting(get('s'));
        $api->format(true);

        $songData = [];
        $songData['url'] = json_decode($api->url(get('url_id')), true)['url'];
        if ($songData['url'] == '') {
            $songData['url'] = 'fail';
        }
        $size = 400;
        if (get('s') == 'tencent') {
            $size = 500;
        }
        $songData['cover'] = json_decode($api->pic(get('pic_id'), $size, 'https://cdn.lifanko.cn/img/nocover.jpg'), true)['url'];
        $songData['lrc'] = json_decode($api->lyric(get('lyric_id')), true)['lyric'];

        $index = get('index');
        if (!empty($index)) {
            $index = $index > 9 ? $index : '0' . $index;
            echo $index . json_encode($songData);
        } else {
            echo json_encode($songData);
        }

        break;
    case 'hs':
        // 热搜词数量
        $max = get('max');

        $jsonHotSearch = saveInfo('hotSearch');

        if (!empty($jsonHotSearch)) {
            //解析为数组格式
            $arrHotSearch = json_decode($jsonHotSearch, true);

            // 按从多到少排序
            arsort($arrHotSearch);

            // 将关键词（键）保存为新数组
            $arrHotSearch = array_keys($arrHotSearch);

            $hotWordNum = count($arrHotSearch);
            // 最多显示$max个热搜词
            for ($i = 0; $i < ($hotWordNum > $max ? $max : $hotWordNum); $i++) {
                echo "<li>{$arrHotSearch[$i]}</li>";
            }
        } else {  //文件为空
            echo "<li>无</li>";
        }

        break;
    case 'pl':
        $list = saveInfo('playList');
        echo $list;

        break;
    default:
        echo 'Eraser Music';
}

function get($key, $value = '')
{
    return trim(isset($_GET[$key]) ? $_GET[$key] : $value);
}

function saveInfo($dir, $new = '')
{
    $filePath = $dir . '.txt';
    if (file_exists($filePath)) {
        // $new为空时是读取状态，不为空时为写入状态
        if (empty($new)) {
            $fp = fopen($filePath, "r");
            // 指定读取大小，这里把整个文件内容读取出来
            $str = fread($fp, filesize($filePath));
            fclose($fp);

            return $str;
        } else {
            $fp = fopen($filePath, "w");
            flock($fp, LOCK_EX);
            fwrite($fp, $new);
            flock($fp, LOCK_UN);
            fclose($fp);

            return true;
        }
    }

    return false;
}

function record($keyword, $source)
{
    $jsonHotSearch = saveInfo('hotSearch');

    if (!empty($jsonHotSearch)) {
        //解析为数组格式
        $arrHotSearch = json_decode($jsonHotSearch, true);
        //有记录则加一
        if (array_key_exists($keyword, $arrHotSearch)) {
            $arrHotSearch[$keyword] += 1;

            //搜索最多的作为默认列表
            if ($arrHotSearch[$keyword] == max($arrHotSearch)) {
                $list = getListInfo($source, $keyword);
                saveInfo('playList', $list);
            }
        } else {
            //无记录则创建
            $arrHotSearch[$keyword] = 1;
        }

        $jsonHotSearch = json_encode($arrHotSearch);
    } else {
        //文件为空
        $arrHotSearch = [$keyword => 1];
        $jsonHotSearch = json_encode($arrHotSearch);

        $list = getListInfo($source, $keyword);
        saveInfo('playList', $list);
    }

    saveInfo('hotSearch', $jsonHotSearch);
}

function getListInfo($source, $keyword)
{
    $api = new Meting($source);
    $data = $api->format(true)->search($keyword, ['limit' => 15]);

    $music = json_decode($data, true);

    $songs = [];
    foreach ($music as $res) {
        $name = $res['name'];
        $artist = implode(',', $res['artist']);

        $url = json_decode($api->url($res['url_id']), true)['url'];

        $size = 400;
        if ($source == 'tencent') {
            $size = 500;
        }
        $cover = json_decode($api->pic($res['pic_id'], $size, 'https://cdn.lifanko.cn/img/nocover.jpg'), true)['url'];
        $lrc = json_decode($api->lyric($res['lyric_id']), true)['lyric'];

        if (!empty($url)) {
            $buffer = ['name' => $name, 'artist' => $artist, 'url' => $url, 'cover' => $cover, 'lrc' => $lrc];

            array_push($songs, $buffer);
        }
    }

    return json_encode($songs);
}
