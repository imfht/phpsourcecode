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

class MembersModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('members');
 		$this->cache   = Cache::getInstance();
 	}
    /**
     +----------------------------------------------------------
     * 添加用户
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
 	public function syncUser($param){
        //检测用户是否存在
        $member_info = $this->get_member_by_third_id($param['third_id']);
        if (!$member_info){
            //添加用户
            $rs = $this->handler->add(array(
                'username'  => $param['username'],
                'third_id'  => $param['third_id'],
                'auth_type' => $param['auth_type'],
                'avatar'    => $param['avatar'],
                'mac_hash'  => $param['mac_hash'],
                'create_time'   => date('Y-m-d H:i:s'),
            ));
            if (!$rs){
                Log::record('添加用户信息失败，参数：'.json_encode($param));
                return false;
            }
            $uid = $rs;
        }else{
            $uid = $member_info['user_id'];
        }

        session('username', $param['username']);
        session('avatar', $param['avatar']);
        session('auth_type', $param['auth_type']);
        session('third_id', $param['third_id']);

        return $uid;
    }
    /**
     +----------------------------------------------------------
     * 获取总用户数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $third_id
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_member_count(){
     	return $this->handler->count();
    }
    /**
     +----------------------------------------------------------
     * 根据third_id获取用户信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $third_id
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_member_by_third_id($third_id){
        $info = $this->handler->where(array('third_id'=>$third_id))->find();
        return $info;
    }
    /**
     +----------------------------------------------------------
     * 更新用户流量
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $id 用户编号
     * @param $f incoming 下行流量 ,outgoing 下行流量
     * @param $num 流量值
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function update_member_coming($id, $f, $num){
        $rs = $this->handler->where(array('user_id'=>$id))->setInc($f, $num);
        if ($rs === false){
            Log::record('更新用户流量失败，SQL：'.$this->handler->getLastSql());
        }
        return $rs;
    }
    /**
     +----------------------------------------------------------
     * 根据用户mac及认证方式获取用户信息，如果发现某一个认证有数据则返回
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $id 用户编号
     * @param $f incoming 下行流量 ,outgoing 下行流量
     * @param $num 流量值
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_user_info_by_usermac($mac, $auth_type){
    	$user_list = $this->handler->where(array('mac_hash'=>md5(strtolower($mac)), 'auth_type'=>array('IN', $auth_type)))->select();
    	
    	if (!$user_list){
    		return false;
    	}

    	$temp = array();
    	$user_list = ArraySetIndex($user_list, 'auth_type');
    	return $user_list;
    	
    }
    /**
     +----------------------------------------------------------
     * 统计全站认证方式
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function chart_auth_type($param){
    	$where = array();
    	if (!empty($param['time_start']) && !empty($param['time_end'])){
    		$where['create_time'] = array('between', $param['time_start'].','.$param['time_end']);
    	}
    	$list = $this->handler->field('COUNT(user_id) as _count, auth_type')->where($where)->group('auth_type')->select();
    	$data = array();
    	foreach ($list as $key => $value) {
    		$data[$value['auth_type']] = $value['_count'];
    	}
    	return $data;
    }
    /**
     +----------------------------------------------------------
     * 统计全站认证方式
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function chart_auth_type_agency($param){

    	$where = array();
    	if (!empty($param['time_start']) && !empty($param['time_end'])){
    		$where['create_time'] = array('between', $param['time_start'].','.$param['time_end']);
    	}
    	$list = $this->handler->field('COUNT(user_id) as _count, auth_type')->where($where)->group('auth_type')->select();
    	$data = array();
    	foreach ($list as $key => $value) {
    		$data[$value['auth_type']] = $value['_count'];
    	}
    	return $data;
    }
    
}