<?php
namespace Common\Controller;
use Think\Controller;

class AdminBaseController extends Controller {
    /**
     * [_initialize 判断登录 加载动态配置 检测权限 设置/获取权限菜单]
     * session [admin_menu] 
     * F() [AdminConfig] [AdminRule]
     */
    public function _initialize() {

        //检测登录
        if(defined('UID')) return;

        //Widget flash插件session问题
        if(IS_POST && ACTION_NAME=='widget') $this->widgetSession();

        //判断是否登录
        $uid=is_login();
        if(!$uid) $this->redirect('Admin/Public/login');
        define('UID',$uid);

        //判断超级管理员
        $admin_super = (UID == C('ADMIN_SUPER')) ? true : false ;
        define('SUPER',$admin_super);

        //加载动态配置
        $this->initConfig();

        //检测节点控制权限
        if(!SUPER){
            //检测动态配置中的规则
            $config_rule = $this->checkConfigRule();
            if($config_rule === false ){
                $this->error('403:禁止访问');
            }elseif($config_rule === null){
                $rule = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
                if( !check_auth($rule) ){
                    $this->error('未授权访问!');
                }
            }
        }

    }
    private function widgetSession(){
        $session_id=I('session_id','');
        if(!empty($session_id)){
            session_write_close();
            session_id($session_id);
            session_start();
        }
    }

    //加载动态配置
    private function initConfig(){
        $admin_config = F('AdminConfig');
        if(!$admin_config){
            $admin_config=D('Config')->getConfig('Admin');
            F('AdminConfig',$admin_config);
        }
        C($admin_config);
    }

    //检测动态配置中是否为允许/禁止访问的规则
    private function checkConfigRule($rule){
        if(!isset($rule)) $rule=CONTROLLER_NAME;

        $deny  = C('DENY_VISIT');
        $allow = C('ALLOW_VISIT');
        
        if ( !empty($deny)  && in_array($rule,$deny) ) {
            return false;
        }
        if ( !empty($allow) && in_array($rule,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }

    //公共分页方法
    final protected function _page($model,$where=array(),$num=15){
        $count=M($model)->where($where)->count();
        if(!$count) return false;
        if(($count/$num) <= 1) return "0,$num";

        $Page = new \Think\Page($count,$num);
        $_page=$Page->show();
        $this->assign('_page',$_page);
        return $Page->firstRow.','.$Page->listRows;
    }

    //公共搜索方法
    final protected function _search($_search=array()){
        $this->assign('_search_block',1);
        $get=I('get.','');
        //去除混淆条件
        if(!IS_GET || (isset($get['form']) && $get['form'] != 'search')) return array();
        else unset($get['form']);

        $where=array();
        foreach ($get as $k => $v) {
            if($v == '' || (is_array($v) && current($v) == '')) continue;

            if(is_array($v)){
                if(count($v) > 1){
                    //多个数组
                    foreach ($v as $_k => $_v) {
                        if($_k=='like') $_v="%{$_v}%";
                        $where[$k][] = array($_k,$_v);
                    }   
                }else{
                    //单个数组
                    $_k=key($v);
                    $_v=current($v);
                    if($_k=='like') $_v="%{$_v}%";
                    $where[$k] = array($_k,$_v);
                }
            }else{
                $where[$k] = $v;
            }
        }
        return $where;
    }
    
    final protected function _batch($model){
        if(IS_GET){ $this->assign('_batch_block',1); }
        if(!IS_POST && !IS_AJAX) return false;
        $post=I('post.','');
        $sort=$post['sort'];
        $pk=$post['pk'];
        $return=true;

        switch ($post['batch']) {
            case 'sort':
                foreach ($sort as $k => $v) {
                    M($model)->where("id={$k}")->setField('sort',$v);
                }
                break;
            case 'disable':
            case 'enable':
                $status=0;
                if($post['batch'] == 'enable') $status=1;

                if(count($pk) > 1){
                    $return=M($model)->where(array('id'=>array('IN',$pk)))->setField('status',$status);
                }elseif(count($pk) == 1){
                    $pk=current($pk);
                    $return=M($model)->where("id={$pk}")->setField('status',$status);
                }
                break;
            case 'del':
                $map['id']=array('IN',$pk);
                $return = D($model)->where($map)->delete();
                break;
            default:
                $return=call_user_func(array($this,'batchCall'),$post['batch'],$pk);
                break;
        }

        if($return) $this->success('批量执行成功');
        else $this->error('批量执行失败');

    }


    final protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix=''){
        if(empty($templateFile)) $templateFile=ACTION_NAME;
        if(IS_AJAX){
            C('SHOW_PAGE_TRACE',false);
            //获取指定继承块模板
            $block=I('block','');
            if(!empty($block)){
                $tpl_file=$this->view->parseTemplate($templateFile);
                $tpl_content=file_get_contents($tpl_file);
                $tpl_find=preg_match('/<block\sname=[\'"]'.$block.'[\'"]\s*?>(.*?)<\/block>/is',$tpl_content,$tpl_block);
                if($tpl_find){
                    $tpl_html=$this->view->fetch($templateFile,$tpl_block[1]);
                    $this->ajaxReturn($tpl_html);
                }
            }
            $content = $this->view->fetch($templateFile);
            $this->ajaxReturn($content);
        }else{
            $this->view->display($templateFile,$charset,$contentType,$content,$prefix);
        }
    }

}