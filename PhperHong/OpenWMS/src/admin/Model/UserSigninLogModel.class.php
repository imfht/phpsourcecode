<?php
namespace admin\Model;
use Think\Model;
use Think\Exception;
use Think\Cache;
use Think\Log;

class UserSigninLogModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('user_signinlog');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
     +----------------------------------------------------------
     * 添加每日商家设备登录日志
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
 	public function add_signin_log(){
          $date = date('Y-m-d');
          $count = $this->handler->where(array('date'=>$date))->count();
          if ($count == 0){
               $rs = $this->handler->add(array(
                    'date'    => date('Y-m-d'),
               ));
               if (!$rs){
                    Log::record('user_signinlog表插入数据失败，sql'.$this->handler->getLastSql());
               }
          }else{
               $rs = $this->handler->where(array('date'=>$date))->setInc('user_total', 1);
               if (!$rs){
                    Log::record('user_signinlog表更新数据失败，sql'.$this->handler->getLastSql());
               }
          }
 		
 	}
   
     /**
     +----------------------------------------------------------
     * 通过商家编号获取累计认证人数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
     public function get_signlog_for_sum_by_mid(){
          $sum = $this->handler->sum('user_total');
          if (!$sum){
               return 0;
          }
          return $sum;
     }
	 /**
     +----------------------------------------------------------
     * 通过商家编号获取最近10天的认证数及日期
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
     public function get_signlog_for_top10_by_mid($mid){
          return  $this->handler->limit('0, 10')->order('date desc')->select();
     }
     /**
     +----------------------------------------------------------
     * 通过商家编号获取最高认证数及日期
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
     public function get_signlog_for_max_by_mid(){
          $num = $this->handler->max('user_total');
          if (!$num){
               return array('user_total'=>0, 'date'=>date('Y-m-d'));
          }
          $info = $this->handler->where(array('user_total'=>$num))->order('date desc')->find();
          return array('user_total'=>$info['user_total'], 'date'=>$info['date']);

     }
}