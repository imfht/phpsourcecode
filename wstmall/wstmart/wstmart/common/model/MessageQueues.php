<?php
namespace wstmart\common\model;
use think\Db;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 消息队列
 */
class MessageQueues extends Base{
   /**
	* 新增
 	*/
	public function add($param){
		$shopId = $param["shopId"];
		$tplCode = $param["tplCode"];
		$msgcat = Db::name("shop_message_cats")->where(["msgCode"=>$tplCode])->find();
		if(!empty($msgcat)){
			$msgDataId = $msgcat["msgDataId"];
			$msgType = $param['msgType'];
			$dbo = Db::name("shop_users su")->join("__USERS__ u","su.userId=u.userId");
			if($msgType==4){
				$dbo = $dbo->where("u.wxOpenId!=''");
			}
			$where = "su.dataFlag=1 and FIND_IN_SET(".$msgType.",su.privilegeMsgTypes) and FIND_IN_SET(".$msgDataId.",su.privilegeMsgs)";
			if($msgType==2){//短信
				$where = "su.dataFlag=1 and FIND_IN_SET(".$msgType.",su.privilegeMsgTypes) and FIND_IN_SET(".$msgDataId.",su.privilegePhoneMsgs)";
			}
			$list = $dbo->where($where)->where(["su.shopId"=>$shopId])->field("su.userId,u.userPhone")->select();
			
			if($msgType==1){
				foreach ($list as $key => $user) {
					WSTSendMsg($user['userId'],$param['content'],$param['msgJson'],$msgType);
				}
			}else{
				$dataAll = [];
				$paramJson = $param["paramJson"];
				foreach ($list as $key => $user) {
					$data = [];

					if($msgType==2){//发短信
						if($user['userPhone']!=''){
							$paramJson['userPhone'] = $user['userPhone'];
						}else{
							continue;
						}
					}
					$paramJson["userId"] = $user["userId"];
					$data['userId'] = $user["userId"];
					$data['msgType'] = $msgType;
					$data['msgCode'] = $param['tplCode'];
					$data['paramJson'] = json_encode($paramJson);
					$data['msgJson'] = $param['msgJson'];
					$data['createTime'] = date('Y-m-d H:i:s');
					$data['sendStatus'] = 0;
					$dataAll[] = $data;
				}
				Db::name("message_queues")->insertAll($dataAll);
			}
		}
		
		
	}
	/**
	 * 发送成功修改状态
	 */
	public function edit($id){
		$data = [];
		$data['sendStatus'] = 1;
		$result = $this->where(["id"=>$id])->save($data);
       	return $result;
	}

}
