<?php
/*
*   Package:        PHPCrazy
*   Link:           http://zhangyun.org/
*   Author:         Crazy <mailzhangyun@qq.com>
*   Copyright:      2014-2015 Crazy
*   License:        Please read the LICENSE file.
*/

/////////////////////// 杂项函数 ///////////////////////////


/*
*   生产一个随机密码
*   RandString(密码长度, 关键词)
*/
function RandString($len = 16, $keyword = '') {
    if (strlen($keyword) > $len) {//关键字不能比总长度长
        return false;
    }
    $str = '';
    $chars = 'abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHIJKMNPQRSTUVWXYZ'; //去掉1跟字母l防混淆            
    if ($len > strlen($chars)) {//位数过长重复字符串一定次数
        $chars = str_repeat($chars, ceil($len / strlen($chars)));
    }
    $chars = str_shuffle($chars); //打乱字符串
    $str = substr($chars, 0, $len);
    if (!empty($keyword)) {
        $start = $len - strlen($keyword);
        $str = substr_replace($str, $keyword, mt_rand(0, $start), strlen($keyword)); //从随机位置插入关键字
    }
    return $str;
}

/*
*   创建一个下拉框
*   Select(下拉框数组, 选中值, 下拉框名)
*/
function Select($selectArray, $selectedValue, $selectParameter) {

    $select = '<select '. $selectParameter .'>';

    foreach ($selectArray as $key => $value) {
        
        $selected = ( $selectedValue == $key ) ? ' selected="selected"' : '';

        $select .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';

    }

    $select .= '</select>';

    return $select;
    
}

/*
*   创建一个时区下拉框
*   LangSel(默认语言, 下拉框名)
*/
function LangSel($DefaultLang, $selectParameter) {

    $lang_dir = ROOT_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR;

    $select = '<select '.$selectParameter.'>';

    $dir = @opendir($lang_dir);

    while( $file = @readdir($dir) ) {
        
        if( preg_match("/^[0-9a-zA-Z_\-]*?$/", $file) ) {
            
            $selected = ( $DefaultLang == $file ) ? ' selected="selected"' : '';

            $TranslationFile = $lang_dir.$file.DIRECTORY_SEPARATOR.'Translation.php';

            if (file_exists($TranslationFile)) {
                
                $TranslateLang = @include $TranslationFile;

                $select .= '<option value="' . $file . '"' . $selected . '>' . $TranslateLang . '</option>';
            }

        }
    }

    @closedir($dir);

    $select .= '</select>';

    return $select;

}

/*
*   创建一个主题下拉框
*   ThemeSel(默认主题, 下拉框名)
*/
function ThemeSel($DefaultTheme, $selectParameter) {

    // 存放主题目录
    $ThemesDir = ROOT_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR;

    $select = '<select '.$selectParameter.'>';

    $dir = @opendir($ThemesDir);

    while( $file = @readdir($dir) ) {
        
        if( preg_match("/^[0-9a-zA-Z_\-]*?$/", $file) ) {
                
            $selected = ( $DefaultTheme == $file ) ? ' selected="selected"' : '';

            $ThemeFile = $ThemesDir.$file.DIRECTORY_SEPARATOR.'Theme.php';

            if (file_exists($ThemeFile)) {
                
                $ThemeName = @include $ThemeFile;

                $select .= '<option value="' . $file . '"' . $selected . '>' . $ThemeName . '</option>';
            }
        }
    }

    @closedir($dir);

    $select .= '</select>';

    return $select;

}

/*
*   使用邮件模板
*   UseEmailTpl(模板名称, 即将替换的模板伪变量)
*/
function UseEmailTpl($tpl_name, $tpl_vars = array()) {

    $filename = ROOT_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$GLOBALS['C']['lang'].DIRECTORY_SEPARATOR.'email'.DIRECTORY_SEPARATOR.$tpl_name . '.html';

    if (!file_exists($filename)) {
        
        //$filename = ROOT_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'email_tpl'.DIRECTORY_SEPARATOR.'zh-cn'.DIRECTORY_SEPARATOR.$tpl_name . '.html';
        
        //if (!file_exists($filename)) {
        throw new Exception(sprintf(L('模版 文件 不存在'), $tpl_name));

        //}
    }

    if (!($fd = @fopen($filename, 'r'))) {

        throw new Exception(sprintf(L('模版 文件 无法打开')));
    }

    $tpl_body = @fread($fd, filesize($filename));

    if (!empty($tpl_vars)) {
        foreach ($tpl_vars as $var_key => $var_value) {
            $tpl_body = str_replace('{' . $var_key . '}', $var_value, $tpl_body);
        }   
    }

    return $tpl_body;
}
?>