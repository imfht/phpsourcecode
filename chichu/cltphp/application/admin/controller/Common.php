<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
class Common extends Controller
{
    protected $mod,$role,$system,$nav,$menudata,$cache_model,$categorys,$module,$moduleid,$adminRules,$HrefId;
    public function initialize()
    {
        //判断管理员是否登录
        if (!session('aid')) {
            $this->redirect('admin/login/index');
        }
        define('MODULE_NAME',strtolower(request()->controller()));
        define('ACTION_NAME',strtolower(request()->action()));
        //权限管理
        //当前操作权限ID
        if(session('aid')!=1){
            $this->HrefId = db('auth_rule')->where('href',MODULE_NAME.'/'.ACTION_NAME)->value('id');
            //当前管理员权限
            $map['a.admin_id'] = session('aid');
            $rules=Db::table(config('database.prefix').'admin')->alias('a')
                ->join(config('database.prefix').'auth_group ag','a.group_id = ag.group_id','left')
                ->where($map)
                ->value('ag.rules');
            $this->adminRules = explode(',',$rules);
            if($this->HrefId){
                if(!in_array($this->HrefId,$this->adminRules)){
                    $this->error('您无此操作权限');
                }
            }
        }
        $this->cache_model=array('Module','AuthRule','Category','Posid','Field','System','cm');
        foreach($this->cache_model as $r){
            if(!cache($r)){
                savecache($r);
            }
        }
        $this->system = cache('System');
        $this->categorys = cache('Category');
        $this->module = cache('Module');
        $this->mod = cache('Mod');
        $this->rule = cache('AuthRule');
        $this->cm = cache('cm');
    }
    //空操作
    public function _empty(){
        return $this->error('空操作，返回上次访问页面中...');
    }
}
