<?php
 /*
 *
 * admin.GoodsImg  商品图片管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	
class GoodsImg extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');	
	}	

	public function goods_img_add_save($goods_id,$imglistname){
		//统一删除商品所有图片
		$this->C($this->cacheDir)->delete('fly_goods_img',"goods_id='$goods_id'");
		if(!empty($imglistname)){
			foreach($imglistname as $row){
				$sql = "insert into fly_goods_img(goods_id,img_path) values('$goods_id','$row')";
				$this->C($this->cacheDir)->update($sql);
			}			
		}
	}
	//得到商品的所有图片
	public function goods_img_list($id){
		if(empty($id)) $id=0;
		$sql	="select * from fly_goods_img where goods_id='$id'";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
}//
?>