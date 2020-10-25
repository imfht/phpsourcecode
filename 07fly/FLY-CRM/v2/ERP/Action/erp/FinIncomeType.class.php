<?php
/*
 * erp.FinIncomeType 其它费用收入分类管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinIncomeType extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
	}	
	function fin_income_type() {
		$sql	= "select * from fin_income_type order by sort asc;";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		return $list;
	}	
	//得到数形参数
	function getTree( $data, $pId=0,$level=0) {
		$tree = '';
		foreach ( $data as $k => $v ) {
			if ( $v[ 'parentID' ] == $pId ) { //父亲找到儿子
				$v[ 'children' ] = $this->getTree( $data, $v[ 'id' ], $level + 1);
				$v[ 'level' ] =  $level + 1;
				$v[ 'treename' ] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level).'|--'.$v['name'];
				$tree[] = $v;
			}
		}
		return $tree;
	}
	//输出树形参数
	function getTreeHtml($tree) {
		$html = '';
		if(!empty($tree)){
			foreach ( $tree as $key=>$t ) {
				$kg="";
				for($x=1;$x<$t['level'];$x++) {
					$kg .="<i class='fly-fl'>|—</i>";
				}
				if ( $t[ 'children' ] == '' ) {
					$html .= "<li><div class='fly-row lines'>
									<i class='fly-fl'>&nbsp;</i>
									<div  class='fly-col-5'>".$kg."<input type='text' name='name[]'  data-id='".$t['id']."' value='".$t['name']."' class='form-control w150 treeName'/></div>

									<div  class='fly-col-2 fly-fr fly-tr'>
										<a class='single_operation' data-act='add' data-id='".$t['id']."'>增加下级</a> 
										<a class='single_operation' data-act='modify' data-id='".$t['id']."'>修改</a> 
										<a class='single_operation' data-act='del' data-id='".$t['id']."'>删除</a>
									</div>
									<div  class='fly-col-2  fly-fr fly-tr'><input type='text' name='sort[]'  data-id='".$t['id']."' value='".$t['sort']."' class='form-control w100 treeSort'/></div>
								</div>
							  </li>";
				} else {
					$html .= "<li><div class='fly-row lines'>
									<lable class='fly-col-1'>[+]</lable>
									<div  class='fly-col-5'>".$kg."<input type='text' name='name[]'  data-id='".$t['id']."' value='".$t['name']."' class='form-control w150 treeName'/></div>
									<div  class='fly-col-2  fly-fr fly-tr'>
										<a class='single_operation' data-act='add' data-id='".$t['id']."'>增加下级</a> 
										<a class='single_operation' data-act='modify' data-id='".$t['id']."'>修改</a> 
										<a class='single_operation' data-act='del' data-id='".$t['id']."'>删除</a>
									</div>
									<div class='fly-col-2  fly-fr fly-tr'><input type='text' name='sort[]'  data-id='".$t['id']."' value='".$t['sort']."' class='form-control w100 treeSort'/></div>
								</div>
								";
					$html .= $this->getTreeHtml( $t[ 'children' ] );
					$html .= "</li>";
				}
			}
		}
		return $html ? '<ul>' . $html . '</ul>': $html;
	}
	//输出树形参数
	function getTreeSelect($tree,$sid) {
		$html = '';	
		if(!empty($tree)){
			foreach ( $tree as $key=>$t ) {
				$selected=($t['id']==$sid)?"selected":"";
				if ( $t[ 'children' ] == '' ) {
					$html .="<option value='".$t['id']."' $selected>".$t['treename']."</option>";
				} else {
					$html .="<option value='".$t['id']."' $selected>".$t['treename']."</option>";
					$html .= $this->getTreeSelect( $t[ 'children' ],$sid);
				}
			}
		}
		return $html;
	}
	
	//输出树形参数
	function getTreeSelectHtml($optid,$sid=0) {
		$list =$this->fin_income_type();
		$tree =$this->getTree($list, 0);
		$html = "<select name='$optid' id='$optid' class=\"form-control\"><option value='0'>请选择分类</option>";	
		$html .=$this->getTreeSelect($tree,$sid);
		$html .="</select>";
		return $html;
	}	
	//浏览
	public function fin_income_type_show(){
		$list =$this->fin_income_type();
		$tree =$this->getTree($list, 0 );
		$treeHtml=$this->getTreeHtml($tree);
		$smarty = $this->setSmarty();
		$smarty->assign( array( "treeHtml" => $treeHtml) );
		$smarty->display( 'erp/fin_income_type_show.html' );
	}		
	
	//添加 
	public function fin_income_type_add(){
		if(empty($_POST)){
			$id	 	  = $this->_REQUEST("id");
			$parentID =$this->getTreeSelectHtml('parentID',$id);
			$smarty  = $this->setSmarty();
			$smarty->assign(array("parentID"=>$parentID));
			$smarty->display('erp/fin_income_type_add.html');	
		}else{
			$into_data=array(
				'name'=>$this->_REQUEST("name"),
				'parentID'=>$this->_REQUEST("parentID"),
				'sort'=>$this->_REQUEST("sort"),
				'visible'=>$this->_REQUEST("visible"),
				'intro'=>$this->_REQUEST("intro"),
			);
			$this->C($this->cacheDir)->insert('fin_income_type',$into_data);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}	
	
	//修改
	public function fin_income_type_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fin_income_type where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->getTreeSelectHtml('parentID',$one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('erp/fin_income_type_modify.html');	
		}else{
			$sql= "update fin_income_type set name='$_POST[name]',
											 parentID='$_POST[parentID]',sort='$_POST[sort]',
											 visible='$_POST[visible]',intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/FinIncomeType/fin_income_type_show/");			
		}
	}
	
	//删除
	public function fin_income_type_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fin_income_type where id in($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	
	//下拉选择
	public function fin_income_type_select_tree($optid,$sid =""){
		$list =$this->fin_income_type();
		$tree =$this->getTree($list, 0);
		$html = "<select name='$optid' id='$optid' class=\"form-control\"><option value='0'>请选择分类</option>";	
		$html .=$this->getTreeSelect($tree,$sid);
		$html .="</select>";
		return $html;
	}
	
	//排序
	public
	function fin_income_type_modify_sort() {
		$id		=$this->_REQUEST('id');	
		$sort	=$this->_REQUEST('sort');	
		$upt_data=array(
					'sort'=>$this->_REQUEST( "sort" )
				 );
		$this->C( $this->cacheDir )->modify('fin_income_type',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");		
	}
	//修改名称
	public
	function fin_income_type_modify_name() {
		$id		=$this->_REQUEST('id');	
		$name	=$this->_REQUEST('name');	
		$upt_data=array(
					'name'=>$this->_REQUEST( "name" )
				 );
		$this->C( $this->cacheDir )->modify('fin_income_type',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");		
	}		
}//
?>