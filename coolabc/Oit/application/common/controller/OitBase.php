<?php
namespace app\common\controller;

use app\common\api\Para;
use app\common\api\RBAC;
use think\Controller;
use think\Db;
use think\Log;

/**
 * Class OitBase
 * @package app\common\controller
 */
class OitBase extends Controller {
    //public $logic;
    public $priv_obj;  // 功能对应的权限认证
    public $user_type_view = false;  // 是否有不同用户种类风格视图 true || false

    /**
     * 控制器初始化
     */
    public function _initialize() {
        // todo::将缓存使用redis,客户端每次访问时,都重置失效时间
        Para::system_const_variable();
        if (ACTION_NAME == 'api') {
            // api验证
            // api后续作用应该很多
            return;
        }

        if(RBAC::access_pass($this->priv_obj) !== true){
            // 没有登陆
            if(!session('user_id')){
                redirect("entroller/Manager/index");
            }
            $this->error(lang('没有权限'));
        }
    }

    /**
     * 功能界面
     * @return string
     */
    public function index(){
        // 表格标题列
        // 检索条件 -- 日期、字典、字符
        // 参数

        echo $this->fetch();
    }

    /**
     * 记录界面
     * @return string
     */
    public function record(){

        echo $this->fetch();
    }

    /**
     * 保存数据 - 使用先删除，再增加的方式
     * @return \think\response\Json
     */
    public function save(){

        return json();
    }


    /**
     * 删除数据
     * @return \think\response\Json
     */
    public function remove(){

        return json();
    }

    /**
     * 废弃, 前台刷新,不与后台交互数据
     * 刷新数据
     * 一般能查询就能刷新
     */
    //public function refresh(){
    //    // 树结构  -- 逻辑
    //    // 表格数据 -- 逻辑
    //
    //    return json();
    //}

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
            $this->view->config('view_path', APP_PATH . request()->module() . '/view/' . $view_type . '/' . $user_type . '/');
        } else {
            $this->view->config('view_path', APP_PATH . request()->module() . '/view/' . $view_type . '/');
        }

        return $this->view->fetch($template, $vars, $replace, $config);
    }

    /**
     * 直接调用obj中某个方法
     * 一般公共方法
     * 只有登陆用户才能调用，注意权限认证
     */
    public static function obj_func(){

    }


}
