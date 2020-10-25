<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-12
 * Time: AM10:08
 */

namespace app\admin\builder;
use app\admin\controller\Admin;

/**
 * AdminBuilder：快速建立管理页面。
 *
 * 为什么要继承AdminController？
 * 因为AdminController的初始化函数中读取了顶部导航栏和左侧的菜单，
 * 如果不继承的话，只能复制AdminController中的代码来读取导航栏和左侧的菜单。
 * 这样做会导致一个问题就是当AdminController被官方修改后AdminBuilder不会同步更新，从而导致错误。
 * 所以综合考虑还是继承比较好。
 *
 * Class AdminBuilder
 * @package Admin\Builder
 */
abstract class AdminBuilder extends Admin {

    /**
     * @author:zzl(郑钟良) zzl@ourstu.com
     */
    public function _initialize()
    {
        //  重写AdminController中得_initialize(),防止重复执行AdminController中的_initialize()
    }

    /**
     * 加载模板输出
     * @access protected
     * @param string $template 模板文件名
     * @param array  $vars     模板输出变量
     * @param array  $replace  模板替换
     * @param array  $config   模板参数
     * @return mixed
     * @author Patrick <contact@uctoo.com>
     */
    protected function fetch($templateFile = '', $vars = [], $replace = [], $config = []) {
        //获取模版的名称
        $template = dirname(__FILE__) . '/../view/Builder/' . $templateFile . '.html';

        //显示页面
        return parent::fetch($template,$vars,$replace,$config);
    }

    protected function compileHtmlAttr($attr) {
        $result = array();
        foreach($attr as $key=>$value) {
            $value = htmlspecialchars($value);
            $result[] = "$key=\"$value\"";
        }
        $result = implode(' ', $result);
        return $result;
    }
}

