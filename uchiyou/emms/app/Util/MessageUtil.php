<?php

namespace App\Util;

use App\ApplyRecord;
use App\TreeTrunk;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Message;
use App\Material;
use App\UsingRecord;

/**
 * 构造消息的工具类，
 * 消息表只有text 类型的 content 为消息体，因此需要构造各种类型的消息内容
 */
class MessageUtil {
	// action 字段的种类
	const EVENT_PURCHASEAPPLY = 1;// 有操作通知，购买物资申请，阅读该消息时，有审批操作。只有审批后，该消息才会处于已处理状态（不在显示）
	const EVENT_REPLY = 2;// 无操作消息， 只要用户阅读过一次该消息，该消息就处于已处理状态（不在显示）。
	const EVENT_READ_ONCE = 3;
	/*
	 * 调用此方法需要用户已登录
	 */
	public static function getPurchaseApplyMessage(ApplyRecord $applyRecord,$treeTrunkName,User $user) {
		
		if (empty ( $user ) == false) {
			return '<div class="ibox-content">
					<h4>物资购买申请信息</h4>
                        <table class="table">
					 		<thead>
                                <tr>
                                    <th>部门</th>
                                    <th>员工姓名</th>
                                    <th>员工工号</th>
                                    <th>资产类型</th>
                                    <th>物资类别</th>
                                    <th>物资名称</th>
                                    <th>物资价格</th>
                                    <th>物资数量</th>
                                    <th>描述</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>' . $treeTrunkName . '</td>
                                    <td>' . $user->name . '</td>
                                    <td>' . $user->number . '</td>
                                    <td>' . $applyRecord->main_type . '</td>
                                    <td>' . $applyRecord->type . '</td>
                                    <td>' . $applyRecord->name . '</td>
                                    <td>' . $applyRecord->price . '</td>
                                    <td>' . $applyRecord->quantity . '</td>
                                    <td>' . $applyRecord->description . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    ';
		}
		return false;
	}
	public static function getPurchaseApplyReply($content,$senderName,$action){
		return '您好 ！ <br> '.$senderName.'已经 '.$action.' <br>'.$content;
	}
	/*
	 * 获取故障信息
	 * @param Material $material 物资记录
	 * @param $faultDescrption 故障申报者对故障的描述
	 * @param $department 名称
	 * @param $userName 申报人姓名
	 */
	public static function getRepaireMessage(Material $material,$faultDescription,$department,$userName){
		return '<div class="ibox-content">
					<h4>物资故障信息</h4>
                        <table class="table">
					 		<thead>
                                <tr>
                                    <th>部门</th>
                                    <th>申报人</th>
                                    <th>物资名称</th>
                                    <th>物资编号</th>
                                    <th>资产类型</th>
                                    <th>物资类别</th>
                                    <th>故障描述</th>
                                    <th>上报时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>' . $department. '</td>
                                    <td>' . $userName. '</td>
                                    <td>' . $material->name . '</td>
                                    <td>' . $material->number . '</td>
                                    <td>' . $material->main_type . '</td>
                                    <td>' . $material->type . '</td>
                                    <td>' . $faultDescription . '</td>
                                    <td>' . Date(DateUtil::FORMAT) . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    ';
	}
	/*
	 * 获取开始递送物资的消息
	 * @param $usingRecord 本次租借物资的记录
	 */
	public static function getStartDeliverMessage(UsingRecord $usingRecord){
		return '您好 ! <br/> 
					您预订的 '.$usingRecord->material_name.' 
							在 '.date(DateUtil::FORMAT).'开始递送，请等待接收 ！
				<br> 祝您生活愉快 !
				';
	}
	/*
	 * 当成功接收到递送物资的消息
	 * $usingRecord 是包含 UsingRecord 表和 递送人id 的一条联合查询记录
	 * $name 是接收者的姓名
	 */
	public static function getAcceptedDeliverMessage($usingRecord,$name){
		return '您好 ! <br/> 
					'.$name.'在 '.date(DateUtil::FORMAT).'接收到编号为
							'.$usingRecord->material_number.'
							的'.$usingRecord->material_name.'，感谢您的付出！
				<br> 祝您生活愉快 !
				';
	}
}
