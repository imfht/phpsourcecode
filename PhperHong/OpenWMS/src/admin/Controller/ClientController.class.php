<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Controller;
use Think\Controller;
use Think\Exception;
class ClientController extends BaseController {
    /**
     +----------------------------------------------------------
     * 获取指定路由的在线用户
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function onlineuser(){
    	$pagenum = I('get.p');
        $pagelen = I('get.pagelen');
       
        $return_data    = array();
        try {
        	$where = array();
        	$where['pagelen'] = $pagelen;
     
            $client         = D('Client');
            $list = $client->get_online_user_all($pagenum, $pagelen);

            
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-在线用户列表',
                'rows'          => $list['list'],
                'param'             => $where,
                'count'             => $list['count'],
                'pagelen'           => $pagelen,
                'breadcrumb'        => '&gt;在线用户列表'
            ));



            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
       
    }
    /**
     +----------------------------------------------------------
     * 获取在线用户详细信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function online_user_info(){
        $return_data    = array();
        try {
            $client         = D('Client');
            $info = $client->get_online_user_info(I('get.mac'));
            $this->assign(array(
                
                'info'          => $info,
            ));
           
        } catch (Exception $e) {
            $this->assign(array(
                'msg'          => $e->getMessage(),
            ));
            
        }
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 获取历史用户详细信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function user_info(){
        $return_data    = array();
        try {
            $client         = D('Client');
            $info = $client->get_ls_user_info(I('get.id'));
            $this->assign(array(
                
                'info'          => $info,

            ));
           
        } catch (Exception $e) {
            $this->assign(array(
                'msg'          => $e->getMessage(),
            ));
            
        }
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 将用户踢下线
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function tick(){
        $mac = I('post.mac');
        $return_data    = array();
        try {
            $router         = D('Router');
            $list = $router->tick($mac);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '操作成功，用户已下线',
            );
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
                'info'  => false,
            );
        }
        exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 获取指定路由的历史用户
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function userlist(){
        $pagenum = I('get.p');
        $pagelen = I('get.pagelen');
        $sortkey = I('get.sortkey');
        $reverse = I('get.reverse');

        
        $where = array(
            'auth_type'     => I('get.auth_type'),
            'device_type'   => I('get.device_type'),
            'date_type'     => I('get.date_type'),
            'time_start'    => I('get.time_start'),
            'time_end'      => I('get.time_end'),
            'router_mac'    => I('get.router_mac'),
        );
        $return_data    = array();
        try {
            $client         = D('Client');
            $list = $client->get_user_list($pagenum, $pagelen, $sortkey, $reverse, $where);
            $cop = C('COPYRIGHT');
            $where['pagelen'] = $pagelen;
            $where['sortkey'] = $sortkey;
            $where['reverse'] = $reverse;
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-历史用户列表',
                'rows'          => $list['list'],
                'router_list'       => $list['router_list'],
                'count'             => $list['count'],
                'pagelen'           => $pagelen,
                'param'             => $where,
                'breadcrumb'        => '&gt;历史用户列表'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
        
    }
    /**
     +----------------------------------------------------------
     * 获取商家下面的用户认证日志
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function signinlog(){
    	$pagenum = I('get.p');
        $pagelen = I('get.pagelen');
        $sortkey = I('get.sortkey');
        $reverse = I('get.reverse');

       
        $where = array(
            'auth_type'     => I('get.auth_type'),
            'time_start'    => I('get.time_start'),
            'time_end'      => I('get.time_end'),
            'router_mac'    => I('get.router_mac'),
        );
        $return_data    = array();
        try {
            $fullsigninlog         = D('FullSigninLog');

            $list = $fullsigninlog->get_signin_log_list_by_mid($pagenum, $pagelen, $sortkey, $reverse, $where);
            $cop = C('COPYRIGHT');
            $where['pagelen'] = $pagelen;
            $where['sortkey'] = $sortkey;
            $where['reverse'] = $reverse;
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-历史认证日志列表',
                'rows'          	=> $list['list'],
                'router_list'       => $list['router_list'],
                'count'             => $list['count'],
                'pagelen'           => $pagelen,
                'param'             => $where,
                'breadcrumb'        => '&gt;历史认证日志列表'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 导出execl认证记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function download_execl(){

       
        $where = array(
            'auth_type'     => I('get.auth_type'),
            'time_start'    => I('get.time_start'),
            'time_end'      => I('get.time_end'),
            'router_mac'    => I('get.router_mac'),
        );
        $return_data    = array();
        try {
            $fullsigninlog         = D('FullSigninLog');
            $fullsigninlog->down_load_execl_for_signinlog($where);
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 导出execl用户列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function download_execl_for_userlist(){
    	$where = array(
            'auth_type'     => I('get.auth_type'),
            'time_start'    => I('get.time_start'),
            'time_end'      => I('get.time_end'),
            'router_mac'    => I('get.router_mac'),
        );
        $return_data    = array();
        try {

            $client         = D('Client');
            //$list = $client->get_user_list(session('adminid'), $pagenum, $pagelen, $sortkey, $reverse, $where);
            $client->down_load_execl_for_userlist($where);
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    public function _empty(){
           $this->display('Empty:index');
           //xacs pr069
    }
}