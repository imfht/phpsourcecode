<?php

/**
 * 模板引擎类文件
 * @abstract 总管有关模板的处理
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Template
{
    private $TPL_VARIABLE = array(); //存储模板变量的数组
    private static $TPL_NAME = 'default'; //当前模板名称
    private static $TPL_SUFFIX = 'php'; //模板后缀

    /**
     * @name setTemplate
     * @abstract 设置项目模板名称，该名称为VIEW常量下的模板文件夹的名称。
     * @param string $name 模板名
     * @return bool
     */
    public static function setTemplate($name)
    {
        if (!empty($name)) {
            $name = str_replace('/', DS, trim(trim($name), '/'));
            self::$TPL_NAME = $name;
            set_include_path(get_include_path() . PATH_SEPARATOR . VIEW . DS . $name . DS);
            return true;
        }
    }

    /**
     * @name setSuffix
     * @abstract 定义模板文件后缀名，默认值：html
     * @param string $suffix 后缀名
     * @return bool
     */
    public static function setSuffix($suffix)
    {
        if (!empty($suffix)) {
            $suffix = trim($suffix, "\0\t\n\x0B\r ."); //删除左右两边指定字符
            self::$TPL_SUFFIX = $suffix;
            return true;
        }
    }

    /**
     * @name assign
     * @abstract 分配模板变量。模板变量格式要求与PHP变量相同，系统会自动检测分配的变量是否符合要求。 成功分配返回真，失败返回假。
     * @param string $name 变量名
     * @param mixed  $value 变量值
     * @return bool
     */
    protected function assign($name, $value = '')
    {
        $name = trim($name);
        if (empty($name) or preg_match('/[0-9]/Uis', substr($name, 0, 1))) {
            //判断变量名是否为空或首字符是否是数字，条件成立则返回假
            return false;
        }
        if ($this->TPL_VARIABLE[$name] = $value) {
            //2014/7/19:原（return true;）现（return $this）[方便执行连贯操作]
            return $this;
        } else {
            return false;
        }
    }

    /**
     * @name display
     * @abstract 用于输出最终前端展示界面
     * @param string $tpl_name 模板页面名
     */
    protected function display($tpl_name = '')
    {
        if (empty($tpl_name)) {
            $tpl_name = $_GET['c'] . '/' . $_GET['a'];
        }
        $tpl_name = trim(str_replace('/', DS, $tpl_name)) . '.' . self::$TPL_SUFFIX;
        if (!is_file($tpl_path = VIEW . DS . self::$TPL_NAME . DS . $tpl_name)) {
            die('模板文件[' . $tpl_name . ']不存在');
        }
        extract($this->TPL_VARIABLE);
        include ($tpl_path);
    }

}

?>