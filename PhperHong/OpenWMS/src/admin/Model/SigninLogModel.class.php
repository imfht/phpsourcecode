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

class SigninLogModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('signinlog');
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
                    Log::record('signinlog表插入数据失败，sql'.$this->handler->getLastSql());
               }
          }else{
               $rs = $this->handler->where(array('date'=>$date))->setInc('login_total', 1);
               if (!$rs){
                    Log::record('signinlog表更新数据失败，sql'.$this->handler->getLastSql());
               }
          }
 		
 	}
    
    
}