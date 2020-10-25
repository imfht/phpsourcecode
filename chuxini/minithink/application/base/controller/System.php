<?php
namespace app\base\controller;
/*
* 后台公共控制器
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/3/24
*/
use app\system\model\Menu;
use think\Cache;
use think\Config;
use think\Controller;
use think\Db;
use think\Session;

class System extends Controller
{
    /**
     * 初始化
     */
    protected function _initialize() {

        //动态设置配置
        $config = Cache::get('cache_config');
        if(!$config){
            $config = config_lists();
            Cache::set('cache_config',$config);
        }
        Config::set($config);

        //权限验证
        if('base/system/menu' != $path = $this->request->path()){
            if(true !== $result = $this->auths($path)){
                $this->error($result);
            }
        }

    }

    /**
     * 权限验证 TODO:时间原因未完善,没有做对操作的权限验证，如新增删除等。 2017/5/4
     */
    public function auths($path = '') {
        $system_user = Session::get('system_user');
        $is_super = $system_user['id'] == config('super_uid');
        if($is_super){return true;}
        $role_info = Db::table('Auth')->where(['id'=>$system_user->getData('role')])->field('name,node,state')->find();
        if(!$role_info['state']){
            return $role_info['name']."已被禁用，请联系管理员启用!";
        }
        $role_node = explode(',',$role_info['node']);
        $menu = Menu::all();

        foreach ($menu as $item){
            if($item->link == $path){
                if(in_array($item->id, $role_node)){
                    return true;
                }else{
                    return '无法访问，权限不足，请联系管理员提升权限';
                }
            }
        }

        return true;
    }
    /**
     * 获取菜单
     * @return \think\response\Json
     */
    public function menu() {
        $msg['data']['list'] = $this->getMenu();
        $msg['status']=200;
        return $msg;
    }

    public function getMenu($pid = 0) {
        $where['pid'] = $pid;
        $where['state'] = 1;
        if(!IS_DEV){$where['is_dev'] = false;}
        $result = Db::table('menu')->where($where)->order('orders ASC')->select();
        foreach ($result as $key=>$value){
            $result[$key]['sub'] = $this->getMenu($value['id']);
        }
        return $result;
    }

    /**
     * 修改状态
     * @param $model
     * @param $id
     * @param $state
     * @return array|string
     */
    public function changeState($model, $id, $state) {

        $state = Db::table(strtolower($model))->where(['id'=>$id])->update(['state'=>$state == 'on']);
        if(false == $state){
            return getMsg("修改失败");
        }
        return getMsg("修改成功");
    }

    /**
     * 修改排序
     * @param $model
     * @param $id
     * @param $orders
     * @return array|string
     */
    public function changeOrder($model, $id, $orders) {
        $state = Db::table(strtolower($model))->where(['id'=>$id])->update(['orders'=>$orders]);
        if(false == $state){
            return getMsg("修改排序失败");
        }
        return getMsg("修改排序成功");
    }

    /**
     * 强制删除操作，如需进行检测等操作请在控制器里重写forceDelete方法
     * @param $model
     * @param $id
     * @return array|string
     */
    public function forceDelete($model, $id) {
        if(empty($id)){return getMsg("删除失败");}
        $state = Db::table(strtolower($model))->where(['id'=>$id])->delete();
        if(false == $state){
            return getMsg("删除失败");
        }
        return getMsg("删除成功","reload");

    }

    /**
     * 批量删除操作，如需进行检测等操作请在控制器里重写allDelete方法
     * @param $model
     * @param $checkbox
     * @return array|string
     */
    public function allDelete($model, $checkbox) {
        $ids = explode(',',$checkbox);
        if (empty($ids)){ return getMsg("删除失败");}
        for ($i = 0; $i< count($ids); $i++){
            $state = Db::table($model)->where(['id'=>$ids[$i]])->delete();
        }
        if(false == $state){
            return getMsg("删除失败");
        }
        return getMsg("删除成功","reload");
    }



}
