<?php

namespace app\common\model;


use GatewayClient\Gateway;
use think\facade\Db;

/**
 * ============================================================================
 * DSKMS多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 数据层模型
 */
class  InstantMessage extends BaseModel {

    public $page_info;
    /**
     * 获取服务机构通知列表
     * @access public
     * @author csdeshang
     * @param type $condition
     * @param type $pagesize
     * @param type $order
     * @return type
     */
    public function getInstantMessageList($condition,$pagesize='',$order='instant_message_id desc'){
        if ($pagesize) {
            $result = Db::name('InstantMessage')->where($condition)->order($order)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            return $result->items();
        } else {
            return Db::name('InstantMessage')->where($condition)->order($order)->limit(10)->select()->toArray();
        }
    }
    /**
     * 取得服务机构通知信息
     * @access public
     * @author csdeshang 
     * @param array $condition 检索条件
     * @param string $fields 字段
     * @param string $order 排序
     * @return array
     */
    public function getInstantMessageInfo($condition = array(), $fields = '*') {
        return Db::name('InstantMessage')->where($condition)->field($fields)->find();
    }
    
    /**
     * 添加服务机构通知信息
     * @access public
     * @author csdeshang  
     * @param array $data 参数数据
     * @return type
     */
    public function addInstantMessage($data) {
        return Db::name('InstantMessage')->insertGetId($data);
    }
    
    /**
     * 编辑服务机构通知信息
     * @access public
     * @author csdeshang 
     * @param array $data 更新数据
     * @param array $condition 条件
     * @return bool
     */
    public function editInstantMessage($data, $condition = array()) {
        return Db::name('InstantMessage')->where($condition)->update($data);
    }

    /**
     * 获取服务机构通知数量
     * @access public
     * @author csdeshang 
     * @param array $condition 条件
     * @return bool
     */
    public function getInstantMessageCount($condition = array()) {
        return Db::name('InstantMessage')->where($condition)->count();
    }

    
    public function sendInstantMessage($instant_message,$auto=false){
        //更新状态
        $data=array('instant_message_verify'=>1,);
        if(!$auto){
            $data['instant_message_verify_time']=TIMESTAMP;
        }
        if(!$this->editInstantMessage($data,array('instant_message_verify'=>0,'instant_message_id'=>$instant_message['instant_message_id']))){
            return ds_callback(false,'消息审核失败');
        }
        if(!config('ds_config.instant_message_register_url')){
            return ds_callback(false,'未设置直播聊天gateway地址');
        }

        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        try{
        Gateway::$registerAddress = config('ds_config.instant_message_register_url');
        if($instant_message['instant_message_to_type']==2){
            Gateway::sendToGroup('live_apply_'.$instant_message['instant_message_to_id'], json_encode($instant_message));
        }
        }catch(\Exception $e){
          return ds_callback(false,$e->getMessage());
        }
        return ds_callback(true);
    }
}

?>
