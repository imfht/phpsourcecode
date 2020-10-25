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
class RouterModel extends Model{
	protected $handler ;
	protected $cache;
	private   $redis_prefix='router:';
 	function __construct() {

 		$this->cache   = Cache::getInstance();
 	}
 	
 
 	/**
	 +----------------------------------------------------------
	 * 根据路由mac及商家热点账号获取路由信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mac 路由mac
	 * @param $mid 热点账号
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_router_info($mac, $field){
		$key = $this->redis_prefix.strtolower($mac);
		
		if($this->cache->exists($key) == 0){
			return false;
		}
		if (!is_array($field)){
			$field = array('router_mac', 'status', 'router_address', 'router_type', 'wan_ip', 'sv', 'type', 'start_time', 'client_ip', 'sys_uptime', 'sys_memfree', 'sys_load', 'wifidog_uptime', 'check_time', 'clientcount', 'online_time');
		}
		$router_info = array();
		if (count($field) > 1){
			$data = $this->cache->hmget($key, $field);
	 		foreach ($field as $k => $value) {
	 			$router_info[$value] = $data[$k];
	 		}
		}else if(count($field) == 1){
			$router_info[$field[0]] = $this->cache->hget($key, $field[0]);
		}
	 	
		
	 	return $router_info;
	}

	/**
	 +----------------------------------------------------------
	 * 修改路由信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mac 路由mac
	 * @param $mid 热点账号
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function update_router_info($mac, $f, $val){
	 	$key = $this->redis_prefix.strtolower($mac);
	 	if($this->cache->exists($key) == 0){
			return false;
		}
	 	$this->cache->hset($key, $f, $val);
	 	return true;
	}
 	/**
	 +----------------------------------------------------------
	 * 根据路由mac及商家热点账号更新路由信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mac 路由mac
	 * @param $mid 热点账号
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_router_info_by_mac($params){
    	$mac = strtolower($params['gw_mac']);
 		$key = $this->redis_prefix.$mac;
 		
 		
 		$client_ip = get_client_ip();

 	
 		//更新cache中的信息
 		$router_info = array();
 		$router_info['router_mac'] = $client_ip;
 		$router_info['client_ip'] = $mac;
		$router_info['sys_uptime'] = $params['sys_uptime'];
		$router_info['sys_memfree'] = $params['sys_memfree'];
		$router_info['sys_load'] = $params['sys_load'];
		$router_info['wifidog_uptime'] = $params['wifidog_uptime'];
		$router_info['check_time'] = intval($params['check_time']) == 0 ? 120 : intval($params['check_time']);
		$router_info['clientcount'] = $params['clientcount'];
		$router_info['router_address'] = $params['gw_address'];
		$router_info['router_type'] = $params['router_type'];
		$router_info['wan_ip'] = $params['wan_ip'];
		$router_info['sv'] = $params['sv'];

		$router_info['online_time'] = time();//最后响应时间时间
		

		$this->set_redis_for_router_info($key, $router_info);
 		return true;
 	}
 	public function set_redis_for_router_info($key, $router_info){
		$this->cache->hmset($key, $router_info);
		return true;
 	}


	/**
	 +----------------------------------------------------------
	 * 检测路由是否合法
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $id 商家编号
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function check_router_by_mac($mac){
		$router_mac = C('router_mac');
		if ($mac != $router_mac){
			return false;
		}
		return true;
	}
	/**
	 +----------------------------------------------------------
	 * 编辑路由
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function edit_router($router_mac){
		if (empty($router_mac)){
			throw new Exception("请填写路由MAC", 1);
		}
		$mac = C('router_mac');

		//判断是否修改了MAC
		if ($router_mac != $mac){
			//删除源路由redis
			$key = 'router:'.strtolower($mac);
			if ($this->cache->exists($key) == 1){
				$this->cache->rm($key);
			}
			//删除路由配置信息
			$router_wifi_config = D('RouterWifiConfig');
			$router_wifi_config->del_wifi_config($mac);

			$client = D('Client');
			//将源路由mac中的用户踢下线
			$online_user_list = $client->get_online_user_list();
			foreach($online_user_list as $val){
				$client->kick($val[0]);
			}
		}
		$config = array(
   			'router_mac'		=> $router_mac,
   		);
   		$this->update_config($config);
		return true;
	}
	//修改配置文件
	protected function update_config($new_config) {
		$config_file = CONF_PATH . '/router.php';
		if (is_writable($config_file)) {
			$config = require $config_file;
			$config = array_merge($config, $new_config);
			file_put_contents($config_file, "<?php \nreturn " . var_export($config, true) . ";", LOCK_EX);
			@unlink(RUNTIME_FILE);
			return true;
		} else {
			return false;
		}
	}
 	
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取路由列表--详细信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $id 商家编号
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_router_info_for_admin(){
		$router_name = C('router_name');
		$router_mac = 	strtolower(C('router_mac'));
		$clent = DD('Client');
		$RouterWifiConfig = DD('RouterWifiConfig');
		//查看是否有路由连接上来
		
		$key = $this->redis_prefix.$router_mac;
		

		$router_info = $this->get_router_info($router_mac);
		$router_info['router_mac'] = $router_mac;
		$router_info['router_name'] = $router_name;
		
		if (!$router_info){
			$router_info['status'] = '-1';//未连接
		}else{

			if ($router_info['status'] != '3'){
				if(time() - $router_info['online_time'] > ($router_info['check_time'])+120){
					$router_info['status'] = '-1';//未连接
				}else{
					$router_info['status'] = '1';//在线
				}
			}else{
				$router_info['status'] = 3;
			}
			
			//删除超时的在线用户
			$clent->del_timeout_user($router_info['check_time']);
			//获取认证在线用户数量
			$online_user_count = $clent->get_online_user_count();
			$router_info['ssid']				= $RouterWifiConfig->get_wifidog_onef($router_info['router_mac'], 'ssid');
			$router_info['check_time'] 			= $router_info['check_time'].'秒';
			$router_info['online_time'] 		= $router_info['status'] != -1 ? time() - $router_info['start_time'] : 0;
			$router_info['last_online_time']	= date('Y-m-d H:i:s', $router_info['online_time']);
			$router_info['start_time']			= date('Y-m-d H:i:s', $router_info['start_time']);
			$router_info['online_user_count'] 	= $online_user_count;
		}
		
		return $router_info;
	}
	
	
	
	/**
	 +----------------------------------------------------------
	 * 升级路由
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function upgrade($id){
		
		if(!$router_info || time() - $router_info['online_time'] > ($router_info['check_time'])+120){
			throw new Exception("路由不是在线状态，无法升级", 1);
		}

		//获取当前路由的版本n
		$router_sv = $router_info['sv'];
		if (empty($router_sv)){
			throw new Exception("不能获取当前路由的版本，无法升级", 1);
		}
		if ($router_sv < '4.2.26'){
			throw new Exception("当前路由的版本低于4.2.26，无法升级,请手动升级到4.2.26", 1);
		}
		//获取固件表中最新固件
		$firmware = D('Firmware');
		$firmware_info = $firmware->get_new_firmware();
		if (!$firmware_info){
			throw new Exception("未发现最新版本的固件，无法升级", 1);
		}
		
		//比较版本
		if ($router_sv >= $firmware_info['sv']){
			throw new Exception("该路由已经是最新版本，无需升级", 1);
		}
		//创建升级任务
		$content = 'upgrade:md5='.$firmware_info['md5'].'#url='.C('WEB_SITE').'/admin/upload/firmware/'.$firmware_info['firmware'].'#ver='.$firmware_info['sv'];
		$param = array(
            'router_mac'    => $router_info1['router_mac'],
            'mid'           => $router_info1['mid'],
            'type'          => 'upgrade',
            'content'       => $content,
            'sv'			=> $firmware_info['sv'],
        );
        
		$router_task = D('RouterTask');
		$rs = $router_task->add_router_task($param);
		if (!$rs){
			throw new Exception("创建任务失败，请重试", 1);
		}
		
		return '已下发升级任务，路由将在'.$router_info['check_time'].'秒左右执行任务,您可以在任务列表中查看任务状态';
	}
	/**
	 +----------------------------------------------------------
	 * 重启路由
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function reboot($id){
		
		//创建升级任务
		
		$param = array(
            'router_mac'    => $router_info1['router_mac'],
            'mid'           => $router_info1['mid'],
            'type'          => 'restart',
            'content'		=> '路由重启',
        );
        
		$router_task = D('RouterTask');
		$rs = $router_task->add_router_task($param);
		if (!$rs){
			throw new Exception("创建任务失败，请重试", 1);
		}
		
		return '已下发重启任务，路由将在'.$router_info['check_time'].'秒左右执行任务';
	}
	
	/**
	 +----------------------------------------------------------
	 * 将用户踢下线
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function tick($mac){
		if (empty($mac)){
			throw new Exception("用户不存在", 1);
		}
		$router_mac = strtolower(C('router_mac'));
		
		$key = $this->redis_prefix.$router_mac;
		
		$router_info = $this->get_router_info($router_mac, array('online_time', 'check_time'));

		if(!$router_info || time() - $router_info['online_time'] > ($router_info['check_time'])+120){
			throw new Exception("路由不是在线状态", 1);
		}
		//踢用户下线
		$client = DD('Client');
		$rs = $client->kick($mac);
		
		if (!$rs){
			throw new Exception("操作失败，请重试", 1);
		}
		return true;
	}
	
	
}