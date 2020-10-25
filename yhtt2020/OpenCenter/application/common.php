<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Container;

/**
 * 获取到网站根目录
 * @return mixed
 * @author 奕潇
 */
function get_root()
{
    // 基础替换字符串
    $request = Container::get('request');
    $root = $request->root();
    return $root;
}

/**
 * @param $time //时间戳
 * @param string $format
 * @return false|string
 * @author sun slf02@ourstu.com
 * @date 2018/9/25 16:27
 * 格式化时间函数
 */
function time_format($time = NULL, $format = 'Y-m-d H:i')
{
    $time = $time === NULL ? time() : intval($time);
    return date($format, $time);
}

/*
 * 获取文档封面图片
 */
function get_cover($cover_id, $field = null)
{

    if (empty($cover_id)) {
        return false;
    }
    $tag = 'picture_' . $cover_id;
    $picture = cache($tag);
    if (empty($picture)) {
        $picture = db('Picture')->where(array('status' => 1,'id'=>$cover_id))->find();
        cache($tag, $picture);
    }

    $picture['path'] = get_pic_src($picture['path']);
    return empty($field) ? $picture : $picture[$field];
}

/**
 * @param $cover_id
 * @return array|bool|mixed|null|PDOStatement|string|\think\Model
 * @author:lin(lt@ourstu.com)
 */
function pic($cover_id)
{
    return get_cover($cover_id, 'path');
}

/**
 * 渲染图片链接
 * @param $path
 * @return mixed
 * @author:lin(lt@ourstu.com)
 */
function get_pic_src($path)
{
    //不存在http://
    $not_http_remote = (strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote = (strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return str_replace('//', '/', get_root() . $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}

/**
 * 系统非常规MD5加密方法
 * @param $str
 * @param string $key
 * @return string
 * @author:wdx(wdx@ourstu.com)
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 (单位:秒)
 * @return string
 */
function think_ucenter_encrypt($data, $key, $expire = 0)
{
    $key = md5($key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key 加密密钥
 * @return string
 */
function think_ucenter_decrypt($data, $key)
{
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);
    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 获取登录管理员id
 * @return mixed
 * @author:wdx(wdx@ourstu.com)
 */
function get_aid()
{
    $admin = session('admin_auth');
    if (session('admin_auth_sign') == data_auth_sign($admin)) {
        return $admin['uid'];
    } else {
        return false;
    }
}

/**
 * 获取登录用户id
 * @return mixed
 * @author:wdx(wdx@ourstu.com)
 */
function get_uid()
{
    $user = session('user_auth');
    if (session('user_auth_sign') == data_auth_sign($user)) {
        return $user['uid'];
    } else {
        return false;
    }
}

/**
 * text函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function text($text, $addslanshes = false)
{
    $text = nl2br($text);
    $text = real_strip_tags($text);
    if ($addslanshes)
        $text = addslashes($text);
    $text = trim($text);
    return $text;
}

/**
 * @param $str
 * @param string $allowable_tags
 * @return string
 * @author:lin(lt@ourstu.com)
 */
function real_strip_tags($str, $allowable_tags = "")
{
    return strip_tags($str, $allowable_tags);
}

/**
 * html函数用于过滤不安全的html标签，输出安全的html
 * @param string $text 待过滤的字符串
 * @param string $type 保留的标签格式
 * @return string 处理后内容
 */
function html($text, $type = 'html')
{
    // 无标签格式
    $text_tags = '';
    //只保留链接
    $link_tags = '<a>';
    //只保留图片
    $image_tags = '<img>';
    //只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    //标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    //兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    //内容等允许HTML的格式
    $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    //专题等全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    //过滤标签
    $text = real_strip_tags($text, ${$type . '_tags'});
    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background[^-]|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}

/**
 * 判断管理员是否登录
 * @return bool
 * @author:wdx(wdx@ourstu.com)
 */
function is_admin_login()
{
    $aid = get_aid();
    if ($aid) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断用户是否登录
 * @return bool
 * @author:wdx(wdx@ourstu.com)
 */
function is_user_login()
{
    $uid = get_uid();
    if ($uid) {
        return true;
    } else {
        return false;
    }
}

/**
 * 数据签名认证
 * @param $data
 * @return string
 * @author:wdx(wdx@ourstu.com)
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data);                //排序
    $code = http_build_query($data);     //url编码并生成query字符串
    $sign = sha1($code);                 //生成签名
    return $sign;
}

/**
 * 获取全球唯一标识
 * @return string
 */
function uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * 状态转换
 * @param array $arr        传入数组
 * @param string $field     字段名
 * @return array
 * @author:wdx(wdx@ourstu.com)
 */
function to_status(&$arr, $field = 'status')
{
    if (isset($arr[$field])) {
        $statusConfig = config('status_replace_str');
        $arr[$field] = $statusConfig[$arr[$field]];
    }
    return $arr;
}

/**
 * 显示转换
 * @param $arr
 * @param $field string
 * @return mixed
 * @author:wdx(wdx@ourstu.com)
 */
function to_is_show(&$arr, $field = 'is_show')
{
    if (isset($arr[$field])) {
        if ($arr[$field] == 1) {
            $arr[$field] = '显示';
        } else {
            $arr[$field] = '隐藏';
        }
    }
    return $arr;
}

/**
 * 是否转换
 * @param $arr
 * @param $field string
 * @return mixed
 * @author:wdx(wdx@ourstu.com)
 */
function to_yes_no(&$arr, $field = '')
{
    if (isset($arr[$field])) {
        if ($arr[$field] == 1) {
            $arr[$field] = '是';
        } else {
            $arr[$field] = '否';
        }
    }
    return $arr;
}

/**
 * 时间转换
 * @param array $arr        传入数组
 * @param string $field     字段名
 * @param string $format    格式
 * @return mixed
 * @author:wdx(wdx@ourstu.com)
 */
function to_time(&$arr, $field = 'time', $format = 'Y-m-d H:i:s')
{
    if (isset($arr[$field])) {
        $arr[$field] = date($format, $arr[$field]);
    }
    return $arr;
}

/**
 * ip转换
 * @param array $arr        传入数组
 * @param string $field     字段名
 * @return mixed
 * @author:wdx(wdx@ourstu.com)
 */
function to_ip(&$arr, $field = 'ip')
{
    if (isset($arr[$field])) {
        $arr[$field] = long2ip($arr[$field]);
    }
    return $arr;
}

/**
 * 将数组转化成树
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int $root
 * @return array
 * @author:wdx(wdx@ourstu.com)
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            if (empty($list[$key]['child'])) {
                $list[$key]['child'] = null;
            }
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];

                    if ($parent == null) {
                        $parent[$child][] = null;
                    } else {
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}
