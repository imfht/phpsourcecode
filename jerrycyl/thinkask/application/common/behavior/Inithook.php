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
use app\common\controller\Base;
use \think\Request;
use \think\Session;
use \think\Cookie;
use \think\Hook;
use \think\Lang;
// echo "当前模块名称是" . $request->module();
// echo "当前控制器名称是" . $request->controller();
// echo "当前操作名称是" . $request->action();
class Inithook
{
    public function run(&$params)
    {
      $request = Request::instance();
      if($request->module()=="install") return ;
      //未安装时不执行
    if (substr(request()->pathinfo(), 0, 7) != 'install' && is_file(APP_PATH . 'database.php')) {
      //初始化某些配置信息
      //扩展插件
      \think\Loader::addNamespace('addons', ROOT_PATH . '/addons/');
      $this->setHook();

    }

       
    }
    /**
     * [setHook hook名不能用下划线]
     * @Author   Jerry
     * @DateTime 2017-05-02T09:44:52+0800
     * @Example  eg:
     */
 protected function setHook() {
    $data = cache('hooks');
    if (!$data) {
      $hooks = db('Hooks')->column('name,addons');
      foreach ($hooks as $key => $value) {
        if ($value) {
          $map['status'] = 1;
          $names         = explode(',', $value);
          $map['name']   = array('IN', $names);
          $data          = db('Addons')->where($map)->column('id,name');
          if ($data) {
            $addons = array_intersect($names, $data);
            \think\Hook::add($key, array_map('get_addon_class', $addons));
          }
        }
      }
      cache('hooks', \think\Hook::get());
    } else {
      \think\Hook::import($data, false);
    }
  }
}