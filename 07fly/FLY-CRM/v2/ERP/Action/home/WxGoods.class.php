<?php
/*
 * 产品管理类
 */	
class WxGoods extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		$this->goods_img=_instance('Action/home/WxGoodsImg');
		
	}	
	public function goods(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = 10;//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");
		$typeid	   = $this->_REQUEST("typeid");
		$where_str = " state='1' ";
		if (!mb_check_encoding($name, 'utf-8')){ 
		  $name = iconv('gb2312', 'utf-8', $name);
		}
		if( !empty($name) ){
			$where_str .=" and name like '%$name%'";
		}
		if( !empty($typeid) ){
			$where_str .=" and type_id='$typeid'";
		}
		//**************************************************************************
		$countSql    = "select * from fly_goods where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_goods  where $where_str order by goods_id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		if(empty($list)){ $list='null';}
		$typelist	 =$this->L('home/WxShopType')->shop_type();
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage,
							"name"=>$name,"typelist"=>$typelist,
							);	
		return $assignArray;
		
	}
	public function goods_json(){
		$assArr  = $this->goods();
		$rtnArr  =array('code'=>'sucess','message'=>'加载数据','list'=>$assArr['list']);
		echo json_encode($rtnArr);
	}	
	public function goods_show(){
			$assArr  = $this->goods();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('home/goods_show.html');	
	}
	
	//按店铺显示
	public function goods_show_shop(){
		$id	 = $this->_REQUEST("id");
		$sql = "select p.*,s.shop_name,s.shop_id from fly_goods as p left join fly_shop as s on p.shop_id=s.shop_id where p.shop_id='$id' and p.state='1' ";
		$list = $this->C($this->cacheDir)->findAll($sql);	
		$smarty  = $this->setSmarty();
		$smarty->assign(array("list"=>$list));
		$smarty->display('home/goods_show_shop.html');	
	}	
	//显示一个产品基本信息
	public function goods_show_one(){
		$id	 = $this->_REQUEST("id");
		$sql = "select g.*,s.shop_name,s.mobile from fly_goods as g left join fly_shop as s on g.shop_id=s.shop_id where g.goods_id='$id'";
		$one = $this->C($this->cacheDir)->findOne($sql);	
		$one['imglist']=$this->goods_img->goods_img_list($id);
		$smarty 	= $this->setSmarty();
		$smarty->assign(array("one"=>$one));
		$smarty->display('home/goods_show_one.html');	
	}

	public function goods_add(){
		$shop_id= $this->_REQUEST("shop_id");
		if(empty($_POST)){
			$shop_type	=$this->L('home/WxShopType')->shop_type();
			$smarty = $this->setSmarty();
			$smarty->assign(array('shop_type'=>$shop_type,'shop_id'=>$shop_id));
			$smarty->display('home/goods_add.html');	
		}else{
			$rtn=$this->goods_add_save();
			if($rtn>=0){
				 $rtn_msg=array('code'=>'sucess','message'=>'修改成功');
			}else{
				 $rtn_msg=array('code'=>'fail','message'=>'修改失败');
			}	
			echo json_encode($rtn_msg);					
		}
	}	
	public function goods_add_save(){
		$member		 =$this->L('home/WxMember')->member_get_info();
		$member_id  =$member['id'];
		
		$name	 	= $this->_REQUEST("name");
		$shop_id	= $this->_REQUEST("shop_id");
		$type_id	= $this->_REQUEST("type_id");
		$price		= $this->_REQUEST("price");
		$stock		= $this->_REQUEST("stock");
		$intro	 	= $this->_REQUEST("intro");
		$imgs	   = $this->_REQUEST("imgs");
		if(!empty($imgs)){
			@$imgs_arr=explode(',',$imgs);
			@$img_1="/Apply/View/templates/home/webuploader/upload/".$imgs_arr[0];
			@$img_2="/Apply/View/templates/home/webuploader/upload/".$imgs_arr[1];
			@$img_3="/Apply/View/templates/home/webuploader/upload/".$imgs_arr[2];
		}else{
			$img_1='';
			$img_2='';
			$img_3='';
		}
		$status	 =1;
		$dt	 = date("Y-m-d H:i:s",time());
		$sql = "insert into fly_goods(name,shop_id,type_id,member_id,price,stock,intro,img1,img2,img3,adt) 
							values('$name','$shop_id','$type_id','$member_id','$price','$stock','$intro','$img_1','$img_2','$img_3','$dt');";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>0){
			return $rtn;
		}else{
			return 0;
		}
	}
	
	public function goods_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$shop_type	=$this->L('home/WxShopType')->shop_type();
			
			$sql 		= "select * from fly_goods where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"shop_type"=>$shop_type));
			$smarty->display('home/goods_modify.html');	
		}else{//更新保存数据
			$id	 		= $this->_REQUEST("id");
			$name	 	= $this->_REQUEST("name");
			$type_id	= $this->_REQUEST("type_id");
			$intro	 	= $this->_REQUEST("intro");
			$price		= $this->_REQUEST("price");
			$stock		= $this->_REQUEST("stock");
			$sql= "update fly_goods set 
							name='$name',
							type_id='$type_id',
							intro='$intro',
							stock='$stock',
							price='$price'
			 		where id='$id'";
			$rtn=$this->C($this->cacheDir)->update($sql);	
			if($rtn>=0){
				 $rtn_msg=array('code'=>'sucess','message'=>'操作成功');
			}else{
				 $rtn_msg=array('code'=>'fail','message'=>'操作失败');
			}	
			echo json_encode($rtn_msg);		
		}
	}
	
	public function goods_status(){
		$rtn=array(
			"0"=>"待审核",
			"1"=>"已审核"
		);
		return $rtn;
	}
	
		
	public function goods_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_goods where id in ($id)";
		$rtn  = $this->C($this->cacheDir)->update($sql);	
		if($rtn>=0){
			 $rtn_msg=array('code'=>'sucess','message'=>'操作成功');
		}else{
			 $rtn_msg=array('code'=>'fail','message'=>'操作失败');
		}	
		echo json_encode($rtn_msg);			
	}

	//传入ID返回名字
	public function goods_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,account as name from fly_goods where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
	
	
	

	
	
}//
?>