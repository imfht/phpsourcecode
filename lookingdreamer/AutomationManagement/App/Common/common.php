<?php

/*
 * This is NOT a freeware, use is subject to license terms [SEOPHP] (C) 2012-2015 QQ:224505576 SITE: http://seophp.taobao.com/
 */
if (!defined('IN_SEOPHP')) {
    exit ('Access Denied');
}

/* 分类树 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            if (!$data ['level'])
                continue;
            $refer [$data [$pk]] = & $list [$key];
        }
        foreach ($list as $key => $data) {
            if (!$data ['level'])
                continue;
            // 判断是否存在parent
            $parentId = $data [$pid];
            if ($root == $parentId) {
                $tree [$data [$pk]] = & $list [$key];
            } else {
                if (isset ($refer [$parentId])) {
                    $parent = & $refer [$parentId];
                    $parent [$child] [$data [$pk]] = & $list [$key];
                }
            }
        }
    }
    return $tree;
}

function cutstr($string, $length, $dot = ' ...', $charset = 'utf-8')
{
    if (strlen($string) <= $length) {
        return $string;
    }

    $pre = chr(1);
    $end = chr(1);
    $string = str_replace(array(
        '&amp;',
        '&quot;',
        '&lt;',
        '&gt;'
    ), array(
        $pre . '&' . $end,
        $pre . '"' . $end,
        $pre . '<' . $end,
        $pre . '>' . $end
    ), $string);

    $strcut = '';
    if (strtolower($charset) == 'utf-8') {

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string [$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string [$i]) > 127 ? $string [$i] . $string [++$i] : $string [$i];
        }
    }

    $strcut = str_replace(array(
        $pre . '&' . $end,
        $pre . '"' . $end,
        $pre . '<' . $end,
        $pre . '>' . $end
    ), array(
        '&amp;',
        '&quot;',
        '&lt;',
        '&gt;'
    ), $strcut);

    $pos = strrpos($strcut, chr(1));
    if ($pos !== false) {
        $strcut = substr($strcut, 0, $pos);
    }
    return $strcut . $dot;
}

function auto_charset($fContents, $from = 'utf-8', $to = 'gb2312')
{
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty ($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = $this->auto_charset($key, $from, $to);
            $fContents [$_key] = $this->auto_charset($val, $from, $to);
            if ($key != $_key)
                unset ($fContents [$key]);
        }
        return $fContents;
    } else {
        return $fContents;
    }
}

function showOption($list, $ids)
{
    $html = SelectTree($list, $ids);
    echo $html;
}

function SelectTree($list, $ids)
{
    $html = "";
    $idarr = explode(",", $ids);
    foreach ($list as $key => $val) {
        $selected = in_array($val ["id"], $idarr) ? " selected " : "";
        $level = $val ["level"] - 2;
        if ($level > 0)
            $subChar = str_repeat('─', $level);
        $html .= '<option ' . $selected . ' value="' . $val ["id"] . '">' . ($val ["pid"] ? "└" : "") . $subChar . '  ' . $val ["title"] . '</option>';
        if ($val ["_child"])
            $html .= SelectTree($val ["_child"], $ids);
    }
    return $html;
}

/* 排序 */
function sortBy($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer [$i] = & $data [$field];
        switch ($sortby) {
            case 'asc' :
                asort($refer);
                break;
            case 'desc' :
                arsort($refer);
                break;
            case 'nat' :
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet [] = & $list [$key];
        return $resultSet;
    }
    return false;
}

/* 数组搜索 */
function search($list, $condition)
{
    if (is_string($condition))
        parse_str($condition, $condition);
    $resultSet = array();
    foreach ($list as $key => $data) {
        $find = false;
        foreach ($condition as $field => $value) {
            if (isset ($data [$field])) {
                if (0 === strpos($value, '/')) {
                    $find = preg_match($value, $data [$field]);
                } elseif ($data [$field] == $value) {
                    $find = true;
                }
            }
        }
        if ($find)
            $resultSet [] = & $list [$key];
    }
    return $resultSet;
}

function getParentIDs($list, $id)
{
    $pids = getPIDs($list, $id);
    $idsArr = explode(",", $pids);
    foreach ($idsArr as $val) {
        if ($val && $list [$val]) {
            $list_id [] = $val;
            $list_title [] = $list [$val] ["title"];
        }
    }
    $result = array(
        "id" => implode(",", $list_id),
        "title" => implode(",", $list_title)
    );
    return $result;
}

function getChildIDs($listTee, $pids)
{
    $list = $listTee;
    $cids = "";
    $pidss = explode(",", $pids ["id"]);
    foreach ($pidss as $val) {
        if ($val && $list [$val] ["_child"]) {
            $list = $list [$val] ["_child"];
        } elseif ($val) {
            $list = array();
        }
    }
    $cids = getCIDs($list);
    $cidsArr = explode(",", $cids);
    foreach ($cidsArr as $val) {
        if ($val)
            $list_id [] = $val;
    }
    $result = implode(",", $list_id);
    return $result;
}

function getPIDs($list, $id)
{
    $result = "";
    $node = $list [$id];
    $result = $node ["id"] . "" . $result;
    if ($node ["pid"])
        $result = getPIDs($list, $node ["pid"]) . "," . $result;
    return $result;
}

function getCIDs($list)
{
    $result = "";
    foreach ($list as $key => $val) {
        $result .= ',' . $val ["id"];
        if ($val ["_child"])
            $result .= ',' . getCIDs($val ["_child"]);
    }
    return $result;
}

function showLogo($logo)
{
    if ($logo) {
        echo '<img src="' . $logo . '" />';
    } else {
        echo '';
    }
}

function getPicture(&$imglist, $content)
{
    $imglist = array();
    $reg = "/<img.*?(\ssrc\s*=\s*(['\"])?\s*([^\s]+?)\s*\\2?)(?=\s|>)[^>]*?>/si";
    preg_match_all($reg, $content, $ereg);
    foreach ($ereg [3] as $img) {
        if (strpos($img, 'static/image') === false) {
            $imglist [] = $img;
        }
    }
}

function getDescription($id, $data, $len = 160)
{
    $content = HtmlTrim($data ['content'], '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17');
    $content = cutstr($content, $len);
    echo $content;
}

function blog_summary($body, $size, $format = NULL)
{
    $pattern = '/<img\s+src=[\\\'| \\\"](.*?(?:[\.gif|\.jpg]))[\\\'|\\\"].*?[\/]?>/i';
    $body = HtmlTrim($body, '13');

    $_size = mb_strlen($body, 'utf-8');
    if ($_size <= $size)
        return $body;
    $strlen_var = strlen($body);
    // 不包含 html 标签
    if (strpos($body, '<') === false) {
        return mb_substr($body, 0, $size);
    }
    // 包含截断标志，优先
    if ($e = strpos($body, '<!-- break -->')) {
        return mb_substr($body, 0, $e);
    }
    // html 代码标记
    $html_tag = 0;
    // 摘要字符串
    $summary_string = '';
    /**
     * 数组用作记录摘要范围内出现的 html 标签
     * 开始和结束分别保存在 left 和 right 键名下
     * 如字符串为：<h3><p><b>a</b></h3>，假设 p 未闭合
     * 数组则为：array('left' => array('h3', 'p', 'b'), 'right' => 'b', 'h3');
     * 仅补全 html 标签，<? <% 等其它语言标记，会产生不可预知结果
     */
    $html_array = array(
        'left' => array(),
        'right' => array()
    );
    for ($i = 0; $i < $strlen_var; ++$i) {
        if (!$size) {
            break;
        }
        $current_var = substr($body, $i, 1);
        if ($current_var == '<') {
            // html 代码开始
            $html_tag = 1;
            $html_array_str = '';
        } else if ($html_tag == 1) {
            // 一段 html 代码结束
            if ($current_var == '>') {
                /**
                 * 去除首尾空格，如 <br / > < img src="" / > 等可能出现首尾空格
                 */
                $html_array_str = trim($html_array_str);
                /**
                 * 判断最后一个字符是否为 /，若是，则标签已闭合，不记录
                 */
                if (substr($html_array_str, -1) != '/') {
                    // 判断第一个字符是否 /，若是，则放在 right 单元
                    $f = substr($html_array_str, 0, 1);
                    if ($f == '/') {
                        // 去掉 /
                        $html_array ['right'] [] = str_replace('/', '', $html_array_str);
                    } else if ($f != '?') {
                        // 判断是否为 ?，若是，则为 PHP 代码，跳过
                        /**
                         * 判断是否有半角空格，若有，以空格分割，第一个单元为 html 标签
                         * 如 <h2 class="a"> <p class="a">
                         */
                        if (strpos($html_array_str, ' ') !== false) {
                            // 分割成2个单元，可能有多个空格，如：<h2 class="" id="">
                            $html_array ['left'] [] = strtolower(current(explode(' ', $html_array_str, 2)));
                        } else {
                            /**
                             * * 若没有空格，整个字符串为 html 标签，如：<b> <p> 等
                             * 统一转换为小写
                             */
                            $html_array ['left'] [] = strtolower($html_array_str);
                        }
                    }
                }
                // 字符串重置
                $html_array_str = '';
                $html_tag = 0;
            } else {
                /**
                 * 将< >之间的字符组成一个字符串
                 * 用于提取 html 标签
                 */
                $html_array_str .= $current_var;
            }
        } else {
            // 非 html 代码才记数
            --$size;
        }
        $ord_var_c = ord($body{$i});
        switch (true) {
            case (($ord_var_c & 0xE0) == 0xC0) :
                // 2 字节
                $summary_string .= substr($body, $i, 2);
                $i += 1;
                break;
            case (($ord_var_c & 0xF0) == 0xE0) :
                // 3 字节
                $summary_string .= substr($body, $i, 3);
                $i += 2;
                break;
            case (($ord_var_c & 0xF8) == 0xF0) :
                // 4 字节
                $summary_string .= substr($body, $i, 4);
                $i += 3;
                break;
            case (($ord_var_c & 0xFC) == 0xF8) :
                // 5 字节
                $summary_string .= substr($body, $i, 5);
                $i += 4;
                break;
            case (($ord_var_c & 0xFE) == 0xFC) :
                // 6 字节
                $summary_string .= substr($body, $i, 6);
                $i += 5;
                break;
            default :
                // 1 字节
                $summary_string .= $current_var;
        }
    }
    if ($html_array ['left']) {
        /**
         * 比对左右 html 标签，不足则补全
         */
        /**
         * 交换 left 顺序，补充的顺序应与 html 出现的顺序相反
         * 如待补全的字符串为：<h2>abc<b>abc<p>abc
         * 补充顺序应为：</p></b></h2>
         */
        $html_array ['left'] = array_reverse($html_array ['left']);
        foreach ($html_array ['left'] as $index => $tag) {
            // 判断该标签是否出现在 right 中
            $key = array_search($tag, $html_array ['right']);
            if ($key !== false) {
                // 出现，从 right 中删除该单元
                unset ($html_array ['right'] [$key]);
            } else {
                // 没有出现，需要补全
                $summary_string .= '</' . $tag . '>';
            }
        }
    }
    return $summary_string;
}

function getCategoryTitle($catid, $return = 0)
{
    $Cachefile = DATA_PATH . '~category.php';
    $Category = include($Cachefile);
    if ($return)
        return $Category [$catid];
    if ($Category [$catid]) {
        echo $Category [$catid] ["title"];
    } elseif ($catid) {
        echo "";
    }
}

function getCategory($catid, $attr = '')
{
    $Cachefile = DATA_PATH . '~category.php';
    $catlist = include($Cachefile);
    $cat = $catlist [$catid];
    if ($attr && $cat [$attr]) {
        return $cat [$attr];
    } else {
        return $cat;
    }
}

function getCategoryPath($catid, $href = false, $pre = '', $module = '')
{
    $Cachefile = DATA_PATH . '~category.php';
    $Category = include($Cachefile);
    if (!$module)
        $module = MODULE_NAME;
    if ($Category [$catid] ["parentids"] ["title"]) {
        $ids = explode(",", $Category [$catid] ["parentids"] ["id"]);
        $titles = explode(",", $Category [$catid] ["parentids"] ["title"]);
        if ($href) {
            foreach ($ids as $key => $val) {
                $html [] = '<a href="' . getUrl($Category [$val], 'Home/' . $module . '/index?catid=' . $val, 1) . '">' . $titles [$key] . '</a>';
            }
            echo $pre . implode(" > ", $html);
        } else {
            echo $pre . implode(" > ", $titles);
        }
    } elseif ($catid && !$pre) {
        echo "";
    }
}

function getListByModule(&$list, $module, $count = 10, $order = "", $sql = "", $cacheName = "", $cacheTime = 0, $groupby = "")
{
    $Model = M($module);
    if ($cacheName && $cacheTime) {
        $list = $Model->where("1 AND create_time<'" . C("NOW_TIME") . "'" . ($sql ? " AND " . $sql : ""))->limit($count)->cache($module . "_" . $count . "_" . $cacheName, $cacheTime)->order($order)->group($groupby)->select();
    } else {
        $list = $Model->where("1 AND create_time<'" . C("NOW_TIME") . "'" . ($sql ? " AND " . $sql : ""))->limit($count)->order($order)->group($groupby)->select();
    }
}

/*
 * 用法： list 数据列表：id, title, link, module, target, level, pid module 模块列表: menu_nav, menu_top, menu_bottom, category_new, category_job.... direct 1 - 水平， 2 - 垂直， 3 - Tabs Navclass 第一个UL样式 navid 第一个UL id limitlevel 限制层数 0 - 所有层
 */
function getNavHtml(&$list, $module = '', $direct = 1, $Navclass = '', $navid = '', $limitlevel = 0)
{
    if (!$list && $module) {
        $list = include(DATA_PATH . '~' . $module . '.php');
    }
    if (!$list)
        return false;
    $navlist = list_to_tree($list);
    if (!$direct) {
        $list = getNavList($navlist, $limitlevel);
    } else {
        $navHtml = getListHtml($navlist, $direct, $Navclass, $navid, $limitlevel);
        echo $navHtml;
    }
}

function getNavList($navlist, $limitlevel = 0)
{
    $list = array();
    $i = 1;
    foreach ($navlist as $val) {
        if (!$val ['level'])
            continue;
        $mods = explode('/', $val ['module']);
        if (!$mods [1])
            $mods [1] = 'index';
        if ($val ['link']) {
            $url = $val ['link'];
        } elseif ($val ['moduledata']) {
            if (strtolower($mods [0]) == 'category') {
                if (MODULE_NAME == $mods [0] && ACTION_NAME == $mods [1] && intval($_GET ['catid']) == intval($val ['modid']))
                    $val ['_active'] = 1;
                $url = getUrl($val ['moduledata'], 'Home/' . $mods [0] . '/' . $mods [1] . '?catid=' . $val ['modid'], 1);
            } else {
                $url = getReadUrl($val ['modid'], $val ['moduledata'], $mods [0], 1);
            }
        } elseif ($val ['parentids']) {
            if (MODULE_NAME == $mods [0] && ACTION_NAME == $mods [1] && intval($_GET ['catid']) == intval($val ['id']))
                $val ['_active'] = 1;
            $url = getUrl($val, 'Home/' . $val ['module'] . '/' . $mods [1] . '?catid=' . $val ['id'], 1);
        } else {
            if (MODULE_NAME == $mods [0] && ACTION_NAME == $mods [1])
                $val ['_active'] = 1;
            $url = getUrl('', 'Home/' . $mods [0] . '/' . $mods [1], 1);
        }
        if (strtolower('http://' . $_SERVER ['SERVER_NAME'] . $_SERVER ['REQUEST_URI']) == strtolower($url))
            $val ['_active'] = 1;
        $val ['link'] = $url;
        if ($val ['_child'] && (!$limitlevel || $limitlevel > $val ['level']))
            $val ['_child'] = getNavList($val ['_child'], $limitlevel);
        $list [] = $val;
    }
    return $list;
}

function getListHtml($navlist, $direct, $Navclass, $Navid, $limitlevel = 0)
{
    $i = 1;
    foreach ($navlist as $val) {
        if (!$val ['level'])
            continue;
        $mods = explode('/', $val ['module']);
        if (!$mods [1])
            $mods [1] = 'index';
        $clsStr = '';
        $a_clsStr = '';
        $liclass = array();
        $a_class = array();
        $level = $val ['level'];
        if ($i == 1) {
            $liclass [] = 'first';
        } elseif ($i == count($navlist)) {
            $liclass [] = 'last';
        } else {
            $liclass [] = 'middle';
        }
        if (fmod($i, 2) == 0) {
            $liclass [] = 'even';
        } else {
            $liclass [] = 'odd';
        }
        $liclass [] = "sf-depth-" . $val ['level'];
        $a_class [] = "sf-depth-" . $val ['level'];
        if ($val ['_child']) {
            $liclass [] = 'menuparent';
            $a_class [] = 'menuparent';
        } else {
            $liclass [] = 'sf-no-children';
        }
        if ($level == 1)
            $liclass [] = 'top';
        if ($liclass) {
            $clsStr = ' class="' . implode(' ', $liclass) . '"';
        }
        if ($val ['link']) {
            $url = $val ['link'];
        } elseif ($val ['moduledata']) {
            if (strtolower($mods [0]) == 'category') {
                if (MODULE_NAME == $mods [0] && ACTION_NAME == $mods [1] && intval($_GET ['catid']) == intval($val ['modid']))
                    $val ['_active'] = 1;
                $url = getUrl($val ['moduledata'], 'Home/' . $mods [0] . '/' . $mods [1] . '?catid=' . $val ['modid'], 1);
            } else {
                $url = getReadUrl($val ['modid'], $val ['moduledata'], $mods [0], 1);
            }
        } elseif ($val ['parentids']) {
            if (MODULE_NAME == $mods [0] && ACTION_NAME == $mods [1] && intval($_GET ['catid']) == intval($val ['id']))
                $val ['_active'] = 1;
            $url = getUrl($val, 'Home/' . $mods [0] . '/' . $mods [1] . '?catid=' . $val ['id'], 1);
        } else {
            if (MODULE_NAME == $mods [0] && ACTION_NAME == $mods [1])
                $val ['_active'] = 1;
            $url = getUrl('', 'Home/' . $mods [0] . '/' . $mods [1], 1);
        }
        if (!$val ['_active']) {
            if (strtolower('http://' . $_SERVER ['SERVER_NAME'] . $_SERVER ['REQUEST_URI']) == strtolower($url))
                $val ['_active'] = 1;
        }
        $target = $val ['target'] ? ' target="' . $val ['link'] . '"' : '';
        if ($val ['_active'])
            $a_class [] = 'active';
        $a_clsStr = ' class="' . implode(' ', $a_class) . '"';
        $str .= '<LI' . $clsStr . '>';
        $str .= '<span></span><a href="' . $url . '"' . $target . $a_clsStr . '>' . $val ["title"] . '</a>';
        if ($val ['_child'] && (!$limitlevel || $limitlevel > $val ['level']))
            $str .= getListHtml($val ['_child'], $direct, $Navclass, $Navid, $limitlevel);
        $str .= '</LI>';
        $i++;
    }
    $clsStr = '';
    $idStr = '';
    if ($level == 1) {
        if (!$Navid)
            $Navid = 'Menu_' . implode('', build_count_rand(1, rand(8, 16), 0));
        $idStr = ' id="' . $Navid . '"';
        $Class [] = 'first';
        $Class [] = $Navid;
        if ($Navclass)
            $Class [] = $Navclass;
    } else {
        $Navid = '';
        $Class [] = 'ddsubmenustyle';
    }
    if ($Class) {
        $clsStr = ' class="' . implode(' ', $Class) . '"';
    }
    $html = '<UL' . $idStr . $clsStr . '>' . $str . '</UL>';
    // if($Navid && $direct < 10)$html .= '<script LANGUAGE="JavaScript">navInit(\''.$Navid.'\', \'span\');</script>';
    return $html;
}

function SetNavPosition($value, $attr = '', $display = 0)
{
    $html = '';
    $html .= '<SELECT ' . $attr . '>';
    if ($display)
        $html .= '<option value="">--不显示--</option>';
    $html .= '<optgroup label="企业站导航">';
    $html .= '<option ' . ($value == 'nav' ? 'selected ' : '') . 'value="nav">企业站 ＞ 网站导航</option>';
    $html .= '<option ' . ($value == 'top' ? 'selected ' : '') . 'value="top">企业站 ＞ 顶部菜单</option>';
    $html .= '<option ' . ($value == 'bottom' ? 'selected ' : '') . 'value="bottom">企业站 ＞ 底部菜单</option>';
    $html .= '</optgroup>';
    $html .= '</SELECT>';
    echo $html;
}

function configGroup(&$group, $return = 0)
{
    $group = array(
        'site' => array(
            'name' => 'site',
            'title' => '站点配置',
            'icon' => 'font-star',
            'admin' => 1
        ),
        'database' => array(
            'name' => 'database',
            'title' => '数据库参数',
            'icon' => 'font-sitemap',
            'admin' => 1
        ),
        'thumb' => array(
            'name' => 'thumb',
            'title' => '缩略图设置',
            'icon' => ' font-qrcode',
            'admin' => 0
        )
    );
    if ($return)
        return $group;
}

function modulelist(&$modules, $return = 0)
{
    $modules = array(
        'New' => array(
            'name' => 'New',
            'title' => '公司新闻'
        ),
        'Product' => array(
            'name' => 'Product',
            'title' => '产品展示'
        ),
        'Case' => array(
            'name' => 'Case',
            'title' => '案例展示'
        ),
        'Down' => array(
            'name' => 'Down',
            'title' => '资料下载'
        ),
        'Job' => array(
            'name' => 'Job',
            'title' => '招聘信息'
        ),
        'Pages' => array(
            'name' => 'Pages',
            'title' => '单页信息'
        ),
        'Article' => array(
            'name' => 'Article',
            'title' => '文章资讯'
        ),
        'Link' => array(
            'name' => 'Link',
            'title' => '友情链接'
        ),
        'Comment' => array(
            'name' => 'Comment',
            'title' => '评论留言'
        ),
        'Menu' => array(
            'name' => 'Menu',
            'title' => '导航菜单'
        ),
        'Online' => array(
            'name' => 'Online',
            'title' => '在线客服'
        )
    );
    if ($return)
        return $modules;
}

function checkTpldir($tpldir)
{
    $dir = APP_PATH . "Tpl/Home/" . $tpldir;
    $isdir = is_dir($dir) ? true : false;
    if ($_POST ["isdefault"]) {
        $model = M("Template");
        $data = array(
            "isdefault" => 0
        );
        $model->where("1")->save($data);
    }
    return $isdir;
}

function checkStyledir($styledir)
{
    if (!$tpldir)
        $tpldir = $_POST ["tpldir"];
    if (!$tpldir)
        return false;
    $dir = APP_PATH . "Tpl/Home/" . $tpldir . "/Public/Styles/" . $styledir;
    $isdir = is_dir($dir) ? true : false;
    if ($_POST ["isdefault"]) {
        $model = M("Tplstyle");
        $data = array(
            "isdefault" => 0
        );
        $model->where("1")->save($data);
    }
    return $isdir;
}

function checkLicense()
{
    if (!defined('SEOPHP_AUTHORKEY'))
        return false;
    $http_host = strtolower($_SERVER ['SERVER_NAME']);
    $lic_name = SeophpCode(SEOPHP_AUTHORNAME, 'DECODE', SEOPHP_AUTHORDATE);
    $lic_host = strtolower(SeophpCode(SEOPHP_AUTHORKEY, 'DECODE', $lic_name));
    if (!strpos($http_host, $lic_host))
        return false;
    $lic_code = getLicenseCode(SEOPHP_AUTHORKEY);
    return $lic_code;
}

function getLicenseCode($key)
{
    $lic_code = preg_replace("/[^A-Za-z0-9]*/", '', $key);
    $lic_code = strtoupper(md5($lic_code));
    $lic_code = substr($lic_code, 2, 4) . '-' . substr($lic_code, 9, 4) . '-' . substr($lic_code, 15, 4) . '-' . substr($lic_code, 20, 4);
    return $lic_code;
}

/* 显示标签 */
function showTags($tags)
{
    $tags = explode(' ', $tags);
    $str = '';
    foreach ($tags as $key => $val) {
        $tag = trim($val);
        $str .= ' <a href="' . __URL__ . '/tag/' . urlencode($tag) . '">' . $tag . '</a>  ';
    }
    return $str;
}

/* 内容处理 */
function getAbstract($content, $id)
{
    if (false !== $pos = strpos($content, '[separator]')) {
        $content = substr($content, 0, $pos) . '  <P> (<a href="' . __URL__ . '/' . $id . '"><B>阅读全部内容… </B></a>) ';
    }
    return $content;
}

/* 处理内容 */
function removeAbstract($content)
{
    return str_replace('[separator]', '', $content);
}

/* 处理大小 */
function getTitleSize($count)
{
    $size = (ceil($count / 10) + 11) . 'px';
    return $size;
}

/* 颜色处理 */
function rcolor()
{
    $rand = rand(0, 255);
    return sprintf("%02X", "$rand");
}

/* 颜色处理 */
function rand_color()
{
    return '#' . rcolor() . rcolor() . rcolor();
}

/* 数据缓存 */
function cache_old($name, $value = '', $expire = 60)
{
    if ('' === $value) {
        return xcache_get($name);
    } else {
        if (is_null($value)) {
            xcache_unset($name);
        } else {
            xcache_set($name, $value, $expire);
        }
    }
}

/* 主题管理 */
function getDefaultPic($file, $extension = '', $module = MODULE_NAME)
{
    $filepath = '/Public/Uploads/' . $module . '/' . $file;
    $dir = str_replace('\\', '/', dirname($_SERVER ['SCRIPT_NAME']));
    $http = 'http://' . $_SERVER ['SERVER_NAME'] . (strlen($dir) == 1 ? '' : $dir);
    if (!$extension)
        $extension = pathinfo($file, PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    if ($file && file_exists('.' . $filepath)) {
        echo $http . $filepath;
    } elseif ($extension && file_exists('./Public/Images/Icon/' . $extension . '.png')) {
        echo $http . '/Public/Images/Icon/' . $extension . '.png';
    } else {
        echo $http . '/Public/Images/Icon/nopic.png';
    }
}

function viewcategory($id, $category)
{
    $url = getUrl($category, 'Home/' . $category ['module'] . '/index' . ($category ['level'] ? '?catid=' . $category ['id'] : ''), 1);
    echo '<a href="' . $url . '" target="_blank">浏览分类</a>';
}

function getReadUrl($id, $vo = '', $module = MODULE_NAME, $return = 0)
{
    $url = getUrl($vo, 'Home/' . $module . '/read?id=' . $id, 1);
    if ($return) {
        return $url;
    } else {
        echo $url;
    }
}

function getUrl($vo = '', $url = '', $return = 0, $suffix = '')
{
    if ($vo ['url'] && strlen($vo ['url']) > 4) {
        $url = U('/' . $vo ['url'], '', '', false, true);
    } else {
        $ch = C('URL_PATHINFO_DEPR_READ');
        $ch_i = C('URL_PATHINFO_DEPR');
        $urltitle = getUrlTitle($vo);
        C('URL_PATHINFO_DEPR', $ch);
        $suffix = $suffix ? $suffix : C('URLREWRITE');
        if (strlen($suffix) < 3) {
            $_suffix = $suffix;
            $suffix = '';
        }
        $patterns = array(
            'Home/Blog/',
            'Home/Cms/'
        );
        $replaces = array(
            'Blog/Blog/',
            'Cms/Cms/'
        );
        $url = str_replace($patterns, $replaces, $url);
        $url = U($url . $urltitle . $_suffix, '', $suffix, false, true);
        $url = str_replace(array(
            '/' . C('DEFAULT_GROUP') . $ch,
            $ch . 'read' . $ch . 'id' . $ch
        ), array(
            '/',
            $ch
        ), $url);
        C('URL_PATHINFO_DEPR', $ch_i);
    }
    if ($return) {
        return $url;
    } else {
        echo $url;
    }
}

function getUrlTitle($vo)
{
    $ch = C('URL_PATHINFO_DEPR_READ');
    $ch_i = C('URL_PATHINFO_DEPR');
    C('URL_PATHINFO_DEPR', $ch);
    $urltitle = '';
    if (C('URLTITLE') && $vo ['title'] || $vo ['urlwords']) {
        if (is_array($vo ['urlwords'])) {
            $Titles [] = implode($ch, $vo ['urlwords']);
        } elseif ($vo ['urlwords']) {
            $urlwords = explode(',', $vo ['urlwords']);
            $Titles [] = implode($ch, $urlwords);
        }
        if ($vo ['is_title_in_url']) {
            $temptitle = $vo ['seokey'] ? $vo ['seokey'] : $vo ['title'];
            if (C('CHANGE_TO_PINYIN') && $vo ['is_title_to_pinyin'])
                $temptitle = Pinyin($temptitle);
            $Titles [] = $temptitle;
        }
        if (is_array($Titles))
            $urltitle = $ch . implode($ch, $Titles);
    }
    C('URL_PATHINFO_DEPR', $ch_i);
    return $urltitle;
}

function getModuleUrl($id, $module = MODULE_NAME)
{
    $url = getUrl('', 'Home/' . $module . '/index?catid=' . $id, 1);
    echo $url;
}

function getSitemapPath($catid, $cat)
{
    if (!$cat ['level']) {
        $html = $cat ['title'];
    } else {
        $html = '<div style="float:left; margin-right:10px;"><a href="' . getUrl($cat, 'Home/' . $cat ['module'] . '/index?catid=' . $cat ['id'], 1) . '" target="_blank">' . $cat ['title'] . '</a></div><a href="' . getUrl('', 'Home/Sitemap/rss?mod=' . $cat ['module'] . '&catid=' . $cat ['id'], 1, 'xml') . '" style="float:left; margin-right:5px;"><img src="' . __PUBLIC__ . '/Images/xml.gif"/></a>';
        $html .= '<a href="' . getUrl($cat, 'Home/Sitemap/maphtml?mod=' . $cat ['module'] . '&catid=' . $cat ['id'], 1) . '"><img src="' . __PUBLIC__ . '/Images/htm.gif"/></a>';
    }
    echo $html;
}

function modulegroup($module)
{
    switch ($module) {
        case 'Blog' :
            $group = 'Blog';
            break;
        case 'Cms' :
            $group = 'Cms';
            break;
        default :
            $group = 'Home';
    }
    return $group;
}

/* 主题管理 */
function getDefaultStyle($style)
{
    if (empty ($style)) {
        return 'blue';
    } else {
        return $style;
    }
}

/* 推荐 */
function getRecommend($type)
{
    switch ($type) {
        case 1 :
            $icon = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/brand.gif" BORDER="0" align="absmiddle" ALT="">';
            break;
        default :
            $icon = '';
    }
    return $icon;
}

/* 密码处理 */
function pwdHash($password, $type = 'md5')
{
    return hash($type, $password);
}

/* 获取Ip */
function gets_client_ip()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset ($_SERVER ['REMOTE_ADDR']) && $_SERVER ['REMOTE_ADDR'] && strcasecmp($_SERVER ['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER ['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return ($ip);
}

/* 生成不重复数 */
function build_count_rand($number, $length = 4, $mode = 1)
{
    if ($mode == 1 && $length < strlen($number)) {
        return false;
    }
    $rand = array();
    for ($i = 0; $i < $number; $i++) {
        $rand [] = rand_string($length, $mode);
    }
    $unqiue = array_unique($rand);
    if (count($unqiue) == count($rand)) {
        return $rand;
    }
    $count = count($rand) - count($unqiue);
    for ($i = 0; $i < $count * 3; $i++) {
        $rand [] = rand_string($length, $mode);
    }
    $rand = array_slice(array_unique($rand), 0, $number);
    return $rand;
}

function rand_string($len = 6, $type = '', $addChars = '')
{
    $str = '';
    switch ($type) {
        case 0 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1 :
            $chars = str_repeat('0123456789', 3);
            break;
        case 2 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3 :
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) { // 位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

function Pinyin($_String, $_Code = 'gb2312', $_autoCheck = true)
{
    $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
    $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274|-10270|-10262|-10260|-10256|-10254";
    if ($_autoCheck) {
        if (strlen("天桥时尚") > 8) {
            $_Code = 'UTF';
        } else {
            $_Code = 'gb2312';
        }
    }
    $_TDataKey = explode('|', $_DataKey);
    $_TDataValue = explode('|', $_DataValue);
    $_Data = (PHP_VERSION >= '5.0') ? array_combine($_TDataKey, $_TDataValue) : _Array_Combine($_TDataKey, $_TDataValue);
    arsort($_Data);
    reset($_Data);
    if ($_Code != 'gb2312')
        $_String = _U2_Utf8_Gb($_String);
    $_Res = '';
    for ($i = 0; $i < strlen($_String); $i++) {
        $_P = ord(substr($_String, $i, 1));
        if ($_P > 160) {
            $_Q = ord(substr($_String, ++$i, 1));
            $_P = $_P * 256 + $_Q - 65536;
        }
        $_Res .= _Pinyin($_P, $_Data);
    }
    return preg_replace("/[^A-Za-z0-9]*/", '', $_Res);
}

function _Pinyin($_Num, $_Data)
{
    if ($_Num > 0 && $_Num < 160) {
        return chr($_Num);
    } elseif ($_Num < -20319 || $_Num > -10247) {
        return '';
    } else {
        foreach ($_Data as $k => $v) {
            if ($v <= $_Num)
                break;
        }
        // return ucfirst($k);
        return $k;
    }
}

function _U2_Utf8_Gb($_C)
{
    $_String = $_C;
    /*
	 * $_String = ''; if($_C < 0x80){ $_String .= $_C; }elseif($_C < 0x800){ $_String .= chr(0xC0 | $_C>>6); $_String .= chr(0x80 | $_C & 0x3F); }elseif($_C < 0x10000){ $_String .= chr(0xE0 | $_C>>12); $_String .= chr(0x80 | $_C>>6 & 0x3F); $_String .= chr(0x80 | $_C & 0x3F); }elseif($_C < 0x200000) { $_String .= chr(0xF0 | $_C>>18); $_String .= chr(0x80 | $_C>>12 & 0x3F); $_String .= chr(0x80 | $_C>>6 & 0x3F); $_String .= chr(0x80 | $_C & 0x3F); }else{ $_String = $_C; }
	 */
    return iconv('UTF-8', 'GB2312', $_String);
}

function _Array_Combine($_Arr1, $_Arr2)
{
    for ($i = 0; $i < count($_Arr1); $i++)
        $_Res [$_Arr1 [$i]] = $_Arr2 [$i];
    return $_Res;
}

function getStylepic($pic, $data)
{
    if ($pic) {
        echo '<a href="http://item.taobao.com/item.htm?id=' . $data ['numberID'] . '" title="点击查看样式" target="_blank"><img title="点击查看样式" src="http://' . $_SERVER ['SERVER_NAME'] . '/App/Tpl/Home/' . $data ['tpldir'] . '/Public/Styles/' . $data ['styledir'] . '/preview.jpg"></a>';
    }
}

function getFocuscode($code, $data)
{
    $str = '&lt;datacall:' . $data ['calltype'] . ' code="' . $code . '" /&gt;';
    echo $str;
}

function getFocusStyle($code)
{
    $Focusfile = DATA_PATH . '~datacall_focus.php';
    $list = include($Focusfile);
    $code = strtoupper($code);
    $focus = $list [$code];
    $str = $focus ["title"] . " (" . $focus ["focuswidth"] . "X" . $focus ["focusheight"] . "像素)";
    return $str;
}

/* 时间格式化 */
function toDate($time, $format = 'Y-m-d H:i:s')
{
    if (empty ($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date(($format), $time);
}

/* 默认缺省 */
function getDefault($isdefault, $imageShow = true)
{
    switch ($isdefault) {
        case 1 :
            $showText = '当前默认';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="当前默认" ALT="当前默认">';
            break;
        default :
            $showText = '';
            $showImg = '';
    }
    return ($imageShow === true) ? ($showImg) : $showText;
}

/* 状态处理 */
function getShowStatus($status, $imageShow = true)
{
    switch ($status) {
        case 0 :
            $showText = '不显示';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="不显示" ALT="不显示">';
            break;
        case 1 :
        default :
            $showText = '显示';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="显示" ALT="显示">';
    }
    return ($imageShow === true) ? ($showImg) : $showText;
}

/* 状态处理 */
function getStatus($status, $imageShow = true)
{
    switch ($status) {
        case 0 :
            $showText = '禁用';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="禁用" ALT="禁用">';
            break;
        case 2 :
            $showText = '待审';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/checkin.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="待审" ALT="待审">';
            break;
        case -1 :
            $showText = '删除';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="删除" ALT="删除">';
            break;
        case 1 :
        default :
            $showText = '正常';
            $showImg = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="正常" ALT="正常">';
    }
    return ($imageShow === true) ? ($showImg) : $showText;
}

/* 状态显示 */
function showStatus($status, $id)
{
    switch ($status) {
        case 0 :
            $info = '<a href="javascript:resume(' . $id . ')">恢复</a>';
            break;
        case 2 :
            $info = '<a href="javascript:pass(' . $id . ')">批准</a>';
            break;
        case 1 :
            $info = '<a href="javascript:forbid(' . $id . ')">禁用</a>';
            break;
        case -1 :
            $info = '<a href="javascript:recycle(' . $id . ')">还原</a>';
            break;
    }
    return $info;
}

/* 状态显示 */
function showRecommend($recommend, $id)
{
    switch ($recommend) {
        case 0 :
            $info = '<a href="javascript:recommend(' . $id . ')">推荐</a>';
            break;
        case 1 :
            $info = '<a href="javascript:unrecommend(' . $id . ')">取消推荐</a>';
            break;
    }
    return $info;
}

/* 组名 */
function getGroupName($id)
{
    if ($id == 0) {
        return '无上级组';
    }
    if ($list = F('groupName')) {
        return $list [$id];
    }
    $dao = D("Role");
    $list = $dao->where(array(
        'field' => 'id,name'
    ))->select();
    foreach ($list as $vo) {
        $nameList [$vo ['id']] = $vo ['name'];
    }
    $name = $nameList [$id];
    F('groupName', $nameList);
    return $name;
}

/* 显示文件扩展名 */
function showExt($ext, $pic = true)
{
    static $_extPic = array(
        'dir' => "folder.gif",
        'doc' => 'msoffice.gif',
        'rar' => 'rar.gif',
        'zip' => 'zip.gif',
        'txt' => 'text.gif',
        'pdf' => 'pdf.gif',
        'html' => 'html.gif',
        'png' => 'image.gif',
        'gif' => 'image.gif',
        'jpg' => 'image.gif',
        'php' => 'text.gif'
    );
    static $_extTxt = array(
        'dir' => '文件夹',
        'jpg' => 'JPEG图象'
    );
    if ($pic) {
        if (array_key_exists(strtolower($ext), $_extPic)) {
            $show = "<IMG SRC='" . WEB_PUBLIC_PATH . "/Images/extension/" . $_extPic [strtolower($ext)] . "' BORDER='0' alt='' align='absmiddle'>";
        } else {
            $show = "<IMG SRC='" . WEB_PUBLIC_PATH . "/Images/extension/common.gif' WIDTH='16' HEIGHT='16' BORDER='0' alt='文件' align='absmiddle'>";
        }
    } else {
        if (array_key_exists(strtolower($ext), $_extTxt)) {
            $show = $_extTxt [strtolower($ext)];
        } else {
            $show = $ext ? $ext : '文件夹';
        }
    }

    return $show;
}

function getTplid($id)
{
    $ids = explode('_', $id);
    return $ids [1];
}

/* 处理文件大小 */
function byte_format($size, $dec = 2)
{
    $a = array(
        "B",
        "KB",
        "MB",
        "GB",
        "TB",
        "PB"
    );
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a [$pos];
}

/* 置顶 */
function getTop($type)
{
    switch ($type) {
        case 1 :
            $icon = '<IMG SRC="' . APP_PUBLIC_PATH . '/images/top.gif" BORDER="0" align="absmiddle" ALT="">';
            break;
        default :
            $icon = '';
    }
    return $icon;
}

// 循环删除目录和文件函数
function delDirAndFile($dirName, $delDir = false)
{
    if ($handle = opendir($dirName)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir("$dirName/$file")) {
                    delDirAndFile("$dirName/$file", $delDir);
                } else {
                    unlink("$dirName/$file");
                }
            }
        }
        closedir($handle);
        if ($delDir)
            rmdir($dirName);
    }
}

function time2string($time)
{
    $second = $time - C('NOW_TIME');
    $timestr = array();
    $day = floor($second / (3600 * 24));
    if ($day > 0)
        $timestr [] = intval($day) . '天';
    $second = $second % (3600 * 24);
    if ($second > 0)
        $timestr [] = $hour = floor($second / 3600) . '小时';
    $second = $second % 3600;
    if ($second > 0)
        $timestr [] = $minute = floor($second / 60) . '分';
    if ($second > 0)
        $timestr [] = intval($second % 60) . '秒';
    return implode('', $timestr);
}

function get_spider_bot($useragent = '')
{ // 蜘蛛
    $spider = "";
    if (!$useragent) {
        $useragent = $_SERVER ['HTTP_USER_AGENT'];
    }
    $useragent = strtolower($useragent);
    if (strpos($useragent, 'baiduspider') !== false) {
        $spider = 'baidu';
    } else if (strpos($useragent, 'sosospider') !== false) {
        $spider = 'soso';
    } else if (strpos($useragent, 'sogou') !== false) {
        $spider = 'sogou';
    } else if (strpos($useragent, 'bing') !== false) {
        $spider = 'bing';
    } else if (strpos($useragent, 'googlebot') !== false) {
        $spider = 'google';
    } else if (strpos($useragent, 'yahoo') !== false) {
        $spider = 'yahoo';
    } else if (strpos($useragent, 'sohu-search') !== false) {
        $spider = 'sohu';
    } else if (strpos($useragent, 'msnbot') !== false) {
        $spider = 'msn';
    } else if (strpos($useragent, 'youdaobot') !== false) {
        $spider = 'youdao';
    } else if (strpos($useragent, 'yodaobot') !== false) {
        $spider = 'yodao';
    } else if (strpos($useragent, 'sinaweibobot') !== false) {
        $spider = 'weibo';
    } else if (strpos($useragent, 'robozilla') !== false) {
        $spider = 'robozilla';
    } else if (strpos($useragent, 'lycos') !== false) {
        $spider = 'lycos';
    } else if (strpos($useragent, 'ia_archiver') !== false || strpos($useragent, 'iaarchiver') !== false) {
        $spider = 'alexa';
    } else if (strpos($useragent, 'archive.org_bot') !== false) {
        $spider = 'archive';
    } else if (strpos($useragent, 'sitebot') !== false) {
        $spider = 'site';
    } else if (strpos($useragent, 'mj12bot') !== false) {
        $spider = 'mj12';
    } else if (strpos($useragent, 'gosospider') !== false) {
        $spider = 'goso';
    } else if (strpos($useragent, 'gigabot') !== false) {
        $spider = 'giga';
    } else if (strpos($useragent, 'yrspider') !== false) {
        $spider = 'yr';
    } else if (strpos($useragent, 'jikespider') !== false) {
        $spider = 'jike';
    } else if (strpos($useragent, 'testspider') !== false) {
        $spider = 'test';
    } else if (strpos($useragent, 'etaospider') !== false) {
        $spider = 'etao';
    } else if (strpos($useragent, 'wangidspider') !== false) {
        $spider = 'wangid';
    } else if (strpos($useragent, 'foxspider') !== false) {
        $spider = 'fox';
    } else if (strpos($useragent, 'docomo') !== false) {
        $spider = 'docomo';
    } else if (strpos($useragent, 'yandexbot') !== false) {
        $spider = 'yandex';
    } else if (strpos($useragent, 'catchbot') !== false) {
        $spider = 'catch';
    } else if (strpos($useragent, 'surveybot') !== false) {
        $spider = 'survey';
    } else if (strpos($useragent, 'dotbot') !== false) {
        $spider = 'dot';
    } else if (strpos($useragent, 'purebot') !== false) {
        $spider = 'pure';
    } else if (strpos($useragent, 'ccbot') !== false) {
        $spider = 'cc';
    } else if (strpos($useragent, 'mlbot') !== false) {
        $spider = 'ml';
    } else if (strpos($useragent, 'adsbot-google') !== false) {
        $spider = 'google-ads';
    } else if (strpos($useragent, 'ahrefsbot') !== false) {
        $spider = 'ahrefs';
    } else if (strpos($useragent, 'spbot') !== false) {
        $spider = 'sp';
    } else if (strpos($useragent, 'augustbot') !== false) {
        $spider = 'august';
    }
    return $spider;
}

function PingXML($blog)
{
    $pingxml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $pingxml .= "<methodCall>";
    $pingxml .= "<methodName>weblogUpdates.extendedPing</methodName>";
    $pingxml .= "<params>";
    $pingxml .= "<param>";
    $pingxml .= "<value><string>" . $blog ['title'] . "</string></value>";
    $pingxml .= "</param>";
    $pingxml .= "<param>";
    $pingxml .= "<value><string>" . $blog ['home'] . "</string></value>";
    $pingxml .= "</param>";
    $pingxml .= "<param>";
    $pingxml .= "<value><string>" . $blog ['url'] . "</string></value>";
    $pingxml .= "</param>";
    $pingxml .= "<param>";
    $pingxml .= "<value><string>" . $blog ['rss'] . "</string></value>";
    $pingxml .= "</param>";
    $pingxml .= "</params>";
    $pingxml .= "</methodCall>";
    return $pingxml;
}

function PingURL($PingURL, $PingXML)
{
    $ch = curl_init();
    $headers = array(
        "POST " . $PingURL . " HTTP/1.0",
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Content-length: " . strlen($PingXML)
    );
    curl_setopt($ch, CURLOPT_URL, $PingURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $PingXML);
    $Response = curl_exec($ch);
    curl_close($ch);
    return $Response;
}

function CheckPingStatus($Response)
{
    if (strpos($Response, "<boolean>0</boolean>") || strpos($Response, "<int>0</int>") || strpos($Response, ">0</"))
        return true;
    else
        return false;
}

function getSeoword($wd, $sword)
{
    $html = '<a class="sw_li" time="' . ($sword ['time'] ? 1 : 2) . '" dp="' . $sword ['dp'] . '" wid="' . md5($sword ['wd']) . '">' . $sword ['wd'] . '</a>';
    return $html;
}

function getBaiduUrl($wd = '', $bs = '', $type = 1, $p = 0)
{
    if ($wd) {
        $wd = rawurlencode($wd);
    } else {
        return false;
    }
    if ($bs)
        $bs = rawurlencode($bs);
    switch ($type) {
        case 1 :
            $url = 'http://www.baidu.com/s?wd=' . $wd . '&ie=utf-8&bs=' . $bs;
            break;
        case 2 :
            $mtime = getMicrotime();
            $url = 'http://suggestion.baidu.com/su?wd=' . $wd . '&p=3&cb=window.bdsug.sug&ie=utf-8&t=' . $mtime;
            break;
        case 3 :
            $url = 'http://index.baidu.com/main/word.php?word=' . $wd . '&ie=utf-8&area=0&time=0';
            break;
        case 4 :
            $pn = $p * 20;
            $url = 'http://news.baidu.com/ns?word=' . $wd . '&ie=utf-8&tn=newstitle&from=news&sr=0&cl=2&rn=20&ct=0&prevct=1&pn=' . $pn;
            break;
        default :
            $url = 'http://www.baidu.com/s?wd=' . $wd . '&ie=utf-8&bs=' . $bs;
    }
    return $url;
}

function getMicrotime()
{
    $time = explode(' ', microtime());
    $time [0] = intval($time [0] * 1000);
    $mtime = $time [1] * 1000 + $time [0];
    return $mtime;
}

function GetContent($Url, $PostData = array(), $Method = "GET", $CookieFile = "", $CookieFileSet = "")
{
    $Html = file_get_contents($Url);
    return $Html;
    $IsPost = $Method == "POST" ? 1 : 0;
    $ch = curl_init($Url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, $IsPost);
    curl_setopt($ch, CURLOPT_REFERER, '');
    // curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)');
    if ($PostData)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
    if ($CookieFile)
        curl_setopt($ch, CURLOPT_COOKIEJAR, $CookieFile);
    if ($CookieFileSet)
        curl_setopt($ch, CURLOPT_COOKIEFILE, $CookieFileSet);
    $Html = curl_exec($ch);
    curl_close($ch);
    return $Html;
}

function GetField($html, $startstr = "", $endstr = "", $matchnum = 0)
{
    if ($html == '')
        return '';
    $v = $html;
    if ($startstr && $endstr) {
        $regexStr = "/" . regexEncode($startstr) . "([\s\S]*?)" . regexEncode($endstr) . "/";
        if ($matchnum) {
            $regexnum = preg_match_all($regexStr, $html, $matches);
            if ($regexnum > $matchnum && $matchnum > 0) {
                foreach ($matches [1] as $mk => $mv) {
                    if ($mk >= $matchnum)
                        unset ($matches [1] [$mk]);
                }
            }
            $v = $matches [1];
        } else {
            $regexnum = preg_match($regexStr, $html, $matches);
            $v = $matches [1];
        }
    }
    return $v;
}

/* 字符串排序比较 */
function sortcmp($a, $b)
{
    if (strlen($a ['Key']) == strlen($b ['Key'])) {
        return 0;
    }
    return strlen($a ['Key']) < strlen($b ['Key']) ? 1 : -1;
}

function regexEncode($str)
{
    if (!$str)
        return $str;
    $str = str_replace("\\", "\\\\", $str);
    $str = str_replace(".", "\.", $str);
    $str = str_replace("[", "\[", $str);
    $str = str_replace("]", "\]", $str);
    $str = str_replace("(", "\(", $str);
    $str = str_replace(")", "\)", $str);
    $str = str_replace("?", "\?", $str);
    $str = str_replace("+", "\+", $str);
    $str = str_replace("*", "\*", $str);
    $str = str_replace("^", "\^", $str);
    $str = str_replace("{", "\{", $str);
    $str = str_replace("}", "\}", $str);
    $str = str_replace("$", "\$", $str);
    $str = str_replace("|", "\|", $str);
    $str = str_replace("/", "\/", $str);
    $str = str_replace("\[MYREF\]", "([\s\S]*?)", $str);
    $str = str_replace("\(\*\)", "[\s\S]*?", $str);
    return $str;
}

function ConvertChatSet($str_or_array, $SourceCharset = 'utf-8', $ObjectCharset = 'gb2312')
{
    if (!is_array($str_or_array) && empty ($str_or_array))
        return "";
    $from_encoding = strtolower($SourceCharset);
    $to_encoding = strtolower($ObjectCharset);
    $converarray = array();
    $from_encoding = str_replace("utf8", "utf-8", $from_encoding);
    $to_encoding = str_replace("utf8", "utf-8", $to_encoding);
    if ($from_encoding == $to_encoding)
        return $str_or_array;
    if (($from_encoding == "big5" && $to_encoding == "gb2312") || ($from_encoding == "gb2312" && $to_encoding == "big5"))
        $flag = false;
    else
        $flag = true;

    if (function_exists('mb_convert_encoding') && $to_encoding != 'pinyin' && $flag) {
        if (!is_array($str_or_array)) {
            return mb_convert_encoding($str_or_array, $to_encoding, $from_encoding);
        } else {
            foreach ($str_or_array as $key => $val) {
                $converarray [$key] = mb_convert_encoding($val, $to_encoding, $from_encoding);
            }
            return $converarray;
        }
    } elseif (function_exists('iconv') && $to_encoding != 'pinyin' && $flag) {
        if (!is_array($str_or_array)) {
            return iconv($from_encoding, $to_encoding, $str_or_array);
        } else {
            foreach ($str_or_array as $key => $val) {
                $converarray [$key] = iconv($from_encoding, $to_encoding, $val);
            }
            return $converarray;
        }
    }
}

function SeophpCode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key != '' ? $key : C('SEOPHP_KEY'));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey [$i] = ord($cryptkey [$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;
        $tmp = $box [$i];
        $box [$i] = $box [$j];
        $box [$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box [$a]) % 256;
        $tmp = $box [$a];
        $box [$a] = $box [$j];
        $box [$j] = $tmp;
        $result .= chr(ord($string [$i]) ^ ($box [($box [$a] + $box [$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

function StrToNum($Str, $Check, $Magic)
{
    $Int32Unit = 4294967296;

    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++) {
        $Check *= $Magic;
        if ($Check >= $Int32Unit) {
            $Check = ($Check - $Int32Unit * ( int )($Check / $Int32Unit));
            $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
        }
        $Check += ord($Str{$i});
    }
    return $Check;
}

function HashURL($String)
{
    $Check1 = StrToNum($String, 0x1505, 0x21);
    $Check2 = StrToNum($String, 0, 0x1003F);

    $Check1 >>= 2;
    $Check1 = (($Check1 >> 4) & 0x3FFFFC0) | ($Check1 & 0x3F);
    $Check1 = (($Check1 >> 4) & 0x3FFC00) | ($Check1 & 0x3FF);
    $Check1 = (($Check1 >> 4) & 0x3C000) | ($Check1 & 0x3FFF);

    $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) << 2) | ($Check2 & 0xF0F);
    $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000);

    return ($T1 | $T2);
}

function CheckHash($Hashnum)
{
    $CheckByte = 0;
    $Flag = 0;

    $HashStr = sprintf('%u', $Hashnum);
    $length = strlen($HashStr);

    for ($i = $length - 1; $i >= 0; $i--) {
        $Re = $HashStr{$i};
        if (1 === ($Flag % 2)) {
            $Re += $Re;
            $Re = ( int )($Re / 10) + ($Re % 10);
        }
        $CheckByte += $Re;
        $Flag++;
    }

    $CheckByte %= 10;
    if (0 !== $CheckByte) {
        $CheckByte = 10 - $CheckByte;
        if (1 === ($Flag % 2)) {
            if (1 === ($CheckByte % 2)) {
                $CheckByte += 9;
            }
            $CheckByte >>= 1;
        }
    }

    return '7' . $CheckByte . $HashStr;
}

function getch($url)
{
    return CheckHash(HashURL($url));
}

function getpr($url)
{
    $googlehost = 'toolbarqueries.google.com';
    $googleua = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 1.1.4322)';
    $ch = getch($url);
    $fp = fsockopen($googlehost, 80, $errno, $errstr, 30);
    $pr = 0;
    if ($fp) {
        $out = "GET /tbr?client=navclient-auto&ch=$ch&features=Rank:&q=info:$url HTTP/1.1\r\n";
        $out .= "User-Agent: $googleua\r\n";
        $out .= "Host: $googlehost\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        while (!feof($fp)) {
            $data = fgets($fp, 128);
            $pos = strpos($data, "Rank_");
            if ($pos === false) {
            } else {
                $pr = substr($data, $pos + 9);
                $pr = trim($pr);
                $pr = preg_replace('/[^\d]/', '', $pr);
                break;
            }
        }
        fclose($fp);
    }
    return $pr;
}

function getbr($url)
{
    $html = GetContent('http://www.aizhan.com/getbr.php?url=' . $url . '&style=1');
    $br = preg_replace('/[^\d]/', '', GetField($html, '<a(*)>', '</a>'));
    return $br;
}

function HtmlTrim($strHtml, $serial)
{
    // $serial = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17';
    if (!$serial)
        return $strHtml;
    $ids = explode(',', $serial);
    $aryReg = array(
        "/<a[^>]*?>([\s\S]*?)<\/a>/i",
        "/<h4[^>]*?>([\s\S]*?)<\/h4>/i",
        "/<br[^>]*?>/i",
        "/<table[^>]*?>([\s\S]*?)<\/table>/i",
        "/<tr[^>]*?>([\s\S]*?)<\/tr>/i",
        "/<td[^>]*?>([\s\S]*?)<\/td>/i",
        "/<p[^>]*?>([\s\S]*?)<\/p>/i",
        "/<font[^>]*?>([\s\S]*?)<\/font>/i",
        "/<div[^>]*?>([\s\S]*?)<\/div>/i",
        "/<span[^>]*?>([\s\S]*?)<\/span>/i",
        "/<tbody[^>]*?>([\s\S]*?)<\/tbody>/i",
        "/<([\/]?)b>/i",
        "/<([\/]?)strong>/i",
        "/<img[^>]*?>/i",
        "/[&nbsp;]{2,}/i",
        "/<script[^>]*?>([\w\W]*?)<\/script>/i",
        "/<object[^>]*?>([\w\W]*?)<\/object>/i",
        "/<!--([\w\W]*?)-->/i"
    );
    $aryRep = array(
        "\\1",
        "\\1",
        "",
        "\\1",
        "\\1",
        "\\1",
        "\\1",
        "\\1",
        "\\1",
        "\\1",
        "\\1",
        "",
        "",
        "",
        "&nbsp;",
        "",
        "",
        ""
    );
    $expBeginTag = array(
        "/<a[^>]*?>/i",
        "/<h4[^>]*?>/i",
        "/<br[^>]*?>/i",
        "/<table[^>]*?>/i",
        "/<tr[^>]*?>/i",
        "/<td[^>]*?>/i",
        "/<p[^>]*?>/i",
        "/<font[^>]*?>/i",
        "/<div[^>]*?>/i",
        "/<span[^>]*?>/i",
        "/<tbody[^>]*?>/i",
        "/<([\/]?)b>/i",
        "/<([\/]?)strong>/i",
        "/<img[^>]*?>/i",
        "/[&nbsp;]{2,}/i",
        "/<script[^>]*?>/i",
        "/<object[^>]*?>/i",
        "/<!--/i"
    );
    $expEndTag = array(
        "/<\/a>/i",
        "/<\/h4>/i",
        "/<\/br>/i",
        "/<\/table>/i",
        "/<\/tr>/i",
        "/<\/td>/i",
        "/<\/p>/i",
        "/<\/font>/i",
        "/<\/div>/i",
        "/<\/span>/i",
        "/<\/tbody>/i",
        "/<\/b>/i",
        "/<\/strong>/i",
        "/<img[^>]*?>/i",
        "/[&nbsp;]{2,}/i",
        "/<\/script>/i",
        "/<\/object>/i",
        "/-->/i"
    );
    $strOutput = $strHtml;
    foreach ($ids as $id) {
        $strOutput = preg_replace($aryReg [$id], $aryRep [$id], $strOutput);
        $strOutput = preg_replace($expBeginTag [$id], '', $strOutput);
        $strOutput = preg_replace($expEndTag [$id], '', $strOutput);
    }
    return $strOutput;
}

// 依据数值转换为名称
function print_depart_name($num)
{
    switch ($num) {
        case "0" :
            echo "『 基础分区 』";
            break;
        case "1" :
            echo "『 一分区 』";
            break;
        case "2" :
            echo "『 二分区 』";
            break;
        case "3" :
            echo "『 三分区 』";
            break;
        case "4" :
            echo "『 四分区 』";
            break;
        case "5" :
            echo "『 五分区 』";
            break;
        case "6" :
            echo "『 六分区 』";
            break;
        default :
            echo "『 $num 』";
    }

}

// 依据数值转换为名称
function print_name($num)
{
    switch ($num) {
        case "0" :
            echo "基础分区";
            break;
        case "1" :
            echo " 一分区 ";
            break;
        case "2" :
            echo " 二分区 ";
            break;
        case "3" :
            echo " 三分区 ";
            break;
        case "4" :
            echo " 四分区 ";
            break;
        case "5" :
            echo " 五分区 ";
            break;
        case "6" :
            echo " 六分区 ";
            break;
        default :
            echo " $num ";
    }
}

// 依据模块名和数值转换为数据库字段
function transform_to_string($model, $num)
{
    if ($model == "ServerDetail") {
        switch ($num) {
            case "0" :
                return "server_name";
                break;
            case "1" :
                return "depart_name";
                break;
            case "2" :
                return "network_ip";
                break;
            case "3" :
                return "external_ip";
                break;
            case "4" :
                return "note";
                break;
            case "5" :
                return "$num";
                break;
            default :
                return "$num";
        }
    } else {
        return $num;
    }
}

//echarts相关函数
// 格式化输出数组
function p($array)
{
    dump($array, 1, '<pre>', 0);
}

// 判断日期格式是否正确
// 可接受的日期格式为Y-m-d H:i:s
// $format为输出格式 $str为字符串
function isdate($str, $format = "Y-m-d H:i:s")
{
    $strArr = explode("-", $str);
    if (empty ($strArr)) {
        return false;
    }
    foreach ($strArr as $val) {
        if (strlen($val) < 2) {
            $val = "0" . $val;
        }
        $newArr [] = $val;
    }
    $str = implode("-", $newArr);
    $unixTime = strtotime($str);
    $checkDate = date($format, $unixTime);
    if ($checkDate == $str)
        return true;
    else
        return false;
}

// 将日期转换为时间戳,必须先做上面的类似的日期格式判断
// 传入日期后返回半小时之前的日期和当前传入的日期数组
// 传入日期后返回week的日期和当前传入的日期数组
// 传入日期后返回month的日期和当前传入的日期数组
function mdate_arr($time, $data_interval = 'day')
{
    if (empty ($time)) {
        $timestamp = time();
    }
    $catime = strtotime($time); // 日期转换为时间戳
    // 半小时之前的时间戳为
    switch ($data_interval) {
        case "day" :
            $half_timestamp = $catime - 1800;
            break;
        case "week" :
            $half_timestamp = strtotime(date('Y-m-d 00:00:00', $catime));
            break;
        case "month" :
            import('ORG.Util.Date'); // 导入日期类
            $Date = new Date ("$time");
            $Date->isLeapYear(); // 判断是否闰年
            $firtday = $Date->firstDayOfMonth();
            $half_timestamp = strtotime($firtday);
            break;
    }

    $half_before = date('Y-m-d H:i:s', $half_timestamp);
    /*
	 * $date_arrs=array(); $date_arrs['now']=$time; $date_arrs['half_before']=$half_before;
	*/
    return $half_before;
}

// 根据传入的日期计算改时间到0点到该时间的间隔组
function get_dates($time)
{
    $trans_timestamp = strtotime($time); // 转换为时间戳
    $start_date = date('Y-m-d 00:00:00', $trans_timestamp); // 开始的日期
    $start_timestamp = strtotime($start_date); // 开始的日期的时间戳
    $nowtime = date('Y-m-d H:i:s');
    if ($trans_timestamp > time()) {
        $trans_timestamp = time();
    }
    $date_array = array();
    while ($start_timestamp < $trans_timestamp) {
        $real_startdate = date('Y-m-d H:i:s', $start_timestamp);
        $start_timestamp = $start_timestamp + 1800;
        $next_date = date('Y-m-d H:i:s', $start_timestamp);
        /*
		 * $date_combine=$real_startdate.",".$next_date; $date_array["$real_startdate"]=$next_date;
		*/
        $date_array [] = $next_date;
    }
    return $date_array;
}

/**
 * 根据指定日期和1~7来获取周一至周日对应的日期
 *
 * @param string $date
 *            指定日期，为空则默认为当前天
 * @param int $weekday
 *            指定返回周几的日期（1~7），默认为返回周一对应的日期
 * @param string $format
 *            指定返回日期的格式
 * @return string
 */
function getWeekDay($date = '', $weekday = 1, $format = 'Y-m-d 00:00:00')
{
    $time = strtotime($date);
    $time = ($time == '') ? time() : $time;
    return date($format, $time - 86400 * (date('N', $time) - $weekday));
}

/**
 * @Descriptions 根据传入的日期计算获取该日期到周一的日期间隔组
 *
 * @param string $trans_timestamp
 *            传入的参数转换为时间戳
 * @param string $Mon_date
 *            取当前日期[传入日期的]的周一的日期,默认从0点开始计算
 * @param string $start_timestamp
 *            计算当天到23:59的时间戳
 * @return 数组
 * @version 1.0.0 (DATE)
 * @author SaltStack
 *
 */
function get_weeks($time)
{
    $trans_timestamp = strtotime($time); // 传参时间转换为时间戳
    if ($trans_timestamp > time()) {
        $trans_timestamp = time();
        $time = date('Y-m-d H:i:s', $trans_timestamp);
    }
    $Mon_date = getWeekDay($time); // 取当前日期的周一的日期默认从0点开始计算
    $start_timestamp = strtotime($Mon_date); // 开始(周一)的日期的时间戳
    $date_array = array();
    $dates_arr = array();
    while ($start_timestamp < $trans_timestamp) {
        $real_startdate = date('Y-m-d H:i:s', $start_timestamp);
        $start_timestamp = $start_timestamp + 86400; // 一天的时间
        $next_date = date('Y-m-d H:i:s', $start_timestamp);
        /*
		 * $date_combine=$real_startdate.",".$next_date; $date_array["$real_startdate"]=$next_date;
		*/
        $date_array [] = $next_date;
    }
    foreach ($date_array as $v) {
        $dates_arr [] = date('Y-m-d H:i:s', strtotime($v) - 1);
    }
    $end_arr = end($dates_arr);
    $end_timestamp = strtotime($end_arr);
    if ($end_timestamp >= $trans_timestamp) {
        $end_num = count($date_array) - 1;
        $dates_arr ["$end_num"] = $time;
    }
    return $dates_arr;
}

/*
 * 功能：获取指定年月日是星期几 传参：年月日格式：2010-01-01的字符串 返回值：计算出来的星期值
*/
function transition($date)
{
    $datearr = explode("-", $date); // 将传来的时间使用“-”分割成数组
    $year = $datearr [0]; // 获取年份
    $month = sprintf('%02d', $datearr [1]); // 获取月份
    $day = sprintf('%02d', $datearr [2]); // 获取日期
    $hour = $minute = $second = 0; // 默认时分秒均为0
    $dayofweek = mktime($hour, $minute, $second, $month, $day, $year); // 将时间转换成时间戳
    $weekarray = array(
        "星期日",
        "星期一",
        "星期二",
        "星期三",
        "星期四",
        "星期五",
        "星期六"
    );
    $week_num = date("w", $dayofweek);
    return $weekarray ["$week_num"];
}

// 根据传入的日期计算传输的所在的月份以及今年开始的月份 (默认从1月开始)
function get_months($time, $start_num = "1")
{
    $trans_timestamp = strtotime($time); // 传参时间转换为时间戳
    if ($trans_timestamp > time()) {
        $trans_timestamp = time();
        $time = date('Y-m-d H:i:s', $trans_timestamp);
    }
    import('ORG.Util.Date');
    $Date = new Date ($time);
    $Month_first_date = $Date->firstDayOfMonth(); // 取当前日期的月份的第一天
    $Month_num = date('m', $trans_timestamp); // 获取当前传入日期的月份
    $Year = date('Y', $trans_timestamp); // 获取当前传入日期的年份
    if ($Month_num != "01") {
        while ($start_num < $Month_num) {
            $date_string = "$Year-$start_num-01";
            $Date = new Date ($date_string);
            $lastday = $Date->lastDayOfMonth();
            $lastday_timestamp = strtotime($lastday);
            $lastMonDay = date('Y-m-d 23:59:59', $lastday_timestamp);
            $date_array [] = $lastMonDay;
            $start_num = $start_num + 1;
            /*
			 * $date_combine=$real_startdate.",".$next_date; $date_array["$real_startdate"]=$next_date;
			*/
        }
    } else {
        $date_array [0] = $time;
        return $date_array;
    }

    $end_num = count($date_array);
    $date_array ["$end_num"] = $time;
    return $date_array;
}

// 根据传入的数组计算每个月的每天的总和
function Jisun_sumof_month($array, $order_str = 'time', $count_str = 'verify')
{
    // 获取最后最后的日期比如31号数值
    $end_dateOf_month = end($array); // 返回时间和数值的数组
    $end_dateOf_time = $end_dateOf_month ["$order_str"]; // 时间精确到秒
    $end_dateOf_y = date('Y', strtotime($end_dateOf_time)); // 获取当前日期的年份
    $end_dateOf_m = date('m', strtotime($end_dateOf_time)); // 获取当前日期的月份
    $end_dateOf_d = date('d', strtotime($end_dateOf_time)); // 获取当月的最后一天
    $end_dateOf_d = $end_dateOf_d + 0; // 把0去掉
    // 从1号开始
    $i = 1;
    while ($i < $end_dateOf_d) {
        if ($i <= 9) {
            $date_of_daystr = "0" . $i;
        } else {
            $date_of_daystr = $i;
        }
        $datestr = $end_dateOf_y . "-" . $end_dateOf_m . "-" . $date_of_daystr;
        $start_time = "$datestr 00:00:00";
        $end_time = "$datestr 23:59:59";
        $start_timestamp = strtotime($start_time);
        $end_timestamp = strtotime($end_time);
        // echo "<br/>start: " . $start_time . " end: " . $end_time;
        $res_month_arr = array();
        // 循环二元数组
        foreach ($array as $key => $val) {
            $time = $val ["$order_str"]; // 取当月所有日期
            $timestamp = strtotime($time); // 当前所有日期转换为时间戳
            if ($timestamp < $end_timestamp && $timestamp > $start_timestamp) {
                $res_month_arr [] = $val ["$count_str"];
                // p($time);
            }
        }
        // p ( $res_month_arr );
        // p(max ( $res_month_arr ));
        $count_num [] = max($res_month_arr);
        $i = $i + 1;
    }
    $arr_num = count($count_num);
    $count_num ["$arr_num"] = $end_dateOf_month ["$count_str"];
    $month_array = array_sum($count_num);
    return $month_array;
}

function get_totalNum($str)
{
    preg_match_all('/:.*/is', $str, $arr);
    $num = str_replace(':', '', $arr [0] [0]);
    return $num;
}

// 根据名称获取表名
function get_tablename($arr, $name)
{
    foreach ($arr as $key => $val) {
        if ($name == "$key") {
            return $val;
        }
    }
}

//判断输入是否为IP
function isOk_ip($ip)
{
    if (preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip)) {
        return 1;
    } else {
        return 0;
    }
}

//根据模块名转化为中文字段以及其他需要转换的字段
/*$modelname:模块的名称
$getmodelarray:模块对应的数组*/
function  model_trans_cn($modelname, $getmodelarray = "null")
{
    $model_string = array();
    switch ($modelname) {
        case "ServerDetail":
            $model_string["main_title"] = "主机管理";
            $model_string["next_title"] = "com & 环境";
            $model_string["main_table_title"] = "Com环境服务器信息管理";
            $model_string["main_table_th"] = array('序号', '分区名称', '服务器名称', '公网IP', '内网IP', 'CPU', '内存', '数据盘', '状态', '备注', '操作');
            $model_string["main_table_tr"] = array('id', 'depart_name', 'server_name', 'external_ip', 'network_ip', 'cpu', 'mem', 'disk', 'status', 'note');
            $model_string["select_array"] = array('status' => array('启用' => '启用', '关闭' => '关闭'), 'depart_name' => array('『 基础分区 』' => '『 基础分区 』',
                '『 一分区 』' => '『 一分区 』', '『 二分区 』' => '『 二分区 』', '『 三分区 』' => '『 三分区 』', '『 四分区 』' => '『 四分区 』', '『 五分区 』' => '『 五分区 』',
                '『 六分区 』' => '『 六分区 』', '『 B2C 』 ' => '『 B2C 』'));
            return $model_string;
            break;
        case "HostZzb":
            $model_string["main_title"] = "主机管理";
            $model_string["next_title"] = "52zzb & 环境";
            $model_string["main_table_title"] = "52zzb环境服务器信息管理";
            $model_string["main_table_th"] = array('序号', '分区名称', '服务器名称或域名', '内网IP', '配置目录', '工程目录', '日志路径', '创建时间', '状态', '备注', '操作');
            $model_string["main_table_tr"] = array('id', 'depart_name', 'server_name', 'network_ip', 'config_dir', 'pro_dir', 'log_dir', 'created_time', 'status', 'note');
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'depart_name' => array('『 基础分区 』' => '『 基础分区 』', '『 三区 』' => '『 三区 』', '『 六区 』' => '『 六区 』')
            );
            return $model_string;
            break;

        case "HostOrg":
            $model_string["main_title"] = "主机管理";
            $model_string["next_title"] = "Org & 环境";
            $model_string["main_table_title"] = "Org环境服务器信息管理";
            $model_string["main_table_th"] = array('序号', '分区名称', '服务器名称或域名', '内网IP', '配置目录', '工程目录', '日志路径', '创建时间', '状态', '备注', '操作');
            $model_string["main_table_tr"] = array('id', 'depart_name', 'server_name', 'network_ip', 'config_dir', 'pro_dir', 'log_dir', 'created_time', 'status', 'note');
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'depart_name' => array('『 基础分区 』' => '『 基础分区 』', '『 三区 』' => '『 三区 』', '『 一区 』' => '『 一区 』')
            );
            return $model_string;
            break;


        case "HostNet":
            $model_string["main_title"] = "主机管理";
            $model_string["next_title"] = "Net & 环境";
            $model_string["main_table_title"] = "Net环境服务器信息管理";
            $model_string["main_table_th"] = array('序号', '分区名称', '服务器名称或域名', '内网IP', '配置目录', '工程目录', '日志路径', '创建时间', '状态', '备注', '操作');
            $model_string["main_table_tr"] = array('id', 'depart_name', 'server_name', 'network_ip', 'config_dir', 'pro_dir', 'log_dir', 'created_time', 'status', 'note');
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'depart_name' => array('『 基础分区 』' => '『 基础分区 』', '『 三区 』' => '『 三区 』', '『 一区 』' => '『 一区 』')
            );
            return $model_string;
            break;


        case "SvnManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "SVN账号管理"; //首页显示位置
            $model_string["main_table_title"] = "内网环境SVN账号信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '账号', '密码', '服务器地址', '账号所有者', '账号所在组', '创建时间', '目录的权限', '使用状态', '备注', '操作'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'account', 'password', 'network_ip', 'owner', 'owner_group', 'created_time', 'directory_pri', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'network_ip' => array('『192.168.3.152』' => '『192.168.3.152』', '『192.168.3.65』' => '『192.168.3.65』')
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "Mysql": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "MYSQL列表"; //首页显示位置
            $model_string["main_table_title"] = "生产环境MYSQL账号信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '名称', '数据库地址', '实例类型', '容量', 'root密码', 'insconn密码', '映射机器', '映射端口', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'name', 'network_ip', 'type', 'disk', 'root_passwd', 'insconn_passwd', 'map_server', 'map_port', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "MysqlManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "MYSQL账号申请"; //首页显示位置
            $model_string["main_table_title"] = "生产环境MYSQL账号申请信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '申请名称', '申请人', '数据库IP', '数据库名', '申请权限', '申请账号', '申请密码', '映射服务器外网IP', '映射服务器端口', '状态'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'apply_server_name', 'apply_people', 'apply_server_ip', 'apply_database', 'apply_pri', 'apply_account', 'apply_passwd', 'maping_external_ip', 'maping_port', 'status'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'isslave' => array('是' => '是', '否' => '否'),
                'maping_status' => array('是' => '是', '否' => '否'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "ErrorRecord": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "故障管理"; //首页显示位置
            $model_string["next_title"] = "故障详情记录"; //首页显示位置
            $model_string["main_table_title"] = "生产环境故障详情信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '处理人', '应用名称', '故障简介', '故障类型', '恢复情况', '影响时间', '故障等级', '创建时间', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'processors', 'app_name', 'error_brief', 'error_type', 'restore_situation', 'affect_time', 'malfuction_level', 'create_time', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'restore_situation' => array('已恢复' => '已恢复', '未恢复' => '未恢复'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;


        case "VpnManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "VPN账号管理"; //首页显示位置
            $model_string["main_table_title"] = "内网环境VPN账号信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '账号', '密码', 'VPN服务器地址', '账号所有者', '创建时间', '账号的有效期', '使用状态', '备注', '操作'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'account', 'password', 'network_ip', 'owner', 'created_time', 'expire_date', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "SysManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "Linux系统账号管理"; //首页显示位置
            $model_string["main_table_title"] = "生产环境Linux系统账号信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '账号', '密码', '服务器地址', '账号所有者', '创建时间', '账号的有效期', '使用状态', '备注', '操作'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'account', 'password', 'network_ip', 'owner', 'created_time', 'account_expire', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "NeiManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "内网账号管理"; //首页显示位置
            $model_string["main_table_title"] = "内网账号信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '账号', '密码', '服务器地址', '账号所有者', '创建时间', '账号的有效期', '使用状态', '备注', '操作'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'account', 'password', 'network_ip', 'owner', 'created_time', 'account_expire', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "OtherManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "其他账号管理"; //首页显示位置
            $model_string["main_table_title"] = "内网账号信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '账号', '密码', '服务器地址', '账号所有者', '创建时间', '账号的有效期', '使用状态', '备注', '操作'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'account', 'password', 'network_ip', 'owner', 'created_time', 'account_expire', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "DnsManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "账号管理"; //首页显示位置
            $model_string["next_title"] = "DNS账号管理"; //首页显示位置
            $model_string["main_table_title"] = "DNS账号信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '主域名', '主域名用途', '账号所有者', '账号', '账号有效期', '密码', '创建时间', '使用状态', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'site', 'use', 'owner', 'zhuce_account', 'expire', 'zhuce_passwd', 'created_time', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "ScriptsManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "自动化管理"; //首页显示位置
            $model_string["next_title"] = "自动化脚本管理"; //首页显示位置
            $model_string["main_table_title"] = "自动化脚本信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '脚本创建者', '简要说明', '脚本目的', '脚本详细内容', '是否在生产运行', '执行情况', '创建时间', '更新记录的时间', '使用状态', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'account', 'brirf', 'goal', 'desc', 'is_com', 'run_status', 'created_time', 'updated_time', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'is_com' => array('是' => '是', '否' => '否'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "DeployManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "自动化管理"; //首页显示位置
            $model_string["next_title"] = "自动化发布管理"; //首页显示位置
            $model_string["main_table_title"] = "自动化发布信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '发布版本', '发布人', '发布时间', '发布内容', '发布系统数量', '发布系统总共花费时间', '创建时间', '更新记录的时间', '使用状态', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'deploy_version', 'deploy_people', 'deploy_time', 'deploy_content', 'deploy_num', 'deploy_take', 'created_time', 'updated_time', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;


        case "CronManage": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "自动化管理"; //首页显示位置
            $model_string["next_title"] = "自动化任务管理"; //首页显示位置
            $model_string["main_table_title"] = "自动化任务信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '计划任务简介', '计划任务服务器', '计划任务内容', '计划任务目的', '计划任务开始时间', '计划任务结束时间', '计划任务执行情况', '创建时间', '更新记录的时间', '使用状态', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'plan_brif', 'plan_network_ip', 'plan_content', 'plan_goal', 'plan_run_start_time', 'plan_run_end_time', 'plan_run_stuation', 'created_time', 'updated_time', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;


        case "LoadCheck": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "巡检管理"; //首页显示位置
            $model_string["next_title"] = "系统负载情况检查记录"; //首页显示位置
            $model_string["main_table_title"] = "生产系统负载检查信息表"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '检查人', '检查的服务器', '检查的内容', '检查周', '检查的日期', '检查情况', '检查的详细内容', '创建时间', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'check_people', 'check_network_ip', 'check_content', 'check_week', 'check_date', 'check_stuation', 'check_desc', 'created_time', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'check_content' => array('负载' => '负载', '错误日志' => '错误日志', '数据备份' => '数据备份'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "LogCheck": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "巡检管理"; //首页显示位置
            $model_string["next_title"] = "系统错误日志检查记录"; //首页显示位置
            $model_string["main_table_title"] = "生产系统错误日志检查信息表"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '检查人', '检查的服务器', '检查的内容', '检查周', '检查的日期', '检查情况', '检查的详细内容', '创建时间', '备注','操作'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'check_people', 'check_network_ip', 'check_content', 'check_week', 'check_date', 'check_stuation', 'check_desc', 'created_time', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'check_content' => array('负载' => '负载', '错误日志' => '错误日志', '数据备份' => '数据备份'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "DbCheck": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "巡检管理"; //首页显示位置
            $model_string["next_title"] = "系统数据备份检查记录"; //首页显示位置
            $model_string["main_table_title"] = "生产系统数据备份检查信息表"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '检查人', '检查的服务器', '检查的内容', '检查周', '检查的日期', '检查情况', '检查的详细内容', '创建时间', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'check_people', 'check_network_ip', 'check_content', 'check_week', 'check_date', 'check_stuation', 'check_desc', 'created_time', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
                'check_content' => array('负载' => '负载', '错误日志' => '错误日志', '数据备份' => '数据备份'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "EmailCheck": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "巡检管理"; //首页显示位置
            $model_string["next_title"] = "邮件问题检查记录"; //首页显示位置
            $model_string["main_table_title"] = "生产系统邮件问题检查信息表"; //列表的大标题
            $model_string["main_table_th"] = array('序号', '值班人', '涉及服务器', '主要问题', '第几周', '发生故障日期', '具体情况', '详细描述', '创建时间', '更新记录的时间', '状态', '备注'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id', 'check_people', 'check_network_ip', 'check_content', 'check_week', 'check_date', 'check_stuation', 'check_desc', 'created_time', 'updated_time', 'status', 'note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

        case "AddModule": //模块名称，模块的名称和数据库表字段一致 （如 模块名为 ServerDetail 那么它的数据库表的名称为 表前缀+server_detail ,最终的表名为pre_server_detail）
            $model_string["main_title"] = "模块管理"; //首页显示位置
            $model_string["next_title"] = "模块管理"; //首页显示位置
            $model_string["main_table_title"] = "模块信息管理"; //列表的大标题
            $model_string["main_table_th"] = array('序号','自定义模块的名称','首页第一个标题','首页显示第二个位置','列表的大标题','下拉框的字段对应的id','创建时间','更新记录的时间','使用状态','备注','操作'); //列表的第一行标题
            $model_string["main_table_tr"] = array('id','model_name','main_title','next_title','main_table_title','drop_down_id','created_time','updated_time','status','note'); //列表的详细内容字段
            $model_string["select_array"] = array(
                'status' => array('启用' => '启用', '关闭' => '关闭'),
            ); //下拉框对应的字段和显示
            return $model_string;
            break;

    }

    //自动配置的模块的相关数据
    if ($getmodelarray) {
        $model_string["main_title"] = $getmodelarray["main_title"]; //首页显示第一个位置
        $model_string["next_title"] = $getmodelarray["next_title"]; //首页显示第二个位置
        $model_string["main_table_title"] = $getmodelarray["main_table_title"]; //列表的大标题
        $model_string["main_table_th"] = $getmodelarray["main_table_th"]; //列表的第一行标题
        $model_string["main_table_tr"] = $getmodelarray["main_table_tr"]; //列表的详细内容字段
        $model_string["select_array"] = $getmodelarray["select_array"]; //下拉框对应的字段和显示
        return $model_string;

    }


}


?>
