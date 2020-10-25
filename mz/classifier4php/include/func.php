<?php
/**
 * Created by PhpStorm.
 * User: djunny
 * Date: 2015-11-02
 * Time: 15:42
 */
if (php_sapi_name() == 'cli') {
    ob_implicit_flush(1);
}

function get_trans_data($msg) {
    //初始化类
    static $pa = NULL;
    if ($pa == NULL) {
        $pa = new PhpAnalysis('utf-8', 'utf-8');
    }
    //执行分词
    $pa->SetSource($msg);
    $pa->StartAnalysis(true);

    $res = $pa->GetFinallyResult(chr(2), true);
    $res = explode(chr(2), trim($res));
    $tag_map = array();
    foreach ($res as &$w) {
        $a = explode('/', $w);
        $s = array_pop($a);
        $t = preg_replace('#\d+$#is', '', $s);
        $w = implode('/', $a);
        if (in_array($t[0], array('n', 'i', 'a', 'v', 'l'))) {
            $tag_map[] = $w;
        }
    }
    return $tag_map;
}

function dump_var($data) {
    if (is_array($data)) {
        $str = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $str .= '[' . $k . '=' . dump_var($v) . ']';
            } else {
                $str .= encoding::iconv('[' . $k . '=' . $v . ']');
            }
        }
        return $str;
    } else {
        return encoding::iconv('[' . $data . ']');
    }
}

function l() {
    $arg_list = func_get_args();
    $log = '';
    for ($i = 0, $l = func_num_args(); $i < $l; $i++) {
        $log .= dump_var($arg_list[$i]);
    }
    $log = "[" . date('H:i:s') . "]" . $log . "\r\n";
    if (php_sapi_name() == 'cli') {
        echo $log;
    }
}

?>