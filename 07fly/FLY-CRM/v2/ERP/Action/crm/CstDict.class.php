<?php 
 /*
 *
 * erp.CstDict  企业字典管理   
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
class CstDict extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->dict_type=_instance('Action/crm/CstDictType');
	}	
	//数据查询
	public function cst_dict(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   	  = $this->_REQUEST("name");
		$typetag	   = $this->_REQUEST("typetag");
		$where_str 	   = " d.dict_id>'0' ";	
		if( !empty($name) ){
			$where_str .=" and d.name like '%$name%'";
		}
		if( !empty($typetag) ){
			$where_str .=" and d.typetag='$typetag'";
		}
			
		$countSql    = "select * from cst_dict as d left join cst_dict_type as t on d.typetag=t.typetag
							where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select d.*,t.typename from cst_dict as d left join cst_dict_type as t on d.typetag=t.typetag
						where $where_str order by d.sort asc limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);		
		return $assignArray;
	}
	public function cst_dict_json(){
		$assArr  = $this->cst_dict();
		echo json_encode($assArr);
	}		
	//返回一个二维数组
	//指定类型返回指定类型
	//返回所有数据字典
	public function cst_dict_list($typetag=null){
		$rtArr	=array();
		$where	=(!empty($typetag))?" where typetag='$typetag'":"";
		$sql	="select dict_id,name from cst_dict {$where}";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	public function cst_dict_show(){
			$dict_type= $this->dict_type->cst_dict_type();
			$smarty  = $this->setSmarty();
			$smarty->assign(array('typelist'=>$dict_type['list']));
			$smarty->display('crm/cst_dict_show.html');	
	}		

	public function cst_dict_add(){
		if(empty($_POST)){
			$dict_type= $this->dict_type->cst_dict_type();
			$smarty     = $this->setSmarty();
			$smarty->assign(array('typelist'=>$dict_type['list']));
			$smarty->display('crm/cst_dict_add.html');	
		}else{
			$typetag = $this->_REQUEST("typetag");
			$into_data=array(
						'name'=>$this->_REQUEST("name"),
						'typetag'=>$this->_REQUEST("typetag"),
						'sort'=>$this->_REQUEST("sort"),
						'visible'=>$this->_REQUEST("visible"),
					);
			$this->C($this->cacheDir)->insert('cst_dict',$into_data);	
			$this->location("操作成功","/crm/CstDict/cst_dict_show/");		
		}
	}		
	public function cst_dict_modify(){
		$dict_id = $this->_REQUEST("dict_id");
		if(empty($_POST)){
			$sql 		= "select * from cst_dict where dict_id='$dict_id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$dict_type	= $this->dict_type->cst_dict_type();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,'typelist'=>$dict_type['list']));
			$smarty->display('crm/cst_dict_modify.html');	
		}else{
			$upt_data=array(
						'name'=>$this->_REQUEST("name"),
						'typetag'=>$this->_REQUEST("typetag"),
						'sort'=>$this->_REQUEST("sort"),
						'visible'=>$this->_REQUEST("visible"),
					);
			$this->C($this->cacheDir)->modify('cst_dict',$upt_data,"dict_id='$dict_id'");		
			$this->location("操作成功","/crm/CstDict/cst_dict_show/");			
		}
	}	
	//删除
	public function cst_dict_del(){		
		$dict_id = $this->_REQUEST("dict_id");
		$sql	= "delete from cst_dict where  dict_id in ($dict_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");		
	}	
	//根据类型返回数据
	public function cst_dict_select(){
		$type  = $this->_REQUEST("type");
		$sql	="select id,name from cst_dict where type='$type' order by sort asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}		
	
	//返回一个一维数组
	public function cst_dict_arr($typetag=null){
		$rtArr	=array();
		$where	=(!empty($typetag))?" where typetag='$typetag'":"";
		$sql	="select dict_id,name from cst_dict {$where}";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["dict_id"]]=$row["name"];
			}
		}
		return $rtArr;
	}

	//返回字典名称
	public function cst_dict_get_name($dict_id){
		if(empty($dict_id)) $dict_id=0;
		$sql ="select dict_id,name from cst_dict where dict_id in ($dict_id)";	
		$one =$this->C($this->cacheDir)->findOne($sql);
		if($one){
			return $one['name'];
		}else{
			return '';
		}
	}	
	//是否启用
	public function cst_dict_modify_visible() {
		$dict_id =$this->_REQUEST('dict_id');	
		$upt_data=array(
					'visible'=>$this->_REQUEST( "visible" )
				 );
		$this->C( $this->cacheDir )->modify('cst_dict',$upt_data,"dict_id='$dict_id'",true);
		$this->L("Common")->ajax_json_success("操作成功");
	}	
	//更排序
	public function cst_dict_modify_sort() {
		$dict_id =$this->_REQUEST('dict_id');	
		$upt_data=array(
					'sort'=>$this->_REQUEST( "sort" )
				 );
		$this->C( $this->cacheDir )->modify('cst_dict',$upt_data,"dict_id='$dict_id'",true);
		$this->L("Common")->ajax_json_success("操作成功");
	}
	//更改名称
	public function cst_dict_modify_name() {
		$dict_id =$this->_REQUEST('dict_id');	
		$upt_data=array(
					'name'=>$this->_REQUEST( "name" )
				 );
		$this->C( $this->cacheDir )->modify('cst_dict',$upt_data,"dict_id='$dict_id'",true);
		$this->L("Common")->ajax_json_success("操作成功");
	}
}//end class
?>