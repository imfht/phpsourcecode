<?php
namespace app\common\widget;

use think\Controller;
use think\Log;

class WidgetBase extends Controller {
    //public $logic;
    public $priv_obj;  // 功能对应的权限认证
    public $user_type_view = false;  // 是否有不同用户种类风格视图 true || false

    /**
     * 扩展支持模板风格
     * @param string $template
     * @param array  $vars
     * @param array  $replace
     * @param array  $config
     * @return string
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = []) {
        // pc 或 mobile
        if (is_mobile()) {
            $view_type = 'mobile';
        } else {
            $view_type = 'pc';
        }
        // 有些功能只有用户有必要操作
        // 有些功能各种不同角色都有可能需要操作
        if($this->user_type_view){
            // user 用户, emp 员工, eba 客户, sup 供应商
            $user_type = session('user_type');
            $this->view->config('view_path', APP_PATH . request()->module() . '/view/' . $view_type . '/' . $user_type . '/widget/');
        } else {
            $this->view->config('view_path', APP_PATH . request()->module() . '/view/' . $view_type . '/widget/');
        }

        return $this->view->fetch($template, $vars, $replace, $config);
    }

}
