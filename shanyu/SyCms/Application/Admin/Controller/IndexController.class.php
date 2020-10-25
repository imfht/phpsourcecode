<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class IndexController extends AdminBaseController {

    public function index(){
        //快捷菜单NAV
        $admin_menu=D('AdminMenu')->getNav();
        $this->assign('admin_menu',$admin_menu);

        //获取后台菜单
        $this->initMenu();

        //动态追踪上级
        // $_nav_line=$this->getNavLine();
        // $this->assign('_nav_line',$_nav_line);

        $this->display();
    }

    //获取拥有权限的菜单
    private function initMenu(){
        if(!session('?admin_menu')){
            $menu=$this->getAuthRule();
            if(!SUPER){
                foreach ($menu as $k =>$v) {
                    if( !check_auth($v['name'],array('in','2,3')) ){
                        unset($menu[$k]);
                    }
                }
                unset($_SESSION['_AUTH_LIST_'.UID.'2,3']);
            }

            $admin_menu=self::getTree($menu,0);
            session('admin_menu',$admin_menu);
        }
    }

    //缓存后台菜单
    private function getAuthRule(){
        $auth_rule=F('AdminAuth');
        if(!$auth_rule){
            $auth_rule=M('AuthRule')->field('id,pid,name,title,icon')->where('type IN (2,3) AND status = 1')->order('sort ASC,id ASC')->select();
            F('AdminAuth',$auth_rule);
        }
        return $auth_rule;
    }

    //递归生成无限级后台菜单
    private static function getTree($arr,$pid){
        $result=array();
        foreach ($arr as $v) {
            if($v['pid'] == $pid){
                $r['url']=$v['name'];
                $r['name']=$v['title'];
                if(!empty($v['icon'])) $r['icon']=$v['icon'];

                $r['item']=self::getTree($arr,$v['id']);
                if(empty($r['item'])) unset($r['item']);

                $result[]=$r;
            }
        }
        return $result;
    }

    //追踪当前节点的所有上级
    private function getNavLine(){
        $auth_rule=$this->getAuthRule();

        //Admin/Article/add
        //Admin/Article/edit/id/25
        foreach ($auth_rule as $v) {
            if($v['pid'] == 0) continue;
            if( MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME == $v['name'] ){
                $now_id_1=$v['id'];
            }
            elseif ( MODULE_NAME.'/'.CONTROLLER_NAME.'/index' == $v['name']) {
                $now_id_2=$v['id'];
            }
        }
        $now_id= isset($now_id_1) ? $now_id_1 : $now_id_2;

        if(!isset($now_id)) return false;
        return self::getParent($auth_rule,$now_id);
    }

    //递归查找所有上级
    private static function getParent($arr,$id){
        $result=array();
        foreach ($arr as $v) {
            if($id == $v['id']){
                $result[] = array(
                    'url'=>$v['name'],
                    'name'=>$v['title'],
                );
                $result=array_merge(self::getParent($arr,$v['pid']),$result);
            }
        }

        return $result;
    }
    //后台菜单搜索
    public function search($keywords=''){
        //数据
        $map['type']=2;
        $map['status']=1;
        $map['title']=array('like','%'.$keywords.'%');
        $list=D('AuthRule')->where($map)->field('id,title,name')->select();
        $this->assign('list',$list);

        $this->display();
    }

	//后台主页-数据统计
    public function system(){

        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd = $gd ['GD Version'];
        } else {
            $gd = "不支持";
        }
        $able = get_loaded_extensions();
        $extensions_list = "";
        foreach ($able as $key => $value) {
            if ($key != 0 && $key % 20 == 0) {
                $extensions_list = $extensions_list . '<br />';
            }
            $extensions_list = $extensions_list . "{$value}&nbsp;&nbsp;";
        }
        $server_info = array(
            '操作系统' => PHP_OS,
            '主机名IP端口' => $_SERVER ['SERVER_NAME'] . ' (' . $_SERVER ['SERVER_ADDR'] . ':' . $_SERVER ['SERVER_PORT'] . ')',
            '运行环境' => $_SERVER ["SERVER_SOFTWARE"],
            '服务器语言' => getenv("HTTP_ACCEPT_LANGUAGE"),
            'PHP运行方式' => php_sapi_name(),
            '管理员邮箱' => $_SERVER['SERVER_ADMIN'],
            '程序目录' => WEB_PATH,
            'MYSQL版本' => function_exists("mysql_close") ? mysql_get_client_info() : '不支持',
            'GD库版本' => $gd,
            '上传附件限制' => ini_get('upload_max_filesize'),
            'POST方法提交限制' => ini_get('post_max_size'),
            '脚本占用最大内存' => ini_get('memory_limit'),
            '执行时间限制' => ini_get('max_execution_time') . "秒",
            '浮点型数据显示的有效位数' => ini_get('precision'),
            '内存使用状况' => round((@disk_free_space(".") / (1024 * 1024)), 5) . 'M/',
            '已用/总磁盘' => round((@disk_free_space(".") / (1024 * 1024 * 1024)), 3) . 'G/' . round(@disk_total_space(".") / (1024 * 1024 * 1024), 3) . 'G',
            '服务器时间' => date("Y年n月j日 H:i:s 秒"),
            '北京时间' => gmdate("Y年n月j日 H:i:s 秒", time() + 8 * 3600),
            '显示错误信息' => ini_get("display_errors") == "1" ? '√' : '×',
            'register_globals' => get_cfg_var("register_globals") == "1" ? '√' : '×',
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? '√' : '×',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? '√' : '×',
            'phpinfo' => '<a href="'.U('Admin/Index/phpInfo').'">PHP详细信息</a>',
        );
        $this->assign('server_info', $server_info);
        $this->assign('extensions_list', $extensions_list);

    	$this->display();
    }
    public function phpInfo(){
        echo phpinfo();
    }
	//清理缓存
	public function delCache(){
        if(APP_MODE == 'sae'){
            $Kvdb = A('Admin/Kvdb');
            $Kvdb->del();
        }else{
            $status=del_dir(RUNTIME_PATH);
            if($status) $this->success('清理缓存完成');
            else $this->error('清理缓存失败');
        }
	}

    //公用插件
    public function widget(){
        $name=I('get.name','');
        if(empty($name)) return false;
        C('SHOW_PAGE_TRACE',false);
        $widget=ucwords($name);
        R('Admin/'.$widget.'/server',array(),'Widget');
        exit();
    }


	// //输出并缓存头部模板
	// public function header($style=''){
	// 	if(!empty($style)) $this->display('Index:header-'.$style);
	// 	else $this->display('Index:header');
	// }
	// //输出并缓存脚部模板
	// public function footer($style=''){
	// 	if(!empty($style)) $this->display('Index:footer-'.$style);
	// 	else $this->display('Index:footer');
	// }

}