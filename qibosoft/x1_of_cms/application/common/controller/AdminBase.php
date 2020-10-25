<?php

namespace app\common\controller;

use plugins\log\model\Action;
use think\Cache;

//定义是后台
define('IN_ADMIN', true);

/**
 * 后台总控制器
 */
class AdminBase extends Base
{
    /**
     * 后台初始化
     */
    protected function _initialize()
    {
        parent::_initialize();
        if(SUPER_ADMIN!==true){
			//钩子扩展
			method_exists($this,'get_hook') && $this->get_hook('admin_begin',$data=[],$this->user);
            hook_listen('admin_begin',$array=['user'=>$this->user]);			

            if(empty($this->user)){
                if( ($this->route[0]=='admin' && $this->route[1]=='index' && $this->route[2]=='login') ){
                }elseif( ($this->route[0]=='admin' && $this->route[1]=='mysql' && $this->route[2]=='into') ){
                    list(,$time) = explode("\t",mymd5(get_cookie('mysql_into'),'DE'));
                    if($time-time()<0){
                        $this->error('后台权限验证失败');
                    }
                }else{
                    if($this->route[0]=='admin' && $this->route[1]=='index' && $this->route[2]==''){
                        header('location:'.url('admin/index/login'));exit;
                    }
                    $this->success('请先登录',url('admin/index/login'),'',0);
                }                
            }else{
                $admin_uid = $time = $ip = NULL;
                list($admin_uid,$time,$ip) = explode("\t",mymd5(get_cookie('admin_login'),'DE'));
                if($this->check_power()!==true){
                    if ($admin_uid) {
                        if (time()-$time>3600*12) {
                            set_cookie('admin_login',null);
                            $this->error('没权限操作,可能已经超时了，请重新登录试试！');
                        }elseif($ip!=get_ip()){
                            set_cookie('admin_login',null);
                            $this->error('IP换了，请重新登录！');
                        }
                        $this->user = get_user($admin_uid);
                        if($this->check_power()!==true){
                            set_cookie('admin_login',null);
                            $this->error('权限检验失败，请重新登录！');
                        }
                    }else{
                        $this->error('你没有此后台权限!!');
                    }                    
                }elseif(empty($admin_uid)){
                    set_cookie('admin_login',mymd5($this->user['uid']."\t".time()."\t".get_ip()));
                }
            }
        }
        if($this->request->isPost() || $this->route[2]=='delete'){
            Action::write(); //操作日志
            //把缓存全清除
            if($this->user['groupid']==3 && in_array($this->route[1], ['setting','sort','module','field','plugin','module','hook_plugin','hook','timedtask','admin_menu','member_menu','webmenu','group','sort_field'])){
                \think\Hook::add('app_end', function(){
                    if (!defined('IN_PLUGIN')&&!defined('FORBID_CLEAR_CACHE')&&empty(cache('forbid_clear_cache'))) {
                        Cache::clear();
                    }                    
                });
            }
        }
        
        // 自动表单公共模板,比如选择分类会用到
        $this->assign('auto_tpl_base_layout', APP_PATH.'member/view/default/layout.htm');
		
		//后台布局模板
		$this->assign('admin_style_layout', config('admin_style_layout'));
		
		//分页样式
// 		config('paginate',[
// 		        'type'      => 'bootstrap',
// 		        'var_page'  => 'page',
// 		        'list_rows' => 15,
// 		]);
    }
    
    /**
     * 后台权限判断
     * @return boolean
     */
    protected function check_power(){
        $_power = (array)getGroupByid($this->user['groupid'],false)['admindb'];  //取得用户的菜单权限
        
        foreach($_power AS $key=>$value){
            list($top,$model,$link) = explode('-', $key);
            if($top=='base'||$top=='member'){   //后台核心功能
                $power['admin'][$link] = $value;
            }elseif($top=='plugin'){    //插件功能
                $power['plugin'][$model][$link] = $value;
            }else{  //模块功能
                $power[$model][$link] = $value;
            }
        }
        
        if($this->route[1]=='plugin' && $this->route[2]=='execute'){
            if($this->route[0]=='admin'&&input('plugin_action')=='quickedit'&&$power['plugin'][input('plugin_name')][input('plugin_controller').'/edit']){
                return true;
            }
            if($power['plugin'][input('plugin_name')][input('plugin_controller').'/'.input('plugin_action')]){
                return true;
            }
        }elseif($this->route[2]=='quickedit' && ($power[$this->route[0]][$this->route[1].'/edit'] || $power[$this->route[0]][$this->route[1].'/index_'.input('name')])){
            
            return true;
        }elseif($this->route[0]=='admin'){
            if($_power){
                if($this->route[1]=='index'&&in_array($this->route[2], ['','leftmenu','index','welcome','sysinfo','login','quit'])){
                    return true;
                }elseif($this->route[1]=='attachment'&&$this->route[2]=='upload'){
                    //这里的权限需要进一步完善!!!!!!!!
                    return true;
                }elseif($this->route[1]=='ajax'&&in_array($this->route[2],['check','getfilterlist'])){
                    //这里的权限需要进一步完善!!!!!!!!
                    return true;
                }
            }
            if($power['admin'][$this->route[1].'/'.$this->route[2]]){
                return true;
            }
        }else{
            if($power[$this->route[0]][$this->route[1].'/'.$this->route[2]]){
                return true;
            }
        }
    }
    
    /**
     * 快速编辑,修改字段
     */
    public function quickedit(){
        $data = input();
        if($data['name']&&$data['pk']){
            $m = $this->model->get($data['pk']);
            $field = $data['name'];
            if($data['type']=='switch'){
                $data['value'] = $data['value']=='false'?0:1;
            }
            $m->$field = $data['value'];
            if($m->save()){
                $this->success('设置成功');
            }else{
                $this->error('设置失败');
            }
        }
    }
    

}
