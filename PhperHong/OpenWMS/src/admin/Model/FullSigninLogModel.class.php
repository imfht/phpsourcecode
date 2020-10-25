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


class FullSigninLogModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('fullsigninlog');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
     +----------------------------------------------------------
     * 添加用户登录日志
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
 	public function add_full_signin_log($param){
        
        $arr = parse_url(session('url'));
   		
        $client_token = md5(random(7).time());
 		$rs = $this->handler->add(array(
 		
 			'user_id'	=> $param['user_id'],
 			'username'	=> $param['username'],
 			'third_id'	=> $param['third_id'],
 			'auth_type'	=> $param['auth_type'],
 			'dateline'	=> date('Y-m-d H:i:s'),
           
            'browser_type' => session('browser_type'),
            'src_url'      => $arr['host'],
            'client_ip'    => $param['client_ip'],
       		'client_token'	=> $client_token,
 		));
 		
        if (!$rs){
            Log::record('添加用户登录日志失败，SQL：'.$this->handler->getLastSql());
        }
        $data = $this->handler->field('id')->where(array('client_token'=>$client_token))->find();
        
        return $data['id'];
 	}
     /**
     +----------------------------------------------------------
     * 更新在线时长
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
     public function update_user_time($mid, $id, $time){
          if (empty($mid)){
               Log::record('更新在线时长失败，商家热点账号为空');
               return false;
          }
          $mid = strtolower($mid);
          $mid = trim($mid);

          $rs = $this->handler->where(array('id'=>$id, 'mid'=>$mid))->save(array('online_time'=>$time));
          if ($rs === false){
               Log::record('更新在线时长失败，SQL：'.$this->handler->getLastSql());
          }
          return $rs;
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
     public function update_member_coming($mid, $id, $f, $num){
          if (empty($mid)){
               Log::record('fullsigninlog更新用户流量失败，商家热点账号为空');
               return false;
          }

          $rs = $this->handler->where(array('id'=>$id, 'mid'=>$mid))->setInc($f, $num);
          if ($rs === false){
            Log::record('fullsigninlog更新用户流量失败，SQL：'.$this->handler->getLastSql());
          }
          return $rs;
     }
    /**
     +----------------------------------------------------------
     * 更新用户流量及在线时长
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
    public function update_coming_and_times($id, $incoming, $outgoing, $time){
    	
    	$rs = $this->handler->where(array('id'=>$id))->save(array(
    		'incoming'	=> $incoming,
    		'outgoing'	=> $outgoing,
    		'online_time'=> $time
    	));
    	return $rs;
    }
    /**
     +----------------------------------------------------------
     * 批量更新用户流量及在线时长
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
    public function update_coming_and_times_more($param){
    	$tp = $this->handler->tablePrefix;
    	//$sql1 = 'INSERT INTO '.$tp.'fullsigninlog (id,`incoming`, `outgoing`, `online_time`) VALUES';

    	$ids = array();
    	$sql = 'UPDATE '.$tp.'fullsigninlog SET incoming = CASE id  '; 
		foreach ($param as $k => $v) { 
			$ids[] = $v['full_signin_log_id'];
		    $sql .= sprintf(" WHEN %d THEN %d ", $v['full_signin_log_id'], $v['incoming']); 
		} 
		$sql.= 'END,outgoing = CASE id';
		foreach ($param as $k => $v) { 
		    $sql .= sprintf(" WHEN %d THEN %d ", $v['full_signin_log_id'], $v['outgoing']); 
		} 
		$sql.= 'END,online_time = CASE id';
		foreach ($param as $k => $v) { 
		    $sql .= sprintf(" WHEN %d THEN %d ", $v['full_signin_log_id'], $v['online_time']); 
		} 
		$ids = implode(',', $ids);
		$sql .= "END WHERE id IN ($ids)"; 




		/*foreach($param as $k => $v){
			$sql1 .= "(" . $v['full_signin_log_id'] . ",'" . $v['incoming'] . "', '".$v['outgoing']."', '".$v['time']."'),";		
		}
		$sql1 = rtrim($sql1 , ',');
		$sql1 .= 'ON DUPLICATE KEY UPDATE `incoming`=VALUES(`incoming`),`outgoing`=VALUES(`outgoing`),`online_time`=VALUES(`online_time`)';*/
		Log::record('fullsigninlog批量更新用户流量失败，SQL：'.$sql);
		Log::record('fullsigninlog批量更新用户流量失败，SQL：'.json_encode($param));
		if (!$this->handler->execute($sql)){
			Log::record('fullsigninlog批量更新用户流量失败，SQL：'.$sql);
		}
		
    }
    
     /**
     +----------------------------------------------------------
     * 根据id获取数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $mid 用户编号
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_fullsigninlog_list_by_id($id){
		
		return $this->handler->where(array('id'=>array('IN', $id)))->select();
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
	public function get_signin_log_list_by_mid($pagenum, $pagelen, $sortkey, $reverse, $w){
		

	
		$where = array();
        if (!empty($w['username'])){
        	$where['username'] = $w['username'];
        }
      
       	if (!empty($w['auth_type'])){
       		$where['auth_type'] = $w['auth_type'];
       	}
       	if (!empty($w['time_start']) && !empty($w['time_end'])){
       		$where['dateline'] = array('BETWEEN', array($w['time_start'], $w['time_end']));
       	}
       	
		$sortkey = empty($sortkey) ? 'id' : $sortkey;
        $reverse = empty($reverse) ? 'desc' : $reverse;
        $pagelen = intval($pagelen) == 0 ? 20 : $pagelen;
        $start = 0;
        if (intval($pagenum) > 0){
        	$start = (intval($pagenum) - 1) * intval($pagelen);
        }
        
        $count = $this->handler->where($where)->count();
      	if ($count == 0){
      		return array('list'=>array(), 'count'=>0, 'router_list'=>$router_list);
      	}

		$list = $this->handler->where($where)->order($sortkey . ' ' . $reverse)->limit($start. ',' . $pagelen)->select();

		
       
       
		return array('list'=>$list, 'count'=>$count, 'router_list'=>$router_list);
		
	}
	/**
	 +----------------------------------------------------------
	 * 导出execl
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $mid string 商家mid
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function down_load_execl_for_signinlog($w){
		
		$where = array();
        if (!empty($w['username'])){
        	$where['username'] = $w['username'];
        }
      
       	if (!empty($w['auth_type'])){
       		$where['auth_type'] = $w['auth_type'];
       	}
       	if (!empty($w['time_start']) && !empty($w['time_end'])){
       		$where['dateline'] = array('BETWEEN', array($w['time_start'], $w['time_end']));
       	}
     
		
		$list = $this->handler->where($where)->select();

		
       
        $temp = array();
		//统计总金额
		$totalMoney = 0 ;
		for ($i = 0 ; $i < count($list) ; $i++ ) {
			$temp[$i][0] = $list[$i]['username'];
			$temp[$i][1] = auth_typeFiler($list[$i]['auth_type']);
			$temp[$i][2] = $list[$i]['dateline'];
			$temp[$i][3] = secondesToDay($list[$i]['online_time']);
			$temp[$i][4] = Bytes($list[$i]['incoming'], 'KB');
			$temp[$i][5] = Bytes($list[$i]['outgoing'], 'KB');
			$temp[$i][6] = $list[$i]['src_url'];
			$temp[$i][7] = $list[$i]['client_ip'];

		}
	


		$title = '用户名称,认证方式,上线时间,在线时长,下行流量,上行流量,来路地址,IP ';
		downToExcel($title,$temp,'认证记录');





        //downToExcel('',$list,'认证记录')
       
		
	}
}