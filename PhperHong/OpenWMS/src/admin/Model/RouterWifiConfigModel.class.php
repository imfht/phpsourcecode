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
namespace admin\Model;
use Think\Model;
use Think\Exception;
use Think\Cache;
use Think\Log;

class RouterWifiConfigModel extends Model{
	protected $cache;
 	function __construct() {
 		$this->cache   = Cache::getInstance();
 	}
    /**
     +----------------------------------------------------------
     * 检测热点配置是否修改，如果修改则更新数据库
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function set_router_wifi_config($param){
    	$router_mac = C('router_mac');
        $param['gw_mac'] = strtolower($param['gw_mac']);
       	if ($router_mac != $param['gw_mac']){
       		return false;
       	}

        $wifi_config_key = 'wifi_config:'.$param['gw_mac'];
        
        $wifi_config_info = $this->get_wifidog_info($wifi_config_key);
        
        $router = DD('Router');
        $router_task = DD('RouterTask');
       
    
        //检测是否有wifidog任务正在进行
        if($router_task->check_type_task($param['gw_mac'], 'wifidog')){
            return false;
        }

        //检测是否有wifi任务正在进行
        if($router_task->check_type_task($param['gw_mac'], 'wifi')){
            return false;
        }

        $data = array(
            'ssid'      => $param['ssid'],
            'checktime' => $param['checktime'],
            'timeout'   => $param['timeout'],
            'enable'    => $param['enable'],
            'apple'     => $param['apple'],
            'nopop'     => $param['nopop'],
            'whiteurl'  => $param['whiteurl'],
            'whitemac'  => $param['whitemac'],
            'last_datetime' => date('Y-m-d H:i:s'),
        );
       
        $this->set_redis_for_router_wifidog($wifi_config_key, $data);
        
        //如果路由热点被关闭，则修改路由状态
        if ($param['enable'] == '0'){
            $router->update_router_info($param['gw_mac'], 'status', 3);
        }else{
            $router->update_router_info($param['gw_mac'], 'status', 1);
        }
        return true;
    }
    /**
     +----------------------------------------------------------
     * 获取redis中的数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_wifidog_info($key){
		
		if ($this->cache->exists($key) == 0){
	 		return false;
	 	}
		$field = array('ssid', 'checktime', 'timeout', 'enable', 'apple', 'nopop', 'whiteurl', 'whitemac', 'last_datetime');
		$wifidog_info = array();
		
	 	$data = $this->cache->hmget($key, $field);

 		foreach ($field as $k => $value) {
 			$wifidog_info[$value] = $data[$k];
 		}
	 	return $wifidog_info;
    }
    /**
     +----------------------------------------------------------
     * 获取单个字段数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_wifidog_onef($mac, $f){
    	$wifi_config_key = 'wifi_config:'.strtolower($mac);
    	return $this->cache->hget($wifi_config_key, $f);
    }
    /**
     +----------------------------------------------------------
     * 存储redis数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function set_redis_for_router_wifidog($key, $info){
		$this->cache->hmset($key, $info);
		return true;
    }

    /**
     +----------------------------------------------------------
     * 删除配置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function del_wifi_config($router_mac){
       
        //删除缓存
        $this->cache->rm('wifi_config:'.strtolower($router_mac));
        return true;
    }
    /**
     +----------------------------------------------------------
     * 获取配置信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_wifi_config_info(){
        //检测该路由是否属于该商家
        $router = DD('Router');
        $router_info = $router->get_router_info_for_admin();
      
        if ($router_info['status'] != '1' && $router_info['status'] != '3'){
            throw new Exception("该路由不是在线状态，无法进行配置", 1);
        }
        $wifi_config_key = 'wifi_config:'.strtolower($router_info['router_mac']);
       	$config_info = $this->get_wifidog_info($wifi_config_key);
       
        if (!$config_info){
            throw new Exception("路由器还未上报配置数据，请等待".$router_info['check_time'].'秒左右再试', 1);
            
        }
        return $config_info;
    }
    
    /**
     +----------------------------------------------------------
     * 下发热点账号
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function wifidog_ucode($router_mac, $mid){
        //创建任务
        $param = array(
            'router_mac'    => $router_mac,
            'mid'           => $mid,
            'param'         => array('ucode'=>$mid),
            'type'          => 'wifidog',
            'content'       => '修改热点账号',
        );
        
        //$router_task = DD('RouterTask');
        //$rs = $router_task->add_router_task($param);
        
    }
    /**
     +----------------------------------------------------------
     * wifidog认证开关配置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function wifidog_lock($enable){
      
        if ($enable != 0 && $enable != 1){
            throw new Exception("未知的命令，请重试", 1);
            
        }
        //检测该路由是否属于该商家
        $router = DD('Router');
        $router_info = $router->get_router_info_for_admin();
        
        if ($router_info['status'] != '1' && $router_info['status'] != '3'){
            throw new Exception("该路由不是在线状态，无法进行配置", 1);
        }
        //创建升级任务
        $msg = $enable == 1 ? '开启认证' : '关闭认证';
        $param = array(
            'router_mac'    => $router_info['router_mac'],
            'param'         => array('enable'=>$enable),
            'type'          => 'wifidog',
            'content'       => $msg,
        );
        
        $router_task = D('RouterTask');
        $rs = $router_task->add_router_task($param);
        if (!$rs){
            throw new Exception("创建任务失败，请重试", 1);
        }
       
       
        //更新缓存
        $wifi_config_key = 'wifi_config:'.strtolower($router_info['router_mac']);
        $u_info = $this->get_wifidog_info($wifi_config_key);
        if ($u_info){
            $u_info['enable'] = $enable;
            $this->set_redis_for_router_wifidog($wifi_config_key, $u_info);
        }
        if ($enable == 0){
            $router->update_router_info($router_info['router_mac'], $router_info['mid'], 'status', 3);
        }
        return '已下发'.$msg.'任务，路由将在'.$router_info['check_time'].'秒左右执行任务';
    }
    /**
     +----------------------------------------------------------
     * wifidog认证配置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function save_config($param){
        
       	
        //检测该路由是否属于该商家
        $router = DD('Router');
        $router_info = $router->get_router_info_for_admin();
      

        if ($router_info['status'] != '1' && $router_info['status'] != '3'){
            throw new Exception("该路由不是在线状态，无法进行配置", 1);
        }

        $wifi_config_key = 'wifi_config:'.strtolower($router_info['router_mac']);
       	$config_info = $this->get_wifidog_info($wifi_config_key);

        //检测是否有变动
        $b = false;
        if($config_info['ssid'] != $param['ssid']){
            //如果修改了SSID，则单独创建任务
            $param_task = array(
                'router_mac'    => $router_info['router_mac'],
                'param'         => 'ssid='.$param['ssid'].';',
                'type'          => 'wifi',
                'content'       => '修改热点名称（SSID）修改为：'.$param['ssid'],
            );
            
            $router_task = D('RouterTask');
            $rs = $router_task->add_router_task($param_task);
            if (!$rs){
                throw new Exception("创建任务失败，请重试", 1);
            }
            $b = true;
        }
        $param_task = array(); 
        if (!empty($param['apple'])){
            $param['apple'] = 1;
        }
        if (!empty($param['nopop'])){
            $param['nopop'] = 1;
        }
        $name_array = array('checktime'=>'检查周期', 'timeout'=>'超时时间', 'apple'=>'连接无线自动变完成', 'nopop'=>'连接无线不弹窗', 'whiteurl'=>'域名白名单', 'whitemac'=>'mac白名单');
        $content = '';
        foreach ($param as $k => $v) {
            if($k != 'ssid' && $v != $config_info[$k]){
                $param_task[$k] = $v;
                $content .= $name_array[$k] . '=' . $v;
            }
        }
      
        //创建升级任务
        if (count($param_task) > 0){
            $param1 = array(
                'router_mac'    => $router_info['router_mac'],
                'param'         => $param_task,
                'type'          => 'wifidog',
                'content'       => $content,
            );
            
            $router_task = D('RouterTask');
            $rs = $router_task->add_router_task($param1);
            if (!$rs){
                throw new Exception("创建任务失败，请重试", 1);
            }
            $b = true;
        }
        if (!$b){
            throw new Exception("没有做任何修改，无需创建任务", 1);
            return false;
        }
        $param_task['last_datetime'] = date('Y-m-d H:i:s');
        $param_task['ssid'] = $param['ssid'];
      
        //更新缓存
        $wifi_config_key = 'wifi_config:'.strtolower($router_info['router_mac']);
        $u_info = $this->get_wifidog_info($wifi_config_key);
        if ($u_info){
            foreach ($param as $k => $v) {
                if($v != $u_info[$k]){
                    $u_info[$k] = $v;
                }
            }
            $this->set_redis_for_router_wifidog($wifi_config_key, $u_info);
        }
        return '已下发热点配置任务，路由将在'.$router_info['check_time'].'秒左右执行任务';
    }
   
}