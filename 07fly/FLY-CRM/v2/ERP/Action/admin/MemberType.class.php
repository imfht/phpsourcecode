<?php	 
/*
 *
 * admin.MemberType 会员分类管理   
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
class MemberType extends Action{	
	private $cacheDir='';//缓存目录
	private $member_type_dist='';//会员组层设置
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->member_type_dist=_instance('Action/admin/MemberTypeDist');
	}	
	
	public function member_type(){
		$sql		 = "select * from fly_member_type order by sort asc";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['type_dist_name']=$this->member_type_dist->member_type_dist_get_name($row['id']);
		}
		$assignArray = array('list'=>$list);	
		return $assignArray;
	}
	public function member_type_show(){
		$assArr	= $this->member_type();
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('admin/member_type_show.html');	
	}

	//增加
	public function member_type_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('admin/member_type_add.html');	
		}else{
			$typename	  = $this->_REQUEST("typename");
			$typedir	  = $this->_REQUEST("typedir");
			$typetag	  = $this->_REQUEST("typetag");
			$sort		  = $this->_REQUEST("sort");
			$seotitle	  = $this->_REQUEST("seotitle");
			$keywords	  = $this->_REQUEST("keywords");
			$intro		  = $this->_REQUEST("intro");
			
			//设置反点导数及比例
			$layers		  = $this->_REQUEST("layers");
			$rate		  = $this->_REQUEST("rate");
			$sql="insert into fly_member_type(typename,typedir,sort,typetag,seotitle,keywords,intro) 
								values('$typename','$typedir','$sort','$typetag','$seotitle','$keywords','$intro');";
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				$this->member_type_dist->member_type_dist_add($member_type_id=$rtn,$data=array('layers'=>$layers,'rate'=>$rate));
			}
			$this->location("操作成功","/admin/MemberType/member_type_show/");		
		}
	}		
	public function member_type_modify(){
		$id	  = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_member_type where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);
			$type_dist_list= $this->member_type_dist->member_type_dist($id);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"type_dist_list"=>$type_dist_list));
			$smarty->display('admin/member_type_modify.html');	
		}else{
			$typename	  = $this->_REQUEST("typename");
			$typedir	  = $this->_REQUEST("typedir");
			$typetag	  = $this->_REQUEST("typetag");
			$sort		  = $this->_REQUEST("sort");
			$seotitle	  = $this->_REQUEST("seotitle");
			$keywords	  = $this->_REQUEST("keywords");
			$intro		  = $this->_REQUEST("intro");
			$layers		  = $this->_REQUEST("layers");
			$rate		  = $this->_REQUEST("rate");
			$sql= "update fly_member_type set 
								typename='$typename',
								typedir='$typedir',
								typetag='$typetag',
								sort='$sort',
								seotitle='$seotitle',
								keywords='$keywords',
								intro='$intro'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);
			
			$this->member_type_dist->member_type_dist_add($member_type_id=$id,$data=array('layers'=>$layers,'rate'=>$rate));
			
			$this->location("操作成功","/admin/MemberType/member_type_show/");			
		}
	}	
	public function member_type_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_member_type where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->location("操作成功","/admin/MemberType/member_type_show/");	
	}	
	
	
	public function member_type_get_one($id){
		$sql = "select * from fly_member_type where id='$id'";	
		$pak = $this->C($this->cacheDir)->findOne($sql);	
		return $pak;
	}
	
	//分类下拉选择
	public function member_type_get_opt($inputname,$value=null){
		$sql = "select * from fly_member_type;";
		$list= $this->C($this->cacheDir)->findAll($sql);
		$string		="<select name='$inputname' id='$inputname'  class='combox'>";
		foreach($list as $key=>$row){
			$string.="<option value='$row[id]'";
			if($row["id"]==$value) $string.=" selected";
			$string.=">".$row["typename"]."</option>";
		}
		$string.="</select>";
		return $string;
	}	
	public function member_type_lookup(){
		$sql = "select id,typename from fly_member_type;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}
	//传入ID返回名字
	public function member_type_get_name($id){
		if(empty($id)) $id=0;
		$sql ="select id,typename from fly_member_type where id in ($id)";
		$list=$this->C($this->cacheDir)->findAll($sql);
		$str ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["typename"]."";
			}
		}
		return $str;
	}
}//
?>