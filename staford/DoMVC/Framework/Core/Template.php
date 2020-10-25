<?php

/**
 * 模板引擎类文件
 * @author 暮雨秋晨
 * @copyright 2014
 */
require_once 'V/TemplateCheck.php';
require_once 'V/TemplateCompile.php';
require_once 'V/TemplateException.php';

class Template
{
    private $arr_vars = array(); //存储模板变量的数组

    private static $str_sfx = 'html'; //模板后缀

    private static $str_cpl = 'compile'; //编译文件

    private static $str_tpl = 'default'; //模板文件

    private static $str_cpl_dir = null; //编译完整路径

    private static $str_tpl_dir = null; //模板完整路径

    private static $obj_cpl = null; //存放编译实例化对象

    private static $obj_chk = null; //存放语法检查实例化对象

    public function __construct()
    {
        self::$str_cpl_dir = RTM_DIR . self::$str_cpl . DS;
        self::$str_tpl_dir = TPL_DIR . self::$str_tpl . DS;
        self::$obj_cpl = new TemplateCompile(self::$str_tpl_dir, self::$str_tpl);
        self::$obj_chk = new TemplateCheck();
    }

    public static function setCfg(array $cfg)
    {
        (isset($cfg['cpl']) && self::$str_cpl = $cfg['cpl']);
        (isset($cfg['tpl']) && self::$str_tpl = $cfg['tpl']);
        (isset($cfg['sfx']) && self::$str_sfx = $cfg['sfx']);
    }

    private function chk_syntax($res, $tpl)
    {
        self::$obj_chk->setFile($tpl);
        self::$obj_chk->syntax_check($res);
    }

    private function compile($tpl_path, $cpl_path)
    {
        //传入模板路径和编译存放路径
        if (!is_file($cpl_path) || filemtime($cpl_path) < filemtime($tpl_path)) {
            @mkdir(dirname($cpl_path), 0777, true);
            $res = file_get_contents($tpl_path);
            $this->chk_syntax($res, $tpl_path);
            $res = self::$obj_cpl->compile($res);
            if (!file_put_contents($cpl_path, $res, LOCK_EX)) {
                throw new TemplateException('无法生成编译文件', 2);
            }
        }
        return $cpl_path;
    }

    private function _include($tpl)
    {
        return $this->compile(self::$str_tpl_dir . str_replace('/', DS, $tpl), self::$str_cpl_dir .
            str_replace('/', DS, $tpl) . '.php');
    }

    /**
     * 分派模板变量
     */
    public function assign($key, $val = null)
    {
        return $this->arr_vars[trim($key)] = $val;
    }

    /**
     * 展示页面
     */
    public function display($tpl = null)
    {
        $tpl = ($tpl == null) ? $_GET['c'] . '/' . $_GET['a'] . '.' . self::$str_sfx : $tpl .
            '.' . self::$str_sfx;

        $tpl_path = self::$str_tpl_dir . str_replace('/', DS, $tpl);
        $cpl_path = self::$str_cpl_dir . str_replace('/', DS, $tpl) . '.php';

        if (!is_file($cpl_path) || filemtime($cpl_path) < filemtime($tpl_path)) {
            $this->compile($tpl_path, $cpl_path);
        }

        extract($this->arr_vars);
        include $cpl_path;
    }
}

?>