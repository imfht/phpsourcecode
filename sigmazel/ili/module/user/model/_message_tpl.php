<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 消息模板
 * @author sigmazel
 * @since v1.0.2
 */
class _message_tpl{
	//根据ID获得记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_message_tpl WHERE MESSAGE_TPLID = '{$id}'");
	}
	
	//根据编号获取记录
	public function get_by_serial($serial){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_message_tpl WHERE SERIAL = '{$serial}'");
	}
	
	//获取所有记录
	public function get_all(){
		global $db;
		
		$temparr = array();
		$temp_query = $db->query("SELECT * FROM tbl_message_tpl ORDER BY SERIAL ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[] = $row;
		}
		
		return $temparr;
	}
	
	//获取默认列表
	public function get_default(){
		$tpls = array();
		
		$tpls['OPENTM204650588'] = array(
		'SERIAL' => 'OPENTM204650588', 
		'TITLE' => '订单消息通知', 
		'CONTENT' => '{{first.DATA}}<br>消息类型：{{keyword1.DATA}}<br>跟进时间：{{keyword2.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{remark.DATA}}:如有疑问，请咨询客服。'
		);
		
		$tpls['OPENTM204618276'] = array(
		'SERIAL' => 'OPENTM204618276', 
		'TITLE' => '开奖结果通知', 
		'CONTENT' => '{{first.DATA}}<br>领取方式：{{keyword1.DATA}}<br>领取时间：{{keyword2.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{first.DATA}}:恭喜您，领到奖品！\n{{remark.DATA}}:如有疑问，请咨询客服。'
		);
		
		$tpls['OPENTM202243318'] = array(
		'SERIAL' => 'OPENTM202243318', 
		'TITLE' => '订单发货通知', 
		'CONTENT' => '{{first.DATA}}<br>订单内容：{{keyword1.DATA}}<br>物流服务：{{keyword2.DATA}}<br>快递单号：{{keyword3.DATA}}<br>收货信息：{{keyword4.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{first.DATA}}:您的订单已发货，我们正加速送到您的手上。\n{{remark.DATA}}:请您耐心等候。'
		);
		
		$tpls['OPENTM201843398'] = array(
		'SERIAL' => 'OPENTM201843398', 
		'TITLE' => '团购券发放通知', 
		'CONTENT' => '{{first.DATA}}<br>券号：{{keyword1.DATA}}<br>名称：{{keyword2.DATA}}<br>有效期：{{keyword3.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{first.DATA}}:恭喜您团购成功，电子券信息如下：\n{{remark.DATA}}:请在有效期内使用，点击可查看详情。'
		);
		
		$tpls['OPENTM201843387'] = array(
		'SERIAL' => 'OPENTM201843387', 
		'TITLE' => '团购券核销通知', 
		'CONTENT' => '{{first.DATA}}<br>券号：{{keyword1.DATA}}<br>名称：{{keyword2.DATA}}<br>核销门店：{{keyword3.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{first.DATA}}:您的电子券已成功核销。\n{{remark.DATA}}:如有疑问，请咨询客服。'
		);
		
		$tpls['TM00004'] = array(
		'SERIAL' => 'TM00004', 
		'TITLE' => '退款通知', 
		'CONTENT' => '{{first.DATA}}<br><br>退款原因：{{reason.DATA}}<br>退款金额：{{refund.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{first.DATA}}:您好，团购未成功，已退款。\n{{Remark.DATA}}:如有疑问，请咨询客服。'
		);
		
		$tpls['OPENTM201014137'] = array(
		'SERIAL' => 'OPENTM201014137', 
		'TITLE' => '积分兑换成功通知', 
		'CONTENT' => '{{first.DATA}}<br>礼品名称：{{keyword1.DATA}}<br>兑换门店：{{keyword2.DATA}}<br>扣除积分：{{keyword3.DATA}}<br>剩余可用积分：{{keyword4.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{first.DATA}}:恭喜您积分兑换成功！\n{{remark.DATA}}:如有疑问，请咨询客服。'
		);
		
		$tpls['OPENTM204658409'] = array(
		'SERIAL' => 'OPENTM204658409', 
		'TITLE' => '积分变动通知', 
		'CONTENT' => '{{first.DATA}}<br>用户名：{{keyword1.DATA}}<br>时间：{{keyword2.DATA}}<br>积分变动：{{keyword3.DATA}}<br>积分余额：{{keyword4.DATA}}<br>变动原因：{{keyword5.DATA}}<br>{{remark.DATA}}', 
		'REMARK' => '{{first.DATA}}:会员积分变更，敬请留意！\n{{remark.DATA}}:感谢您的使用！'
		);
		
		return $tpls;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_message_tpl', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_message_tpl', $data, "MESSAGE_TPLID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_message_tpl', "MESSAGE_TPLID = '{$id}'");
	}
	
}
?>