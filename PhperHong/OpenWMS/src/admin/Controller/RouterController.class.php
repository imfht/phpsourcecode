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
class RouterController extends BaseController {
    public function index(){
        try {
            $router         = D('Router');
            $router_info = $router->get_router_info_for_admin();

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-路由管理',
                'router_info'          => $router_info,
                'breadcrumb'        => '&gt;路由管理'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 升级路由
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function upgrade(){
        $id = I('post.id');
        $return_data    = array();
        try {
            $router         = D('Router');
            $msg = $router->upgrade();
            $return_data = array(
                'ret'           => 1,
                'msg'           => $msg,
            );
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 路由重启
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function reboot(){
        $id = I('post.id');
        $return_data    = array();
        try {
            $router         = D('Router');
            $msg = $router->reboot();
            $return_data = array(
                'ret'           => 1,
                'msg'           => $msg,
            );
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
    
    /**
	 +----------------------------------------------------------
	 * 编辑路由
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	*/
    public function save_router_config(){
    	$router_mac 	= strtolower(I('post.router_mac'));
       
        $return_data    = array();
        try {
            $router         = D('Router');
            $router->edit_router($router_mac);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '编辑路由成功',
            );
            $this->success('编辑路由成功', U('Router/index'));
        } catch (Exception $e) {
           
            $this->error($e->getMessage(), U('Router/index'));
        }
      
    }
    
    /**
     +----------------------------------------------------------
     * 获取路由wifidog配置信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function router_wifi_config(){
        try {
            $router_wifi_config         = D('RouterWifiConfig');
            $info = $router_wifi_config->get_wifi_config_info();

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'         => $cop['pname_cn'].$cop['version_major'].'-路由设置',
                'info'          => $info,
                'id'            => $id,
                'breadcrumb'        => '&gt;<a href="'.U('Router/index').'">路由管理</a>&gt;路由设置'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Router/index'));
        }
        
    }
    /**
     +----------------------------------------------------------
     * wifidog认证开关配置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function wifidog_lock(){
        $enable = I('post.enable');
        $return_data    = array();
        try {
            $router_wifi_config         = D('RouterWifiConfig');
            $info = $router_wifi_config->wifidog_lock($enable);
            $return_data = array(
                'ret'           => 1,
                'msg'           => $info,
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
     * 保存wifidog配置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function save_config(){
        $param = array(
            'ssid'      => I('post.ssid'),
            'checktime' => I('post.checktime'),
            'timeout'   => I('post.timeout'),
            'apple'     => I('post.apple'),
            'nopop'     => I('post.nopop'),
            'whiteurl'	=> I('post.whiteurl'),
            'whitemac'	=> I('post.whitemac'),
        );

        try {
            $router_wifi_config         = D('RouterWifiConfig');
            $info = $router_wifi_config->save_config($param);
            $this->success($info, U('Router/router_wifi_config'));
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
            $this->error($e->getMessage(), U('Router/router_wifi_config'));
        }
    }
   
    /**
     +----------------------------------------------------------
     * 获取路由任务日志
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function router_task_list(){
        try {
            $router_task         = D('RouterTask');
            $list = $router_task->get_router_task_list();

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'         => $cop['pname_cn'].$cop['version_major'].'-任务日志列表',
                'list'          => $list,
                'id'            => $id,
                'breadcrumb'        => '&gt;<a href="'.U('Router/index').'">路由管理</a>&gt;任务日志列表'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Router/index'));
        }
    }
    
}