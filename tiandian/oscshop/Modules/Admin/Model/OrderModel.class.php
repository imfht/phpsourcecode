<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Admin\Model;

class OrderModel{
	
	/**
	 *显示订单状态单位分页	 
	 */
	public function show_order_page($search){
		
		$sql="SELECT o.order_id,o.order_num_alias,o.total,o.ip_region,o.payment_code,o.shipping_method,o.date_added,o.date_modified,m.uname,os.order_status_id,os.name FROM "
		.C('DB_PREFIX').'order o,'.C('DB_PREFIX').'member m,'.C('DB_PREFIX').'order_status os WHERE o.member_id=m.member_id AND '
		.'o.order_status_id=os.order_status_id ';
		
		if(isset($search['order_num'])){
			$sql.=" and o.order_num_alias='".$search['order_num']."'";
		}
		if(isset($search['user_name'])){
			$sql.=" and m.uname='".$search['user_name']."'";
		}
		if(isset($search['status'])){
			$sql.=" and os.order_status_id=".$search['status'];
		}
		
		$count=count(M()->query($sql));
		
		$Page = new \Think\Page($count,C('BACK_PAGE_NUM'));
		$show  = $Page->show();// 分页显示输出			
		
		$sql.=' ORDER BY o.order_id DESC LIMIT '.$Page->firstRow.','.$Page->listRows;
		
		$list=M()->query($sql);
	
		return array(
			'empty'=>'<tr><td colspan="20">~~暂无数据</td></tr>',
			'list'=>$list,
			'page'=>$show
		);

	}
	//订单信息
	public function order_info($id){
		//订单详情
		$order_sql="SELECT o.*,m.uname,m.email,o.shipping_tel,os.name,a.address FROM "
		.C('DB_PREFIX').'order o,'.C('DB_PREFIX').'member m,'.C('DB_PREFIX').'address a,'
		.C('DB_PREFIX').'order_status os WHERE o.member_id=m.member_id AND o.address_id=a.address_id and '		
		.'o.order_status_id=os.order_status_id AND o.order_id='.$id;		
		$order=M()->query($order_sql);		
		//商品清单
		$order_product=M('order_goods')->where('order_id='.$id)->select();
		//价格、运费
		$order_total = M()->query("SELECT * FROM " .C('DB_PREFIX').
		 "order_total WHERE order_id =" .$id." ORDER BY sort_order");
		//订单状态
		$order_statuses=M('OrderStatus')->select();
		//订单历史
		$order_history=M('order_history')->where(array('order_id'=>$id))->select();
		
		return array(
			'order'=>$order[0],
			'order_product'=>$order_product,
			'order_total'=>$order_total,
			'order_statuses'=>$order_statuses,
			'order_history'=>$order_history
		);
	}
	
 	function addOrderHistory($order_id, $data) {		
		
		$order['order_id']=$order_id;
		$order['date_modified']=time();
		$order['order_status_id']=$data['order_status_id'];
		M('Order')->save($order);		
		
		$oh['order_id']=$order_id;
		$oh['order_status_id']=$data['order_status_id'];
		$oh['notify']=(isset($data['notify']) ? (int)$data['notify'] : 0) ;
		$oh['comment']=strip_tags($data['comment']);
		$oh['date_added']=time();
		$oh_id=M('OrderHistory')->add($oh);

		return $oh_id;
		
	}

		public function getOrderHistories($order_id) {
		

		$query = M()->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " 
		. C('DB_PREFIX') . "order_history oh LEFT JOIN " 
		. C('DB_PREFIX') . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id 
	    . "' ORDER BY oh.date_added ASC");

		return $query;
	}

	function del_order($id){
		
		M('order')->where(array('order_id'=>$id))->delete();
		M('order_goods')->where(array('order_id'=>$id))->delete();
		M('order_history')->where(array('order_id'=>$id))->delete();
		M('order_total')->where(array('order_id'=>$id))->delete();			
					
		return array(
			'status'=>'success',
			'message'=>'删除成功',
			'jump'=>U('Order/index')
		);
	}
	
}
?>