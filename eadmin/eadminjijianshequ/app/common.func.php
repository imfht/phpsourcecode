<?php

use esclass\database;


function shildContent($content, $status, $type)
{
    $div = '<div class="bs-callout bs-callout-warning" id="callout-buttons-context-usage">
		    <h4>当前内容违纪已被屏蔽</h4>
		    <p>当前内容违纪已被屏蔽，如有疑问，可发起申诉。</p>
		  </div>';
    switch ($type) {
        case 0:
            if ($status == 0) {
                return $div;
            }
            return $content;
            break;
        case 1:
            if ($status == 0) {
                return $div;
            }
            return $content;
            break;
        case 2:
            if ($status == 0) {
                return $div;
            }
            return $content;
            break;
        default:
            return $content;
    }

}

function getImageToLocal($txt)
{


    $keywords = webconfig('web_url');
    $matches  = [];
    preg_match_all('/\<img.*?src\=\"(.*?)\"[^>]*>/i', htmlspecialchars_decode($txt), $matches);
    if (!is_array($matches)) return $txt;
    $uid = is_login();
    foreach ($matches[1] as $k => $v) {

        $url = trim($v, "\"'");

        $ext = '';
        if (strpos($url, $keywords) === false && (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://')) //非本站地址,需要下载图片
        {

            $arr = getImage($url);
            if ($arr['error'] == 0) {

                $txt = str_replace($url, $keywords . 'uploads/picture/cache/' . $uid . '/' . $arr['file_name'], $txt);


            }
        }
    }

    return $txt;
}

/* 
*功能：php完美实现下载远程图片保存到本地 
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
*当保存文件名称为空时则使用远程文件原来的名称 
*/
function getImage($url, $save_dir = '', $filename = '', $type = 0)
{

    if (trim($url) == '') {
        return ['file_name' => '', 'save_path' => '', 'error' => 1];
    }
    $uid = is_login();
    if (trim($save_dir) == '') {
        $save_dir = './uploads/picture/cache/' . $uid . '/';
    }
    if (trim($filename) == '') {//保存文件名

        $ext = strrchr($url, '.');
        if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png' && $ext != '.jpeg') {
            return ['file_name' => '', 'save_path' => '', 'error' => 3];
        }
        $filename = $uid . '_' . generate_password(8) . time() . $ext;

    }

    if (0 !== strrpos($save_dir, '/')) {
        $save_dir .= '/';
    }
    //创建保存目录 
    if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return ['file_name' => '', 'save_path' => '', 'error' => 5];
    }
    //获取远程文件所采用的方法  
    if ($type) {
        $ch      = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        curl_close($ch);
    } else {
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
    }
    //$size=strlen($img); 
    //文件大小  

    $fp2 = @fopen($save_dir . $filename, 'a');
    fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
    return ['file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0];
}


function deletearray($arr, $val)
{

    $key = array_search($val, $arr);

    if ($key !== false) {
        array_splice($arr, $key, 1);
        return $arr;
    }
    return false;
}

function user_has_focus($uid, $touid, $type = 3)
{

    $count = db('user_focus')->where(['type' => $type, 'uid' => $uid, 'sid' => $touid])->count();
    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}

function gettypemess($type)
{

    $topicarr   = [1, 3, 4, 14];
    $commentarr = [9, 10, 13];
    $userarr    = [7, 8];

    if (in_array($type, $topicarr)) {
        $data['name'] = '帖子消息';
        $data['type'] = 1;
    }
    if (in_array($type, $commentarr)) {
        $data['name'] = '评论消息';
        $data['type'] = 2;
    }
    if (in_array($type, $userarr)) {
        $data['name'] = '用户消息';
        $data['type'] = 3;
    }
    return $data;
}

function homeaction_log($uid, $type, $sid)
{
    $data['sid'] = $sid;
    $topicarr    = [1, 2, 3, 4, 11, 14];
    $commentarr  = [9, 10, 12, 13];
    $htarr       = [5, 6];
    $userarr     = [7, 8, 15, 16, 17, 18];


    if (in_array($type, $topicarr)) {
        $topicinfo        = db('topic')->where(['id' => $sid])->getRow();
        $data['describe'] = $topicinfo['title'];
        if ($type == 1) {//被赞
            point_controll($topicinfo['uid'], 'zantopic', $sid);
            sendsysmess('你的帖子"' . $topicinfo['title'] . '"被赞了' . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $topicinfo['id']]) . '">链接</a>', $uid, $topicinfo['uid'], $type);

        }
        if ($type == 3) {//被收藏
            point_controll($topicinfo['uid'], 'sctopic', $sid);
            sendsysmess('你的帖子"' . $topicinfo['title'] . '"被收藏了' . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $topicinfo['id']]) . '">链接</a>', $uid, $topicinfo['uid'], $type);
        }
        if ($type == 11) {//发帖

            point_controll($topicinfo['uid'], 'addtopic', $sid);
            //关注的话题加了帖子发送，关注的人发的帖子发送
            if (!empty($topicinfo['gidtext'])) {


                $nn = explode(',', $topicinfo['gidtext']);

                foreach ($nn as $key => $vo) {

                    $gid = db('group')->where(['name' => $vo])->value('id');

                    $uidarr = db('user_focus')->where(['type' => 2, 'sid' => $gid])->column('uid');

                    if ($uidarr) {

                        foreach ($uidarr as $k => $v) {
                            sendsysmess('你关注的话题有了新帖子"' . $topicinfo['title'] . '"' . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $topicinfo['id']]) . '">链接</a>', 0, $v, 1);
                        }


                    }

                }


            }

            $uidarr1 = db('user_focus')->where(['type' => 3, 'sid' => $topicinfo['uid']])->column('uid');
            if ($uidarr1) {

                foreach ($uidarr1 as $k => $v) {
                    sendsysmess('你关注的话题有了新帖子"' . $topicinfo['title'] . '"' . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $topicinfo['id']]) . '">链接</a>', 0, $v, 1);
                }


            }


        }

        if ($type == 4) {//被取消收藏
            point_controll($topicinfo['uid'], 'qxsctopic', $sid);
            sendsysmess('你的帖子"' . $topicinfo['title'] . '"被取消收藏了' . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $topicinfo['id']]) . '">链接</a>', $uid, $topicinfo['uid'], $type);
        }
        if ($type == 14) {//删帖
            point_controll($topicinfo['uid'], 'deletetopic', $sid);
            sendsysmess('你的帖子"' . $topicinfo['title'] . '"删除了', $uid, $topicinfo['uid'], $type);
        }


    }
    if (in_array($type, $commentarr)) {
        $commentinfo      = db('comment')->where(['id' => $sid])->getRow();
        $data['describe'] = '"' . msubstr(clearHtml(htmlspecialchars_decode($commentinfo['content'])), 0, 60) . '"';

        if ($type == 9) {//被赞
            point_controll($commentinfo['uid'], 'zancomment', $sid);

            sendsysmess('你的评论' . $data['describe'] . '被赞了' . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $commentinfo['fid']]) . '">链接</a>', $uid, $commentinfo['uid'], $type);
        }
        if ($type == 10) {//被反对
            point_controll($commentinfo['uid'], 'fanduicomment', $sid);
            sendsysmess('你的评论' . $data['describe'] . '被反对了' . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $commentinfo['fid']]) . '">链接</a>', $uid, $commentinfo['uid'], $type);
        }
        if ($type == 12) {//发评论

            point_controll($commentinfo['uid'], 'addcomment', $sid);
            //关注的人发的评论，关注的帖子有了新评论
            $uidarr = db('user_focus')->where(['type' => 3, 'sid' => $commentinfo['uid']])->column('uid');
            if ($uidarr) {

                foreach ($uidarr as $k => $v) {
                    sendsysmess('你关注的人有了新评论' . $data['describe'] . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $commentinfo['fid']]) . '">链接</a>', 0, $v, 1);
                }


            }

            $uidarr1 = db('user_focus')->where(['type' => 1, 'sid' => $commentinfo['fid']])->column('uid');
            if ($uidarr1) {

                foreach ($uidarr1 as $k => $v) {
                    sendsysmess('你关注的帖子有了新评论' . $data['describe'] . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $commentinfo['fid']]) . '">链接</a>', 0, $v, 1);
                }


            }


        }

        if ($type == 13) {//删评论
            point_controll($commentinfo['uid'], 'deletecomment', $sid);
            sendsysmess('你的评论' . $data['describe'] . '删除了', $uid, $commentinfo['uid'], $type);
        }


    }
    if (in_array($type, $htarr)) {
        $data['describe'] = db('group')->where(['id' => $sid])->value('name');
    }
    if (in_array($type, $userarr)) {
        $userinfo = db('user')->where(['id' => $sid])->getRow();


        $data['describe'] = $userinfo['nickname'];
        if ($type == 7) {//被关注
            point_controll($sid, 'focususer', $sid);
            sendsysmess('你被人关注啦' . '&nbsp;<a href="' . es_url('user/home', ['id' => $uid]) . '">链接</a>', $uid, $sid, $type);
        }
        if ($type == 8) {//被取消关注
            point_controll($sid, 'qxfocususer', $sid);
            sendsysmess('你被人取消关注啦' . '&nbsp;<a href="' . es_url('user/home', ['id' => $uid]) . '">链接</a>', $uid, $sid, $type);
        }
        if ($type == 16) {//邀请注册

            point_controll($uid, 'yaoqing', $sid);//邀请注册增加经验值
        }
        if ($type == 18) {//登录

            point_controll($uid, 'login', $sid);//登录增加经验值
        }
        if ($type == 17) {//注册

            point_controll($uid, 'register', $sid);//注册增加经验值
        }
    }


    $data['uid'] = $uid;

    $data['type'] = $type;

    $data['status'] = 1;

    $data['create_time'] = time();

    db('homeaction_log')->insert($data);

    return;
}

function replace_contentimage($content)
{

    $content = htmlspecialchars_decode($content);
    preg_match_all("/\<img.*?src\=\"(.*?)\"[^>]*>/i", $content, $images);

    if ($images) {

        foreach ($images[1] as $k => $v) {

            if (!preg_match("/^(http:\/\/|https:\/\/).*$/", $v)) {
                $content = str_replace($v, webconfig('web_url') . $v, $content);

            }


        }


        return $content;
    } else {
        return $content;
    }


}

//获取图片
function getcontentimage($content, $noreplace = true)
{

    preg_match_all("/\<img.*?src\=\"(.*?)\"[^>]*>/i", $content, $images);

    if ($images) {

        foreach ($images[1] as $k => $v) {


            if (strpos($v, '/addon/editor/static/kindeditor/plugins/emoticons/images/') !== false) {


                unset($images[1][$k]);
                unset($images[0][$k]);

            } else {

                if ($noreplace) {
                    if (!preg_match("/^(http:\/\/|https:\/\/).*$/", $v)) {
                        $images[0][$k] = str_replace($v, webconfig('web_url') . $v, $images[0][$k]);

                    }
                }


            }


        }


        return $images;
    } else {
        return '';
    }

}

//获取图片

function hasfocus($touid, $uid)
{

    $fs = DB('user_focus')->where(['uid' => $touid, 'sid' => $uid, 'type' => 3])->count();

    $gz = DB('user_focus')->where(['uid' => $uid, 'sid' => $touid, 'type' => 3])->count();

    if ($gz > 0) {
        //这个是我关注的人
        if ($fs > 0) {
            //这个是我的粉丝
            $hasfocus = 3;//好友
        } else {
            $hasfocus = 2;//关注

        }
    } else {
        if ($fs > 0) {
            //这个是我的粉丝
            $hasfocus = 1;//粉丝
        } else {
            //没有任何关系

        }


    }
    return $hasfocus;
}

function formatnumber($hits)
{
    $b = 1000;
    $c = 10000;
    if ($hits > $b) {
        if ($hits < $c) {

            $hits = floor($hits / $b) . '千';
        } else {

            $hits = (floor(($hits / $c) * 10) / 10) . '万';
        }
    } else {

    }
    return $hits;
}

function get_topiccatename($id)
{
    $info = DB('topiccate')->where(['id' => $id])->getRow();

    return $info['name'];


}

/**
 * 获取小组背景图片
 */
function get_groupbg($bg_id)
{
    $info = DB('Picture')->where(['id' => $bg_id])->field('path,url')->getRow();


    if ($info) {


        if (!empty($info['url']))  : return $info['url']; endif;

        if (!empty($info['path'])) : return WEB_PATH_PICTURE . $info['path']; endif;

    } else {
        return '__PUBLIC__/images/background_group.jpg';
    }


}

function get_wapgroupbg($bg_id)
{
    $info = DB('Picture')->where(['id' => $bg_id])->field('path,url')->getRow();


    if ($info) {


        if (!empty($info['url']))  : return $info['url']; endif;

        if (!empty($info['path'])) : return WEB_PATH_PICTURE . $info['path']; endif;

    } else {
        return '__PUBLIC__/images/group_bg.jpg';
    }


}

/**
 * 获取小组头像图片
 */
function get_groupavatar($cover_id)
{

    $info = DB('Picture')->where(['id' => $cover_id])->field('path,url')->getRow();


    if ($info) {


        if (!empty($info['url']))  : return $info['url']; endif;

        if (!empty($info['path'])) : return WEB_PATH_PICTURE . $info['path']; endif;

    } else {
        return '__PUBLIC__/images/default.png';
    }

}

/**
 * 字符串截取，支持中文和其他编码
 *
 * @static
 * @access public
 * @param string $str     需要转换的字符串
 * @param string $start   开始位置
 * @param string $length  截取长度
 * @param string $charset 编码格式
 * @param string $suffix  截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{


    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    //截取内容时去掉图片，仅保留文字
    $strlen = mb_strlen($str, 'utf-8');
    if ($strlen < $length) {
        $suffix = false;
    }
    return $suffix ? $slice . '...' : $slice;
}

function clearcontent($content)
{

    $content = htmlspecialchars_decode($content);


    $content = preg_replace("/&lt;/i", "<", $content);


    $content = preg_replace("/&gt;/i", ">", $content);

    $content = preg_replace("/&amp;/i", "&", $content);


    $content = strip_tags($content);
    return $content;
}


function clearHtml($content)
{
    $content = preg_replace('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i', "", $content);
    $content = preg_replace("/<a[^>]*>/i", "", $content);
    $content = preg_replace("/<\/a>/i", "", $content);
    $content = preg_replace("/<p>/i", "", $content);
    $content = preg_replace("/<\/p>/i", "", $content);
    $content = preg_replace("/<div[^>]*>/i", "", $content);
    $content = preg_replace("/<\/div>/i", "", $content);
    $content = preg_replace("/<!--[^>]*-->/i", "", $content);//注释内容
    $content = preg_replace("/style=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/class=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/id=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/lang=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/width=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/height=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/border=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/face=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/face=.+?['|\"]/", '', $content);//去除样式 只允许小写 正则匹配没有带 i 参数
    $content = preg_replace("/\n/is", '', $content);
    $content = preg_replace("/\r\n/is", '', $content);

    $content = preg_replace('/ |　/is', '', $content);
    $content = preg_replace('/&nbsp;/is', '', $content);
    $content = preg_replace('/&emsp;/is', '', $content);
    $content = strip_tags($content);
    return $content;
}

function cutstr_html($string, $length = 0, $ellipsis = '…')
{

    $string = strip_tags($string);
    $string = preg_replace("/\n/is", '', $string);
    $string = preg_replace("/\r\n/is", '', $string);

    $string = preg_replace('/ |　/is', '', $string);
    $string = preg_replace('/&nbsp;/is', '', $string);
    $string = preg_replace('/&emsp;/is', '', $string);

    if (mb_strlen($string, 'utf-8') <= $length) {
        $ellipsis = '';
    }
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $string);
    if (is_array($string) && !empty($string[0])) {
        if (is_numeric($length) && $length) {


            $string = join('', array_slice($string[0], 0, $length)) . $ellipsis;
        } else {
            $string = implode('', $string[0]);
        }
    } else {
        $string = '';
    }
    return $string;
}

function usercz($uid, $did, $type = 2, $cid = 1)
{

    $data['uid']         = $uid;
    $data['did']         = $did;
    $data['create_time'] = time();
    $data['type']        = $type;
    $data['cid']         = $cid;

    if ($cid == 3 && $type == 2 && $uid == $did) {
        return;
    } else {
        return database::getInstance()->table('usercz')->insert($data);
    }


}

/**
 * 获取文件url
 */
function get_file_url($id = 0)
{

    $info = database::getInstance()->table('File')->where(['id' => $id])->field('path,url')->getRow();

    if (!empty($info['url']))  : return $info['url']; endif;

    if (!empty($info['path'])) : return WEB_PATH_FILE . $info['path']; endif;

    return '暂无文件';
}

function get_file_name($id = 0)
{

    $info = database::getInstance()->table('File')->where(['id' => $id])->field('name')->getRow();


    return $info['name'];
}

/**
 * 获取图片url
 */
function get_picture_url($id = 0)
{

    $info = database::getInstance()->table('Picture')->where(['id' => $id])->field('path,url')->getRow();

    if (!empty($info['url']))  : return $info['url']; endif;


    if (!empty($info['path'])) : return WEB_PATH_PICTURE . $info['path']; endif;

    return detect_site_url() . '/public/images/onimg.png';
}


function getusernamebyid($uid)
{
    if ($uid == 0) {
        return '所有人';
    } else {
        $children = DB('user')->where(['id' => $uid])->getRow();

        return $children['nickname'];


    }


}

/*
 * 来判断导航链接内部外部从而生成新链接
 *
 *
 */
function getnavlink($link, $sid)
{
    if ($sid == 1) {

        $arr = explode(',', $link);

        $url = $arr [0];

        array_shift($arr);
        if (empty ($arr)) {

            $link = routerurl($url);
        } else {
            $m     = 1;
            $queue = [];
            foreach ($arr as $k => $v) {

                if ($m == 1) {
                    $n = $v;
                    $m = 2;
                } else {
                    $b          = $v;
                    $queue [$n] = $b;
                    $m          = 1;
                }
            }
            if (empty ($queue)) {
                $link = routerurl($url);
            } else {
                $link = routerurl($url, $queue);
            }
        }
    }

    return $link;
}

function routerurl($url, $arr = [])
{
    if (empty ($arr)) {
        $str = es_url($url);
    } else {
        $str = es_url($url, $arr);
    }

    $str = str_replace('admin.php', 'index.php', $str);

    return $str;
}

function getnavactive($link)
{


    //"http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
//	$mm= parse_url($link);
    $local = $_SERVER['REQUEST_URI'];

    if ($local == getbaseurl()) {
        $local = es_url('Index/index');
    }

    if (strpos($local, $link) === false) {

        return false;

    } else {
        return true;
    }


}

function getheadurl($head)
{
    if (preg_match("/^(http:\/\/|https:\/\/).*$/", $head)) {
        return $head;
    } else {
        return detect_site_url() . $head;
    }
}

function getheadurlbyid($uid)
{

    $children = database::getInstance()->table('user')->where(['id' => $uid])->getRow();

    if (preg_match("/^(http:\/\/|https:\/\/).*$/", $children['userhead'])) {
        return $head;
    } else {
        return detect_site_url() . $children['userhead'];
    }
}

function getusergrade($gradeid, $uid = 0)
{


    if ($uid > 0) {
        $rzinfo = db('rzuser')->where(['uid' => $uid])->getRow();
        if ($rzinfo) {

            $name = $rzinfo['statusdes'];
            return;
        }


    }


    if ($uid == 0) {
        $name = DB('usergrade')->where(['id' => $gradeid])->value('name');

        if (empty($name)) {
            $name = '普通会员';
        }


    } else {

        $info = DB('user')->where(['id' => $uid])->getRow();

        $map['score|<='] = $info['expoint1'];

        $res = DB('usergrade')->where($map)->order('score desc')->limit(1)->getList();


        if (!empty($res)) {
            if ($res[0]['id'] != $info['grades']) {
                $data['grades'] = $res[0]['id'];
                DB('user')->where(['id' => $uid])->update($data);
            }
        }
        $name = $res[0]['name'];
        if (empty($name)) {
            $name = '普通会员';
        }


    }


    return $name;
}

function sendsysmess($content, $uid, $touid, $type)
{

    $data['update_time'] = time();
    $data['create_time'] = time();
    $data['uid']         = $uid;
    $data['touid']       = $touid;
    $data['type']        = $type;
    $data['content']     = $content;

    db('message')->insert($data);


}

function asyn_sendmail($data)
{
    $domain = $_SERVER['HTTP_HOST'];


    $url = getweburl() . es_url('Index/send_mail');


    http_curl($url, $data, 'POST');


}

/**
 * 发送邮件
 */
function send_email($address, $title, $message)
{
    /*
     * 邮件发送类
     * 支持发送纯文本邮件和HTML格式的邮件，可以多收件人，多抄送，多秘密抄送，带附件(单个或多个附件),支持到服务器的ssl连接
     * 需要的php扩展：sockets、Fileinfo和openssl。
     * 编码格式是UTF-8，传输编码格式是base64
     * @example
     *  */
    $mail = new \extend\sendmail();

    $mail->setServer(webconfig('mailserver'), webconfig('mailusername'), webconfig('mailpassword'), webconfig('mailport'), true); //设置smtp服务器，到服务器的SSL连接
    $mail->setFrom(webconfig('mailusername')); //设置发件人
    $mail->setFromname(webconfig('mailname')); //设置发件人
    $mail->setReceiver($address); //设置收件人，多个收件人，调用多次
    //	 $mail->setCc("XXXX"); //设置抄送，多个抄送，调用多次
    //	 $mail->setBcc("XXXXX"); //设置秘密抄送，多个秘密抄送，调用多次
    //	 $mail->addAttachment( array("XXXX","xxxxx") ); //添加附件，多个附件，可调用多次，第一个文件名是 程序要去抓的文件名，第二个文件名是显示在邮件中的文件名。
    $mail->setMail($title, html_entity_decode($message)); //设置邮件主题、内容


    $mail->sendMail(); //发送


    return $mail->error();
}