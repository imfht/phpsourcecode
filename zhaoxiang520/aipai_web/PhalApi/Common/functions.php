<?php
/**
 * 公共函数
 * @since   2016-08-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

/**
 * 国际化翻译
 * @param string $item 关键翻译因子
 * @param string $lang 目标语言 zh_cn|en
 * @return mixed
 */
function T( $item = '', $lang = ''){
    return (new \PhalApi\Core\Translate($lang))->get($item);
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 * @link http://www.thinkphp.cn
 */
function dump($var, $echo=true, $strict=true) {
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else{
        return $output;
    }
}