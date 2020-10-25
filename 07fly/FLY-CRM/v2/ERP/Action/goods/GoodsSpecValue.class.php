<?php
/*
 *
 * admin.GoodsSpecValue  商品规格值管理   
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
class GoodsSpecValue extends Action {

	private $cacheDir = ''; //缓存目录
	private $auth;
	public function __construct() {
		$this->auth = _instance( 'Action/sysmanage/Auth' );
	}
	public function goods_spec_value_add($data) {
		if ( !empty( $data ) ) {
			$this->goods_spec_value_del($data['spec_id']);
			for($i=0;$i<count($data['spec_value_name']);$i++){
				$savedata=array(
					'spec_id'=>$data['spec_id'],
					'spec_value_name'=>$data['spec_value_name'][$i],
					'sort'=>$data['spec_value_sort'][$i]
				 );
				$this->C( $this->cacheDir )->insert('fly_goods_spec_value',$savedata );
			}
			
		}
	}
	
	//删除单个值
	public
	function goods_spec_value_del($id) {
		if(empty($id)) $id=0;
		$this->C( $this->cacheDir )->delete('fly_goods_spec_value',"spec_id='$id'");
		return true;
	}
	//得到所有属性值名称
	public function goods_spec_value_list($id){
		if(empty($id)) $id=0;
		$sql	="select * from fly_goods_spec_value where spec_id='$id' order by sort asc";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	
	//得到所有属性值名称
	public function goods_spec_value_name($id){
		if(empty($id)) $id=0;
		$sql  ="select spec_value_name from fly_goods_spec_value where spec_id='$id' order by sort asc";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "【".$row["spec_value_name"]."】 ";
			}
		}
		return $str;
	}


} //
?>