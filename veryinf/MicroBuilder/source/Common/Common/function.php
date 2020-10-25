<?php

/**
 * @param string $url
 * @param string $vars
 * @return string
 */
function AU($url = '', $vars = '') {
    $entry = parse_name(MODULE_NAME);
    if($entry == 'app') {
        return MAU($url, $vars);
    } else {
        return WAU($url, $vars);
    }
}

function MU() {
    
}

function WU() {
    
}

function MAU($url = '', $vars = '') {
    if(!defined('MODULE_NAME') || !defined('ADDON_NAME')) {
        trigger_error('当前上下文不支持这个函数AU', E_USER_ERROR);
    }
    $addon = parse_name(ADDON_NAME);
    $url = "/extend/{$addon}/{$url}";
    $url = U($url, $vars);
    $info = pathinfo(__APP__);
    if($info['dirname'] == '/') {
        $root = '/m';
    } else {
        $root = substr($info['dirname'], 0, -1) . 'm';
    }
    if($info['basename'] == 'index.php') {
        $root .= '/' . $info['basename'];
    }
    
    $len = strlen(__APP__);
    $url = substr($url, $len);
    return $root . $url;
}

function WAU($url = '', $vars = '') {
    if(!defined('MODULE_NAME') || !defined('ADDON_NAME')) {
        trigger_error('当前上下文不支持这个函数AU', E_USER_ERROR);
    }
    $entry = parse_name(MODULE_NAME);
    $addon = parse_name(ADDON_NAME);
    $url = "/{$entry}/extend/{$addon}/{$url}";
    $url = U($url, $vars);
    $info = pathinfo(__APP__);
    if($info['dirname'] == '/') {
        $root = '/w';
    } else {
        $root = substr($info['dirname'], 0, -1) . 'w';
    }
    if($info['basename'] == 'index.php') {
        $root .= '/' . $info['basename'];
    }

    $len = strlen(__APP__);
    $url = substr($url, $len);
    return $root . $url;
}

function inputRaw($jsonDecode = true) {
    $post = file_get_contents('php://input');
    if($jsonDecode) {
        $post = @json_decode($post, true);
    }
    return $post;
}

function attach($path) {
    if(stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0) {
        return $path;
    } else {
        return __SITE__ . 'attachment/' . $path;
    }
}

function coll_key($ds, $key) {
    if(!empty($ds) && !empty($key)) {
        $ret = array();
        foreach($ds as $row) {
            $ret[$row[$key]] = $row;
        }
        return $ret;
    }
    return array();
}

function coll_neaten($ds, $key) {
    if(!empty($ds) && !empty($key)) {
        $ret = array();
        foreach($ds as $row) {
            $ret[] = $row[$key];
        }
        return $ret;
    }
    return array();
}

function coll_walk($ds, $callback, $key = null) {
    if(!empty($ds) && is_callable($callback)) {
        $ret = array();
        if(!empty($key)) {
            foreach($ds as $k => $row) {
                $ret[$k] = call_user_func($callback, $row[$key]);
            }
        } else {
            foreach($ds as $k => $row) {
                $ret[$k] = call_user_func($callback, $row);
            }
        }
        return $ret;
    }
    return array();
}

/**
 * 该函数从一个数组中取得若干元素。
 * 该函数测试（传入）数组的每个键值是否在（目标）数组中已定义；
 * 如果一个键值不存在，该键值所对应的值将被置为FALSE，
 * 或者你可以通过传入的第3个参数来指定默认的值。
 *
 * @param array $keys 需要筛选的键名列表
 * @param array $src 要进行筛选的数组
 * @param mixed $default 如果原数组未定义某个键，则使用此默认值返回
 * @return array
 */
function coll_elements($keys, $src, $default = false) {
    $return = array();
    if(!is_array($keys)) {
        $keys = array($keys);
    }
    foreach($keys as $key) {
        if(isset($src[$key])) {
            $return[$key] = $src[$key];
        } else {
            $return[$key] = $default;
        }
    }
    return $return;
}

/**
 * 生成分页数据
 * @param int $currentPage 当前页码
 * @param int $totalCount 总记录数
 * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
 * @param int $pageSize 分页大小
 * @return string 分页HTML
 */
function pagination($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '')) {
    global $_W;
    $pdata = array(
        'tcount' => 0,
        'tpage' => 0,
        'cindex' => 0,
        'findex' => 0,
        'pindex' => 0,
        'nindex' => 0,
        'lindex' => 0,
        'options' => ''
    );
    if($context['ajaxcallback']) {
        $context['isajax'] = true;
    }

    $pdata['tcount'] = $tcount;
    $pdata['tpage'] = ceil($tcount / $psize);
    if($pdata['tpage'] <= 1) {
        return '';
    }
    $cindex = $pindex;
    $cindex = min($cindex, $pdata['tpage']);
    $cindex = max($cindex, 1);
    $pdata['cindex'] = $cindex;
    $pdata['findex'] = 1;
    $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
    $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
    $pdata['lindex'] = $pdata['tpage'];

    if($context['isajax']) {
        if(!$url) {
            $url = $_W['script_name'] . '?' . http_build_query($_GET);
        }
        $pdata['faa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', ' . $context['ajaxcallback'] . ')"';
        $pdata['paa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', ' . $context['ajaxcallback'] . ')"';
        $pdata['naa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', ' . $context['ajaxcallback'] . ')"';
        $pdata['laa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', ' . $context['ajaxcallback'] . ')"';
    } else {
        if($url) {
            $pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
            $pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
            $pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
            $pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
        } else {
            $_GET['page'] = $pdata['findex'];
            $pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
            $_GET['page'] = $pdata['pindex'];
            $pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
            $_GET['page'] = $pdata['nindex'];
            $pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
            $_GET['page'] = $pdata['lindex'];
            $pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
        }
    }

    $html = '<div><ul class="pagination pagination-centered">';
    if($pdata['cindex'] > 1) {
        $html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
        $html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
    }
    //页码算法：前5后4，不足10位补齐
    if(!$context['before'] && $context['before'] != 0) {
        $context['before'] = 5;
    }
    if(!$context['after'] && $context['after'] != 0) {
        $context['after'] = 4;
    }

    if($context['after'] != 0 && $context['before'] != 0) {
        $range = array();
        $range['start'] = max(1, $pdata['cindex'] - $context['before']);
        $range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
        if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
            $range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
            $range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
        }
        for ($i = $range['start']; $i <= $range['end']; $i++) {
            if($context['isajax']) {
                $aa = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', ' . $context['ajaxcallback'] . ')"';
            } else {
                if($url) {
                    $aa = 'href="?' . str_replace('*', $i, $url) . '"';
                } else {
                    $_GET['page'] = $i;
                    $aa = 'href="?' . http_build_query($_GET) . '"';
                }
            }
            $html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
        }
    }

    if($pdata['cindex'] < $pdata['tpage']) {
        $html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
        $html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
    }
    $html .= '</ul></div>';
    return $html;
}

function util_random($length, $numeric = false) {
    $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    if($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

/**
 * 判断一个数是否介于一个区间或将一个数转换为此区间的数.
 *
 * @param string $num 输入参数
 * @param int $downline 参数下限
 * @param int $upline 参数上限
 * @param string $returnNear 对输入参数是判断还是返回
 * @return boolean | number
 * <pre>
 *  boolean 判断输入参数是否介于 $downline 和 $upline 之间
 *  number 将输入参数转换为介于  $downline 和 $upline 之间的整数
 * </pre>
 */
function util_limit($num, $downline, $upline, $returnNear = true) {
    $num = intval($num);
    $downline = intval($downline);
    $upline = intval($upline);
    if($num < $downline){
        return empty($returnNear) ? false : $downline;
    } elseif ($num > $upline) {
        return empty($returnNear) ? false : $upline;
    } else {
        return empty($returnNear) ? true : $num;
    }
}

function util_curd($instance, $prefix, $dos = array()) {
    $curd = array('list', 'create', 'modify', 'delete');
    $dos = array_merge($curd, $dos);
    $do = I('get.do');
    $do = in_array($do, $dos) ? $do : $dos[0];
    $method = $prefix . ucfirst($do);
    if(method_exists($instance, $method)) {
        call_user_func(array($instance, $method));
        return '';
    }
    return $do;
}

/**
 * 将一个数组转换为 XML 结构的字符串
 *
 * @param array $arr 要转换的数组
 * @param int $isRoot 节点层级, 1 为 Root.
 * @return string XML 结构的字符串
 */
function util_2xml($arr, $isRoot = 1) {
    $s = $isRoot == 1 ? "<xml>" : '';
    foreach($arr as $tagname => $value) {
        if (is_numeric($tagname)) {
            $tagname = $value['TagName'];
            unset($value['TagName']);
        }
        if(!is_array($value)) {
            $s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
        } else {
            $s .= "<{$tagname}>" . util_2xml($value, $isRoot + 1)."</{$tagname}>";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    return $isRoot == 1 ? $s."</xml>" : $s;
}

/**
 * 构造错误数组
 *
 * @param int $errno 错误码，0为无任何错误。
 * @param string $errormsg 错误信息，通知上层应用具体错误信息。
 * @return array
 */
function error($errno, $message = '') {
    return array(
        'errno' => $errno,
        'message' => $message,
    );
}

/**
 * 检测返回值是否产生错误
 *
 * 产生错误则返回true，否则返回false
 *
 * @param mixed $data 待检测的数据
 * @return boolean
 */
function is_error($data) {
    if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
        return false;
    } else {
        return true;
    }
}


function import_third($class) {
    import('Third.' . $class, MB_ROOT . 'source/Core/', '.php');
}
