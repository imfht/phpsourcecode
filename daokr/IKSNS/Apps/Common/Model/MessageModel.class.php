<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月23日晚 03:30 站内消息模型
*/
namespace Common\Model;
use Think\Model;

class MessageModel extends Model
{
	
	// 发送消息
	public function sendMessage($userid, $touserid, $title, $content) {
		if ($touserid && $content) {
			$data = array (
					'userid' => $userid,
					'touserid' => $touserid,
					'title' => $title,
					'content' => $content,
					'addtime' => time () 
			);
			if (! false == $this->create ( $data )) {
				$messageid = $this->add ();
			}
		
		}
		return $messageid>0 ? $messageid : 0;
	}
	
}