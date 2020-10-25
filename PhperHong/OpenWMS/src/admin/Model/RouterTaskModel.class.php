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

class RouterTaskModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('router_task');
 		$this->cache   = Cache::getInstance();
 	}
    /**
     +----------------------------------------------------------
     * 检测是否有指定类型任务存在
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function check_type_task($mac, $type){
        $key = 'router_task:'.$mac.$type;
        return $this->cache->get($key);
    }
 	/**
     +----------------------------------------------------------
     * 插入任务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function add_router_task($param){
        if (empty($param['router_mac']) || empty($param['type'])){
            throw new Exception("任务创建失败，缺少关键数据", 1);
            return false;
        }
        //将任务写入redis中，设置有效期 TASK_TIME
        $key = 'router_task:'.$param['router_mac'].$param['type'];
        $router_task_list_for_redis = $this->cache->get($key);
        if ($router_task_list_for_redis){
            throw new Exception("任务已存在，请等待执行完成后再发送", 1);
            return false;
        }

   	
        $param['mid'] = strtolower($param['mid']);
        $param['router_mac'] = strtolower($param['router_mac']);
        
        $rs = $this->handler->add(array(
            'router_mac'    => $param['router_mac'],
            'type'          => $param['type'],
            'content'       => $param['type'] != 'upgrade' ? $param['content'] : '路由升级',
            'create_date'   => date('Y-m-d H:i:s'),
        ));
        if (!$rs){
            return false;
        }

        if ($param['type'] == 'upgrade'){
            $redis_value = array('task_id'=>$rs, 'type'=>'upgrade', 'command'=>'task_id='.$rs.';system:upgrade;', 'sv'=>$param['sv'], 'upgrade'=>$param['content']);
        }else if ($param['type'] == 'restart'){
            $redis_value = array('task_id'=>$rs, 'type'=>'restart', 'command'=>'task_id='.$rs.';system:restart;');
        }else if($param['type'] == 'wifidog'){
            $temp = '';
            foreach ($param['param'] as $k => $val) {
                $temp .= $k.'='.$val.';';
            }
            $redis_value = array('task_id'=>$rs, 'type'=>'wifidog', 'command'=>'task_id='.$rs.';wifidog:'.$temp);
        }else if($param['type'] == 'wifi'){
            $redis_value = array('task_id'=>$rs, 'type'=>'wifi', 'command'=>'task_id='.$rs.';wifi:'.$param['param']);
        }
        $this->cache->set($key, $redis_value, C('TASK_TIME'));
        
        return true;
    }
    /**
     +----------------------------------------------------------
     * 获取任务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
 	public function get_router_task($param){
        if (empty($param['gw_mac'])){
            return false;
        }
        $param['gw_mac']    = strtolower($param['gw_mac']);
       
        //WIFIDOG任务
        $key = 'router_task:'.$param['gw_mac'].'wifidog';
        $wifidog_task = $this->cache->get($key);
        if ($wifidog_task){
            $this->cache->rm($key);
            return $wifidog_task['command'];
        }
        //WIFI任务
        $key = 'router_task:'.$param['gw_mac'].'wifi';
        $wifi_task = $this->cache->get($key);
        if ($wifi_task){
            $this->cache->rm($key);
            return $wifi_task['command'];
        }
        //获取升级任务
        $key = 'router_task:'.$param['gw_mac'].'upgrade';
        $upgrade_task = $this->cache->get($key);
        if ($upgrade_task){
            if ($param['sv'] >= $upgrade_task['sv']){
                //路由版本大于任务升级版本，无需升级
                $this->update_task_status($upgrade_task['task_id'], 0);
                //redis删除任务
                $this->cache->rm($key);
                return false;
            }
            //$this->cache->rm($key);
            return $upgrade_task['command'];
        }
        //获取重启任务
        $key = 'router_task:'.$param['gw_mac'].'restart';
        $restart_task = $this->cache->get($key);
        if ($restart_task){
            $this->cache->rm($key);
            return $restart_task['command'];
        }
        return false;
    }
    /**
     +----------------------------------------------------------
     * 获取路由执行任务反馈结果
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function set_router_task_status($param){
        if (empty($param['gw_mac']) || empty($param['task_id'])){
            return false;
        }
        $param['gw_mac']    = strtolower($param['gw_mac']);
        $param['gw_id']     = strtolower($param['gw_id']);
        $this->update_task_status($param['task_id'], $param['ret']);
        
        return true;
    }
    /**
     +----------------------------------------------------------
     * 获取升级任务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_upgrade_task($param){
        
        if (empty($param['gw_mac'])){
            return false;
        }

        $param['gw_mac']    = strtolower($param['gw_mac']);

        //获取升级任务
        $key = 'router_task:'.$param['gw_mac'].'upgrade';
        $upgrade_task = $this->cache->get($key);

        if ($upgrade_task){
            if ($param['sv'] >= $upgrade_task['sv']){
                //路由版本大于任务升级版本，无需升级
                $this->update_task_status($upgrade_task['task_id'], 0);
                //redis删除任务
                $this->cache->rm($key);
                return false;
            }
            $this->cache->rm($key);
            return $upgrade_task['upgrade'];
        }
        return false;
    }
    /**
     +----------------------------------------------------------
     * 修改任务状态
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function update_task_status($id, $ret){
        $rs = $this->handler->where(array('id'=>$id))->save(array('ret'=>$ret));
        if ($rs === false){
            Log::record('任务状态修改失败，id='.$id.', ret='.$ret);
            return false;
        }
        return true;
    }
    /**
     +----------------------------------------------------------
     * 根据路由编号及用户编号获取该路由的任务日志
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_router_task_list(){
       
       

        $list = $this->handler->order('id desc')->select();
       
        return $list;
    }
   
}