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

class ClientModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('client');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
     +----------------------------------------------------------
     * 添加用户设备
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
 	public function add_client($param){
 		if (empty($param['mac']) || empty($param['user_id'])){
			Log::record('mac或user_id数据为空,无法存储:'.json_encode($param));
			return false;
		}

 		//更新当天商户登录记录统计
		$signinlog = DD('SigninLog');
		$signinlog->add_signin_log();

		//登录用户记录
		$usersigninlog = DD('SigninUserLog');
		$usersigninlog->add_signin_user_log(array('mac_hash' => $param['mac_hash'], 'auth_type'=>$param['auth_type']));

		//更新用户登录日志，详细信息
		$full_signin_log = DD('FullSigninLog');
        $data = array(
            
            'user_id'   => $param['user_id'],
            'username'  => $param['username'],
            'third_id'  => $param['third_id'],
            'auth_type' => $param['auth_type'],
            'client_ip'	=> get_client_ip(),
        );
        $fullsigninlog_id = $full_signin_log->add_full_signin_log($data);
        if (!$fullsigninlog_id){
            Log::record('添加用户登录日志失败，参数：'.json_encode($data));
        }
        
		$param['full_signin_log_id'] = $fullsigninlog_id;


		$client = $this->handler->where(array('mac_hash'=>$param['mac_hash'], 'third_id'=>$param['third_id'], 'auth_type'=>$param['auth_type']))->find();
		//更新或者添加设备
		if($client) {
			$rs = $this->handler->where(array('id'=>$client['id']))->save(array('times'=>intval($client['times'])+1, 'lastvisit_time'=>date('Y-m-d H:i:s')));
		}else{
			$rs = $this->handler->add(array(
				
				'mac_hash'			=> $param['mac_hash'],
				'mac'				=> $param['mac'],
				'user_id'			=> $param['user_id'],
				'username'			=> $param['username'],
				'third_id'			=> $param['third_id'],
				'auth_type'			=> $param['auth_type'],
				'create_time'		=> date('Y-m-d H:i:s'),
				'lastvisit_time'	=> date('Y-m-d H:i:s'),
				
				'device_type'		=> session('deviceType'),
				'devices_cj'		=> session('devices_cj'),
				
			));
		}
		
        
		if (!$rs){
			Log::record('添加设备表失败失败，SQL：'.$this->handler->getLastSql());
			return false;
		}
		$token = $this->add_users_for_redis($param);

		return $token;
 	}

    /**
	 +----------------------------------------------------------
	 * 存储数据到redis
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
 	public function add_users_for_redis($param){
 		$token = md5($param['user_id'].time());
 		$key = 'user:'.strtolower($param['mac']);
		$this->cache->hmset($key, array(
			'incoming'	=> 0,
			'outgoing'	=> 0,
			'mac_hash'	=> $param['mac_hash'],
			'mac'		=> $param['mac'],
			'user_id'	=> $param['user_id'],
			'username'	=> $param['username'],
			'third_id'	=> $param['third_id'],
			'auth_type' => $param['auth_type'],
			'deviceType'=> session('deviceType'),
			'router_mac'=> $param['router_mac'],
			'url'		=> session('url'),
			'client_ip'	=> get_client_ip(),
			'status'	=> -1,
			'token'		=> $token,
			'start_date_time'	=> 0,
			'end_date_time'		=> 0,
			'full_signin_log_id'	=> $param['full_signin_log_id'],
		));

		$this->cache->expire($key,172800); //缓存2天

		return $token;
 	}
 	/**
	 +----------------------------------------------------------
	 * 获取用户token
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_user_token($mac){
	 	$key = 'user:'.strtolower($mac);
	 	if ($this->cache->exists($key) == 0){
	 		return false;
	 	}
	 	$token = $this->cache->hget($key, 'token');
	 	
		return $token;
	}
	/**
	 +----------------------------------------------------------
	 * 验证用户token
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function check_user_token($mac, $token){
		$key = 'user:'.strtolower($mac);
		if ($this->cache->exists($key) == 0){
	 		return false;
	 	}
	 	$rtoken = $this->cache->hget($key, 'token');
	 	
	 	if($token == $rtoken){

	 		return true;
	 	}else{
	 		Log::record('token验证失败:mac'.$mac.',token:'.$token);
	 	}
	 	
	 	return false;
	 	 
	}
	/**
	 +----------------------------------------------------------
	 * 更新用户信息,如果没有该用户互相则返回false
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function update_user_info($mac, $f, $val){
	 	$key = 'user:'.strtolower($mac);
	 	if ($this->cache->exists($key) == 0){
	 		return false;
	 	}
	 	//如果是流量，则叠加
	 	if ($f == 'incoming' || $f == 'outgoing'){
	 		//字节转换成kb
			$val = floor($val/1024);
	 		$this->cache->hincrby($key, $f, $val);
	 	}else{
	 		$this->cache->hset($key, $f, $val); 
	 	}
	 	
		return true;
	}

	 /**
	 +----------------------------------------------------------
	 * 获取用户信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_user_info($mac, $field){
		$key = 'user:'.strtolower($mac);
	 	$user_info = array();
	 	if ($this->cache->exists($key) == 0){
	 		return false;
	 	}
 		if (!is_array($field)){
 			$field = array(
 				'incoming', 
 				'outgoing', 
 				'mid', 
 				'mac_hash', 
 				'mac', 
 				'user_id', 
 				'username', 
 				'third_id', 
 				'auth_type', 
 				'router_mac', 
 				'client_ip', 
 				'status', 
 				'token', 
 				'start_date_time', 
 				'end_date_time', 
 				'full_signin_log_id',
 				'deviceType',
 				'url'
 			);
 		}
 		$data = $this->cache->hmget($key, $field);

 		foreach ($field as $k => $value) {
 			$user_info[$value] = $data[$k];
 		}

	 	return $user_info;
	}

	
	/**
	 +----------------------------------------------------------
	 * 将用户踢下线
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	public function kick($mac, $isupdate=true){
	
		$user_info = $this->get_user_info($mac, array('status', 'start_date_time', 'full_signin_log_id', 'incoming', 'outgoing', 'user_id', 'third_id'));
		
		if (!$user_info || $user_info['status'] != 1){
			$this->del_list($mac);
			Log::record('用户已下线'.$mid.'==='.$mac);
			return false;
		}
		
		$start_date_time = $user_info['start_date_time'];
		$full_signin_log_id = $user_info['full_signin_log_id'];
		// 更新用户结束时间
		$this->update_user_info($mac, 'end_date_time', time());
		// 设置用户状态为下线
		$this->update_user_info($mac, 'status', 0);
		// 清除token
		$this->update_user_info($mac, 'token', '');
		$this->update_user_info($mac, 'incoming', 0);
		$this->update_user_info($mac, 'outgoing', 0);

		//在线列表中删除该用户
		$del_rs = $this->del_list($mac);
		
		if ($isupdate){
			// 如果设置强制下线时间, 则在线时间不得大于强制下线时间和请求间隔时间
			$router = DD('Router');
			$router_info = $router->get_router_info($router_mac, array('check_time'));

			$merchant = DD('Merchant');
			$merchant_online_times = $merchant->get_merchant_online_times();
			$time = time() - intval($start_date_time);
			if($merchant_online_times > 0) {
				$check_time = intval($router_info['check_time']);
				$time = min($merchant_online_times + $check_time, $time);
			}
			$incoming = intval($user_info['incoming']);
			$outgoing = intval($user_info['outgoing']);
			// 更新在线时长
			$full_signin_log = DD('FullSigninLog');
			$full_signin_log->update_coming_and_times($full_signin_log_id, $incoming, $outgoing, $time);
			
		}
		Log::record('用户下线：'.$mid.'==='.$mac.'用户数据'.json_encode($user_info));
		return true;
	}
	
	 /**
	 +----------------------------------------------------------
	 * 将用户mac与商家mid进行对应 在线列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function set_list($mac){
		if (empty($mac)){
			return false;
		}
		$mac = strtolower($mac);
		//更新用户结束时间
		$time = time();
		Log::record('更新用户时间'.$mac.'==='.date('Y-m-d H:i:s'));
		return $this->cache->zadd('all_online_user_list', $time, $mac);
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 删除超时用户
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function del_timeout_user($check_time = 120){
		 // 10s 补充时间, 网络延时等等
		$time_out_user = $this->cache->zrevrangebyscore('all_online_user_list', time()-intval(120 + $check_time), 0);
	
		$b = true;
		if (count($time_out_user)>1){
			$b = false;
		}
		foreach($time_out_user as $val){
			Log::record('用户下线：'.$val);
		
			$this->cache->zrem('all_online_user_list', $val);
			
			$this->kick($val, $b);
			
		}
		//如果有多个过期用户，则批量更新在线时长及流量
		if ($b == false){
			$up_array = array();
			foreach($time_out_user as $val){
				$user_info = $this->get_user_info($val, array('router_mac', 'status', 'start_date_time', 'full_signin_log_id', 'incoming', 'outgoing'));
				
				if (!$user_info){
					continue;
				}
				$router_mac = $user_info['router_mac'];
				$mid = $user_info['mid'];
				$start_date_time = $user_info['start_date_time'];
				
				// 如果设置强制下线时间, 则在线时间不得大于强制下线时间和请求间隔时间
				$router = DD('Router');
				$router_info = $router->get_router_info($router_mac, array('check_time'));

				$merchant = DD('Merchant');
				$merchant_online_times = $merchant->get_merchant_online_times();
				$time = time() - intval($start_date_time);
				if($merchant_online_times > 0) {
					$check_time = intval($router_info['check_time']);
					$time = min($merchant_online_times + $check_time, $time);
				}
				
				$up_array[] = array(
					'full_signin_log_id'	=> $user_info['full_signin_log_id'],
					'incoming'	=> $user_info['incoming'],
					'outgoing'	=> $user_info['outgoing'],
					'time'	=> $time,
				);
			}
			if (count($up_array)>0){
				$full_signin_log = DD('FullSigninLog');
				$full_signin_log->update_coming_and_times_more($up_array);
			}
		}
		return true;
	}
	/**
	 +----------------------------------------------------------
	 * 获取在线用户mac
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_online_user_list(){
		$router_mac = strtolower($router_mac);
		$allkey = $this->cache->zrange('all_online_user_list', 0, -1);
		return $allkey;
	}
	/**
	 +----------------------------------------------------------
	 * 获取在线用户数
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_online_user_count(){
		$this->del_timeout_user();
		$count = $this->cache->zcard('all_online_user_list');
		
		return intval($count);
	}
	/**
	 +----------------------------------------------------------
	 * 获取在线用户列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_online_user_all($pagenum, $pagelen){
		$this->del_timeout_user();
		$user_keys = $this->cache->zrange('all_online_user_list', 0, -1);
		$user_list = array();
		if (count($user_keys) == 0){
			return false;
		}
		$mid = '';
		$router_mac = '';

		$pagelen = intval($pagelen) == 0 ? 20 : $pagelen;
        $start = 0;
        if (intval($pagenum) > 0){
        	$start = (intval($pagenum) - 1) * intval($pagelen);
        }

        $end = $start+$pagelen;
        if ($end > count($user_keys)){
        	$end = count($user_keys);
        }
        for($i=$start; $i<$end;$i++){
        	$temp = $this->get_user_info($user_keys[$i], array('incoming', 'outgoing', 'mac', 'username', 'auth_type', 'start_date_time', 'deviceType','url'));
			$temp['online_time'] = time() - $temp['start_date_time'];
			$user_list[] = $temp;
        }
		

		if (count($user_list) == 0){
			return false;
		}
		
	
		return array('list'=>$user_list, 'count'=>count($user_keys));
		
		
	}
	
	/**
	 +----------------------------------------------------------
	 * 将用户mac在 list中删除
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function del_list($mac){
		$mac = strtolower($mac);
		$rs = $this->cache->zrem('all_online_user_list', 'user:'.$mac);
		if (!$rs){
			Log::record('删除超时用户失败，mac:'.$mac);
		}
		
	}
	/**
	 +----------------------------------------------------------
	 * 根据third_id获取设备数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_user_list_by_thirdid($third_id){
		$list = $this->handler->where(array('third_id'=>array('IN', $third_id)))->select();
		return $list;
	}
	/**
     +----------------------------------------------------------
     * 获取在线用户详细信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function get_online_user_info($mac){
        
		
		
		$user_info = $this->get_user_info($mac, array('status', 'third_id', 'incoming', 'outgoing', 'mac', 'username', 'auth_type', 'start_date_time', 'full_signin_log_id', 'client_ip'));
		if (!$user_info){
			throw new Exception("用户不存在", 1);
			return false;
		}
		if ($user_info['status'] != 1){
			$this->kick($mac);
			throw new Exception("该用户已下线", 1);
			return false;
		}
		//获取用户数据
		$client_info = $this->get_user_list_by_thirdid($user_info['third_id']);
		//获取当前登录日志数据
		$full_signin_log = DD('FullSigninLog');
		$full_signin_log_list = $full_signin_log->get_fullsigninlog_list_by_id($user_info['full_signin_log_id']);
		$user_info['times'] = $client_info[0]['times'];
		$user_info['device_type'] = $client_info[0]['device_type'];
		$user_info['devices_cj'] = $client_info[0]['devices_cj'];
		$user_info['src_url'] = $full_signin_log_list[0]['src_url'];
		$user_info['online_time'] = time() - $user_info['start_date_time'];
		$user_info['start_date_time'] = date('Y-m-d H:i:s', $user_info['start_date_time']);

		return $user_info;
    }

	/**
	 +----------------------------------------------------------
	 * 历史用户列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_user_list($pagenum, $pagelen, $sortkey, $reverse, $w){
		
        $where = array();
        if (!empty($w['auth_type'])){
        	$where['auth_type'] = $w['auth_type'];
        }
        if (!empty($w['device_type'])){
        	$where['device_type'] = $w['device_type'];
        }

        if (!empty($w['date_type']) && in_array($w['date_type'], array('create_time', 'lastvisit_time'))){
        	if (!empty($w['time_start']) || !empty($w['time_end'])){
        		if (empty($w['time_start']) && !empty($w['time_end'])){
	        		$w['time_start'] = date('Y-m-d');
	        	}
	        	if (empty($w['time_end']) && !empty($w['time_start'])){
	        		$w['time_end'] = date('Y-m-d');
	        	}
	        	$w['time_end'] = $w['time_end'] . ' 23:59:59';
	        	if (strtotime($w['time_start']) < strtotime($w['time_end'])){
	        		$where[$w['date_type']] = array('BETWEEN', array($w['time_start'], $w['time_end']));
	        	}
        	}
        	
        
        }


		$sort_array = array('auth_type', 'times', 'create_time', 'lastvisit_time', 'device_type');					
		$sortkey = empty($sortkey) || !in_array($sortkey, $sort_array) ? 'id' : $sortkey;
        $reverse = empty($reverse) ? 'desc' : $reverse;
        $pagelen = intval($pagelen) == 0 ? 20 : $pagelen;
        $start = 0;
        if (intval($pagenum) > 0){
        	$start = (intval($pagenum) - 1) * intval($pagelen);
        }
        $count = $this->handler->where($where)->count();
      
		$list = $this->handler->where($where)->order($sortkey . ' ' . $reverse)->limit($start. ',' . $pagelen)->select();
		
		
	
		return array('list'=>$list, 'count'=>$count, 'router_list'=>$router_list);
	}
	/**
	 +----------------------------------------------------------
	 * 根据获取历史用户列表(导出execl)
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function down_load_execl_for_userlist($w){
		

		

        $where = array();
        if (!empty($w['auth_type'])){
        	$where['auth_type'] = $w['auth_type'];
        }
        if (!empty($w['device_type'])){
        	$where['device_type'] = $w['device_type'];
        }
       
        if (!empty($w['date_type']) && in_array($w['date_type'], array('create_time', 'lastvisit_time'))){
        	if (!empty($w['time_start']) || !empty($w['time_end'])){
        		if (empty($w['time_start']) && !empty($w['time_end'])){
	        		$w['time_start'] = date('Y-m-d');
	        	}
	        	if (empty($w['time_end']) && !empty($w['time_start'])){
	        		$w['time_end'] = date('Y-m-d');
	        	}
	        	$w['time_end'] = $w['time_end'] . ' 23:59:59';
	        	if (strtotime($w['time_start']) < strtotime($w['time_end'])){
	        		$where[$w['date_type']] = array('BETWEEN', array($w['time_start'], $w['time_end']));
	        	}
        	}
        	
        
        }
		
		$list = $this->handler->where($where)->select();
		
		$temp = array();
		//统计总金额
		$totalMoney = 0 ;
		for ($i = 0 ; $i < count($list) ; $i++ ) {
			$temp[$i][0] = $list[$i]['username'];
			$temp[$i][1] = $list[$i]['mac'];
			$temp[$i][2] = auth_typeFiler($list[$i]['auth_type']);
			$temp[$i][3] = $list[$i]['times'];
			$temp[$i][4] = $list[$i]['create_time'];
			$temp[$i][5] = $list[$i]['lastvisit_time'];
			$temp[$i][6] = device_typeFiler($list[$i]['device_type']);
			$temp[$i][7] = devices_cjFiler($list[$i]['devices_cj']);
		}
	
		$title = '用户名称,用户MAC,认证方式,认证次数,创建时间,最后一次响应时间,设备类型,设备厂家 ';
		downToExcel($title,$temp,'设备列表');
		
	}
	/**
	 +----------------------------------------------------------
	 * 历史用户详情
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_ls_user_info($id){
		$user_info = $this->handler->where(array('id'=>$id))->find();
		return $user_info;
	}
	
	/**
	 +----------------------------------------------------------
	 * 根据mid获取今日新老访客分布
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_client_new_old_list_by_mid(){
		$list = $this->handler->field('times')->where(array('lastvisit_time'=>array('EGT', date('Y-m-d'))))->group('mac')->select();
		//获取新用户数和老用户数
		$count = array('new'=>0, 'old'=>0);
		foreach ($list as $key => $value) {
			if ($value['times'] == 1){
				$count['new']++;
			}else{
				$count['old']++;
			}
		}
		return $count;
	}
	/**
	 +----------------------------------------------------------
	 * 根据mid获取今日终端类型分布
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_client_device_type_by_mid(){
		$list = $this->handler->field('device_type')->where(array('lastvisit_time'=>array('EGT', date('Y-m-d'))))->group('mac')->select();
		//获取新用户数和老用户数
		$count = array('Phone'=>0, 'computer'=>0, 'Tablet'=>0);
		foreach ($list as $key => $value) {
			
			$count[$value['device_type']]++;
			
		}
		return $count;
	}
	/**
	 +----------------------------------------------------------
	 * 根据mid获取手机认证用户包含虚拟短信认证
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function get_client_phone_list_by_mid($pagenum, $pagelen, $sortkey, $reverse, $w){
		
		$where = array('auth_type'=>array('IN', 'mobile,virtualmobile'));
        if (!empty($w['username'])){
        	$where['username'] = $w['username'];
        }
       
		$sortkey = empty($sortkey) ? 'id' : $sortkey;
        $reverse = empty($reverse) ? 'desc' : $reverse;
        $pagelen = intval($pagelen) == 0 ? 20 : $pagelen;
        $start = 0;
        if (intval($pagenum) > 0){
        	$start = (intval($pagenum) - 1) * intval($pagelen);
        }
        $count = $this->handler->field('id')->where($where)->group('username')->select();
        $count = count($count);
       
		$list = $this->handler->where($where)->group('username')->order($sortkey . ' ' . $reverse)->limit($start. ',' . $pagelen)->select();
		
		return array('list'=>$list, 'count'=>$count);
		
	}
	/**
     +----------------------------------------------------------
     * 根据用户mac及认证方式获取用户信息，如果发现某一个认证有数据则返回
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
	public function get_user_info_by_usermac($mac, $auth_type){
		$user_list = $this->handler->where(array('mac_hash'=>md5(strtolower($mac)), 'auth_type'=>array('IN', $auth_type)))->group('auth_type')->select();
    	if (!$user_list){
    		return false;
    	}
    	$temp = array();
    	$user_list = ArraySetIndex($user_list, 'auth_type');
    	return $user_list;
	}
}