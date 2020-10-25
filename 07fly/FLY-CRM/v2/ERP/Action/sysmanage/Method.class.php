<?php 
/*
 *
 * sysmanage.Method  后台菜单功能模块   
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

class Method extends Action{	
	
	private $cacheDir='';//缓存目录
	private $auth;
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
	}	
	
	public function method(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		$where_str  = " id != 0";

		$menu_id = $this->_REQUEST("menu_id");
		$keywords = $this->_REQUEST("keywords");
		
		if($menu_id){
			$where_str .=" and menuID='$menu_id'";
		}
		if($keywords){
			$where_str .=" and (name like '%$keywords%' or value like '%$keywords%' )";
		}
		
		//排序生成
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_price' ){
			$order_by .=" price $orderDirection";
		}else if( $orderField=='by_stock' ){
			$order_by .=" stock $orderDirection";
		}else{
			$order_by .=" id asc";
		}
		
		$countSql  = "select id from fly_sys_method where  $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		
		$sql		 = "select * from fly_sys_method where $where_str $order_by limit $beginRecord,$pageSize";
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
		return $assignArray;
	}
	public function method_json(){
		$assArr  = $this->method();
		echo json_encode($assArr);
	}	
	public function method_show(){
		$assArr  = $this->method();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('sysmanage/method_show.html');	
	}		
	
	public function method_add(){
		$menu_id = $this->_REQUEST("menu_id");
		if(empty($_POST)){
			$smarty     = $this->setSmarty();
			$smarty->assign(array("menu_id"=>$menu_id));
			$smarty->display('sysmanage/method_add.html');	
		}else{
			$into_date=array(
				'menuID'=>$menu_id,
				'name'=>$this->_REQUEST("name"),
				'value'=>$this->_REQUEST("value"),
				'sort'=>$this->_REQUEST("sort"),
				'visible'=>$this->_REQUEST("visible"),
			);
			$this->C( $this->cacheDir )->modify('fly_sys_method',$into_date);
			$this->L("Common")->ajax_json_success("操作成功");
	
		}
	}		
	//删除
	public function method_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fly_sys_method where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/sysmanage/Method/method_show/");	
	}	
/*	public function method_select_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fly_sys_method  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			$parentID  = "<select name=\"parentID\" >";
			$parentID .= "<option value='0' >添加一级分类</option>";
			$parentID .= $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}*/
	//转换在以栏目ID为关键字的二维数组
	public function method_arr(){
		$rtArr  =array();
		$sql	="select id,menuID,name,value from fly_sys_method order by sort asc,id desc ";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["menuID"]][$row["value"]]=$row["name"];
			}
		}
		return $rtArr;
	}	
	//转换在以栏目ID为关键字的二维数组
	public function method_arr_checkbox($menuID,$role_method){
		$method=$this->method_arr();
		$checkbox='';
		if(array_key_exists($menuID,$method)){
			foreach($method[$menuID] as $key=>$value){
				$checked=in_array($key,$role_method)?"checked":"";
				$checkbox .="<input type='checkbox' name='methodID[]' class='method_checkbox' value='".$key."' ".$checked."> ".$value." ";
			}
		}
		return $checkbox;
	}

    /**返回需要验证的方法
     * @return array
     * Author: lingqifei created by at 2020/4/3 0003
     */
    public function method_auth_list(){
		$rtArr  = array();
		$sql	= "select value from fly_sys_method order by sort asc,id desc ";
		$list	= $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$v){
			$rtArr[]=$v["value"];
		}
		return $rtArr;
	}		

	//是否启用
	public function method_modify_visible() {
		$id		=$this->_REQUEST('id');	
		$visible=$this->_REQUEST('visible');	
		$upt_data=array(
					'visible'=>$this->_REQUEST( "visible" )
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_method',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	//是否名称
	public function method_modify_name() {
		$id		=$this->_REQUEST('id');	
		$name	=$this->_REQUEST('name');	
		$upt_data=array(
					'name'=>$this->_REQUEST( "name" )
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_method',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	//参数值
	public function method_modify_value() {
		$id		=$this->_REQUEST('id');	
		$value	=$this->_REQUEST('value');	
		$upt_data=array(
					'value'=>$this->_REQUEST( "value" )
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_method',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	//排序
	public function method_modify_sort() {
		$id		=$this->_REQUEST('id');	
		$sort=$this->_REQUEST('sort');	
		$upt_data=array(
					'sort'=>$this->_REQUEST( "sort" )
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_method',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	
}//
?>