<?php 
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\behavior;
use \think\Controller;
use \think\Request;
use \think\Session;
use \think\Cookie;
use \think\Hook;
use \think\Lang;
// echo "当前模块名称是" . $request->module();
// echo "当前控制器名称是" . $request->controller();
// echo "当前操作名称是" . $request->action();
class Initlang extends Controller
{
    protected   $request;
    public function run(&$params)
    {
        if($this->request->module()=="install") return ;
        $this->loadlang();
        $this->request = Request::instance();
        

    }
    private function loadlang(){
       //公共语言库
        Lang::load(APP_PATH . 'common/lang/zh-cn.php');
        //插件语言库
        $plus = finddirfromdir(APP_PATH."../plus"); 
        if(is_array($plus)){
           foreach ($plus as $k => $v) {
                if(file_exists(PLUS_PATH.$v.'/lang/zh-cn.php')){
                    Lang::load(PLUS_PATH.$v.'/lang/zh-cn.php');
                }
           }
        }
    //模型语言包
    $moduls = finddirfromdir(APP_PATH."../application");
    // show($moduls);
    if(is_array($moduls)){
           foreach ($moduls as $k => $v) {
                if(file_exists(APP_PATH.$v.'/lang/zh-cn.php')){
                    Lang::load(APP_PATH.$v.'/lang/zh-cn.php');
                }
           }
        }

    }


}