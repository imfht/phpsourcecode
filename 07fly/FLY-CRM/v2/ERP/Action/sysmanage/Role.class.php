<?php
/*
 *
 * sysmanage.Role  角色管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------------------
 *
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	
class Role extends Action{	

	private $cacheDir='';//缓存目录
	private $auth;
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
		$this->menu=_instance('Action/sysmanage/Menu');
		$this->method=_instance('Action/sysmanage/Method');
	}	
	
	public function role(){
		$sql	= "select *,name as text,id as tags from fly_sys_role order by sort asc;";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		return $list;
	}

    public function role_json() {
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize= $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum)?1:$pageNum;
        $pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
        //**************************************************************************

        //**获得传送来的数据做条件来查询
        $keywords  = $this->_REQUEST("keywords");
        $pid   	        = $this->_REQUEST("pid");
        $pid_son=$this->get_role_self_son($pid);
        $pid_txt=implode(",",$pid_son);

        $where_str 	   = " id>'0' ";
        if( !empty($keywords) ){
            $where_str .=" and name like '%$keywords%'";
        }
        if( !empty($pid) ){
            $where_str .=" and parentID in ($pid_txt)";
        }
        $countSql    = "select *  from fly_sys_role where  $where_str order by sort asc;";
        $totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
        $beginRecord= ($pageNum-1)*$pageSize;//计算开始行数

        $sql		 = "SELECT *  FROM fly_sys_role WHERE  $where_str  order by sort asc limit $beginRecord,$pageSize";
        $list		 = $this->C($this->cacheDir)->findAll($sql);
        $assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
        echo json_encode($assignArray);
    }

    public function role_tree_json() {
        $list=$this->role();
        $tree=list2tree($list,0,0,'id','parentID','name');
        echo json_encode($tree);
    }
	/**
	 * [putCsv description]
	 * @param  string   $tree  		[description] 栏目的树形格式
	 * @param  array   $role      [description] 数组,当前角色的标签
	 * @return [type]           [description] 输出以为checkbox的html
	 */
	function getTreeChecked($tree,$role) {
		$html = '';
		$role_menu =explode(',',$role["SYS_MENU"]);
		$role_method =explode(',',$role["SYS_METHOD"]);
		foreach ( $tree as $t ) {
			$kg="";
			for($x=1;$x<$t['level'];$x++) {
				$kg .="<i class='fly-fl'>|—</i>";
			}
			$checked=in_array($t['id'],$role_menu)?"checked":"";
			
			//if ( $t[ 'children' ] == '' ) { //修改判断为空
			if ( empty($t[ 'nodes' ]) ) {
				$method=$this->method->method_arr_checkbox($t['id'],$role_method);
				$html .= "<li><div class='fly-row lines'>
								<i class='fly-fl'>&nbsp;</i>
								<div  class='fly-col-8'>
									".$kg."<input type='checkbox' name='menuID[]' value='".$t['id']."'  class='children_method' ".$checked."> ".$t['text']."&nbsp;&nbsp;&nbsp;".$method."
								</div>
							</div>
						  </li>";
			} else {
				$html .= "<li><div class='fly-row lines'>
								<lable class='fly-col-1'>[+]</lable>
								<div  class='fly-col-8'>".$kg."<input type='checkbox' name='menuID[]' value='".$t['id']."' class='children_menu' ".$checked."> ".$t['text']."</div>		
							</div>
							";
				$html .= $this->getTreeChecked( $t[ 'nodes' ] ,$role);
				$html .= "</li>";
			}
		}
		return $html ? '<ul>' . $html . '</ul>': $html;
	}

	
	//权限维护功能
	function role_check_power(){
		$id=$this->_REQUEST("id");
		$list =$this->menu->menu_check_list();
        $tree=list2tree($list,0,0,'id','parentID','name');
		$role = $this->role_get_one($id);
		$role =(!empty($role))?$role:array('SYS_MENU'=>'0','SYS_METHOD'=>'0',);
		$treeHtml=$this->getTreeChecked($tree,$role);
		$smarty = $this->setSmarty();
		$smarty->assign( array( "treeHtml" => $treeHtml,'id'=>$id) );
		$smarty->display( 'sysmanage/role_check_power.html' );
	}
	
	//权限保存
	function role_check_power_save(){
		$id=$this->_REQUEST("id");
		$menuID=$this->_REQUEST("menuID");
		$methodID=$this->_REQUEST("methodID");
		$menu_txt	= is_array($menuID)?implode(',',$menuID):0;
		$method_txt	= is_array($methodID)?implode(',',$methodID):0;
		$sql="delete from fly_sys_power where master='role' and master_value='$id'";
        $this->C($this->cacheDir)->update($sql);
		$into_menu_data=array(
			'master'=>'role',
			'master_value'=>$id,
			'access'=>'SYS_MENU',
			'access_value'=>$menu_txt,
		);
		$this->C($this->cacheDir)->insert('fly_sys_power',$into_menu_data);
		$into_method_data=array(
			'master'=>'role',
			'master_value'=>$id,
			'access'=>'SYS_METHOD',
			'access_value'=>$method_txt,
		);
		$this->C($this->cacheDir)->insert('fly_sys_power',$into_method_data);
		$this->L("Common")->ajax_json_success("操作成功");	
	}	

	//权限列表显示
	public function role_show() {
		$smarty = $this->setSmarty();
		$smarty->display( 'sysmanage/role_show.html' );
	}
	
	//增加
	public function role_add(){
		$id = $this->_REQUEST("id");
		if(empty($_POST)){
			$role_list	=$this->role_select_tree();
			$smarty		=$this->setSmarty();
			$smarty->assign(array("role_list"=>$role_list));
			$smarty->display('sysmanage/role_add.html');	
		}else{
			$name	 = $this->_REQUEST("name");
			$parentID= $this->_REQUEST("parentID");
			$sort	 = $this->_REQUEST("sort");
			$visible = $this->_REQUEST("visible");
			$intro	 = $this->_REQUEST("intro");
			
			$sql= "insert into fly_sys_role(name,parentID,sort,visible,intro) 
								values('$name','$parentID','$sort','$visible','$intro')";
			$this->C($this->cacheDir)->update($sql);
			$this->L("Common")->ajax_json_success("操作成功");
		}
	}
	
	//修改
	public function role_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_role where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);
            $role_list	=$this->role_select_tree();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"role_list"=>$role_list));
			$smarty->display('sysmanage/role_modify.html');	
		}else{
			$name	 = $this->_REQUEST("name");
			$parentID= $this->_REQUEST("parentID");
			$sort	 = $this->_REQUEST("sort");
			$visible = $this->_REQUEST("visible");
			$intro	 = $this->_REQUEST("intro");
            $pid=$this->_REQUEST( "parentID" );
            if($pid==$id){
                $this->L("Common")->ajax_json_error("父级栏目不能选择自己");
                return false;
                exit;
            }
			$sql="update fly_sys_role set name='$name',
											parentID='$parentID',
											sort='$sort',
											visible='$visible',
											intro='$intro'
				  where id='$id'";	
			$this->C($this->cacheDir)->update($sql);
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}	
	
	public function role_del(){
		$id=$this->_REQUEST("id");
		$sqlstr1 = "delete from fly_sys_role where id in ($id)";	
		$sqlstr2 = "delete from fly_sys_power where master_value in ($id) and master='role'";										
		$this->C($this->cacheDir)->begintrans();
		if($this->C($this->cacheDir)->update($sqlstr1)<0 || $this->C($this->cacheDir)->update($sqlstr2)<0 ){
			$this->C($this->cacheDir)->rollback();
		}
		$this->C($this->cacheDir)->commit();
		$this->L("Common")->ajax_json_success("操作成功");	
	}		

	//系统栏目和权限列表
	public function menu_power_check($id=null){
		$list 	   = $this->L("sysmanage/Menu")->menu_tree_arr();
		$method	   = $this->L("sysmanage/Method")->method_arr();
		$role_menu = array();
		$role_mod  = array();
		if($id){
			$result = $this->role_get_one($id);
			$role_menu =explode(',',$result["SYS_MENU"]); 
			$role_mod  =explode(',',$result["SYS_METHOD"]); 
		}
		$string  = "<table width=\"100%\"  border='0' cellpadding='5' cellspacing='0' class='table table-bordered'>";
		$string .= "<tr bgcolor='#FBF5C6' height='25'><td>栏目</td><td>菜单</td></tr>";
		$cnt	 = 1;
		if(is_array($list)){
			foreach($list as $key=>$row1){
				$ischeck1=in_array($row1["id"],$role_menu)?"checked":"";
				$string .="<tr><td width='10%'>".$row1["name"]."<input type='checkbox' name='menuID[]' value='".$row1["id"]."' $ischeck1 id='".$row1["id"]."' onclick='checkedStatus(this.id);'></td><td id='sub".$row1["id"]."'>" ;
					foreach($row1["parentID"] as $item_key=>$row2){
						$bgcolor =($cnt%2==0)?"#FBF5C6":"#F9F9F9";
						$ischeck2=in_array($row2["id"],$role_menu)?"checked":"";
						$string .= "<table width=\"100%\" cellpadding='5' cellspacing='0'><tr  bgcolor='$bgcolor'><td width='15%' style='padding-left:10px;'><input type='checkbox' name='menuID[]' value='".$row2["id"]."' $ischeck2 id='".$row2["id"]."'  onclick='checkedStatus(this.id);'> ".$row2["name"]."</td><td id='sub".$row2["id"]."'>";	
							foreach($row2["parentID"] as $item_key=>$row3){
								$ischeck3=in_array($row3["id"],$role_menu)?"checked":"";
								$string .= "<table  width=\"100%\" cellpadding='5' cellspacing='0'><tr><td width='20%' height='25'><input type='checkbox' name='menuID[]' value='".$row3["id"]."' $ischeck3 id='".$row3["id"]."'  onclick='checkedStatus(this.id);'> ".$row3["name"]."</td><td id='sub".$row3["id"]."' align=left>";	
									if( @is_array($method[$row3["id"]]) ){
										$string .= "<table cellpadding='5' cellspacing='0'><tr>";
										foreach( $method[$row3["id"]] as $mkey=>$mvalue){
											$ischeck4=in_array($mkey,$role_mod)?"checked":"";
											$string .= "<td width='100' height='25'><input type='checkbox' name='methodID[]' value='$mkey' $ischeck4 > $mvalue </td>";
										}
										$string .= "</td></tr></table>";	
									}
									
								$string .= "</td></tr></table>";			
							}							
						$cnt++;	
						$string .= "</td></tr></table>";		
					}
				$string .= "</td></tr>";			
			}
			$string .= "</table>";
		}
		return $string;	
	}

    /**获得一个角色的权限
     * @param $id
     * @return array
     * Author: lingqifei created by at 2020/4/4 0004
     */
    public function role_get_one($id){
		$power =array();
		$sql  = "select access,access_value from fly_sys_power where master='role' and master_value='$id' ";
		$list =$this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循环
		if(is_array($list)){
			foreach($list as $key=>$row){
				$power[$row['access']] = $row["access_value"];
			}
		}	
		return $power;	
	}

    /**树形下拉
     * @return array|string
     * Author: lingqifei created by at 2020/4/3 0003
     */
    public function role_select_tree()
    {
        $sql = "select * from fly_sys_role order by sort asc;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $listselect=list2select($list,0,0,'id','parentID','name');
        return $listselect;
    }

	//排序
	public function role_modify_sort() {
		$id		=$this->_REQUEST('id');	
		$sort	=$this->_REQUEST('sort');	
		$upt_data=array(
					'sort'=>$sort
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_role',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	//修改名称
	public function role_modify_name() {
		$id		=$this->_REQUEST('id');	
		$value	=$this->_REQUEST('value');
		$upt_data=array(
					'visible'=>$value
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_role',$upt_data,"id='$id'",true);
		$this->L("Common")->ajax_json_success("操作成功");	
	}

	//得到传入ID的子类
	public function role_get_child($pid=1){
		$data =$this->role();
		$tree =$this->L('Tree');
		$child=$tree->get_all_child($data,$pid);
		return $child;
	}

    /**获得所有指定id所有父级
     * @param int $roleid
     * @param array $data
     * @return array
     */
    public function get_role_all_pid($roleid=0, $data=[])
    {
        $sql	= "select *  from fly_sys_role where parentID='$roleid' order by sort asc;";
        $info = $this->C( $this->cacheDir )->findOne( $sql );
        if(!empty($info) && $info['parentID']){
            $data[]=$info['parentID'];
            return $this->get_role_all_pid($info['parentID'],$data);
        }
        return $data;
    }

    /**获得所有指定id所有子级
     * @param int $roleid
     * @param array $data
     * @return array
     */
    public function get_role_all_son($roleid=0, $data=[])
    {

        $sql	= "select *  from fly_sys_role where parentID='$roleid' order by sort asc;";
        $sons = $this->C( $this->cacheDir )->findAll( $sql );
        if (count($sons) > 0) {
            foreach ($sons as $v) {
                $data[] = $v['id'];
                $data = $this->get_role_all_son($v['id'], $data); //注意写$data 返回给上级
            }
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return false;
        }
        return $data;
    }
    /**得到自己的和子级
     * @param $id
     * @return array
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public  function get_role_self_son($id){
        $sons=$this->get_role_all_son($id);
        $sons[]=$id;
        return $sons;
    }
	
}//
?>