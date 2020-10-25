<?php
/*
 *
 * sysmanage.Postion  职位管理   
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

class Position extends Action{	
	private $cacheDir='';//缓存目录
	private $auth;
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
	}	
	
	//获取数据
	public function position(){
		$sql	= "select * from fly_sys_position order by sort asc;";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		return $list;
	}

    public function position_json() {
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize= $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum)?1:$pageNum;
        $pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
        //**************************************************************************

        //**获得传送来的数据做条件来查询
        $keywords  = $this->_REQUEST("keywords");
        $pid   	        = $this->_REQUEST("pid");
        $pid_son=$this->get_position_self_son($pid);
        $pid_txt=implode(",",$pid_son);

        $where_str 	   = " id>'0' ";
        if( !empty($keywords) ){
            $where_str .=" and name like '%$keywords%'";
        }
        if( !empty($pid) ){
            $where_str .=" and parentID in ($pid_txt)";
        }
        $countSql    = "select *  from fly_sys_position where  $where_str order by sort asc;";
        $totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
        $beginRecord= ($pageNum-1)*$pageSize;//计算开始行数

        $sql		 = "SELECT *  FROM fly_sys_position WHERE  $where_str  order by sort asc limit $beginRecord,$pageSize";
        $list		 = $this->C($this->cacheDir)->findAll($sql);
        $assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
        echo json_encode($assignArray);
    }

    public function position_tree_json() {
        $list=$this->position();
        $tree=list2tree($list,0,0,'id','parentID','name');
        echo json_encode($tree);
    }

	public function position_show() {
		$smarty = $this->setSmarty();
		$smarty->display( 'sysmanage/position_show.html' );
	}
	
	//添加
	public function position_add(){
		if(empty($_POST)){
			$pid=$this->_REQUEST('pid');
			$position_list	=$this->position_select_tree();
			$smarty     = $this->setSmarty();
			$smarty->assign(array("position_list"=>$position_list));
			$smarty->display('sysmanage/position_add.html');	
		}else{
			$sql= "insert into fly_sys_position(name,parentID,sort,visible,intro) 
								values('$_POST[name]','$_POST[parentID]','$_POST[sort]','$_POST[visible]','$_POST[intro]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}	
	//修改
	public function position_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_position where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);
            $position_list	= $this->position_select_tree();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"position_list"=>$position_list));
			$smarty->display('sysmanage/position_modify.html');	
		}else{
            $pid=$this->_REQUEST( "parentID" );
            if($pid==$id){
                $this->L("Common")->ajax_json_error("父级栏目不能选择自己");
                return false;
                exit;
            }
			$sql= "update fly_sys_position set name='$_POST[name]',
											 parentID='$_POST[parentID]',sort='$_POST[sort]',
											 visible='$_POST[visible]',intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}	
	//删除
	public function position_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fly_sys_position where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}

    /**树形下拉
     * @return array|string
     * Author: lingqifei created by at 2020/4/3 0003
     */
    public function position_select_tree()
    {
        $sql = "select * from fly_sys_position order by sort asc;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $listselect=list2select($list,0,0,'id','parentID','name');
        return $listselect;
    }

	
	public function position_arr(){
		$rtArr  =array();
		$sql	="select id,name from fly_sys_position";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}
	//排序
	public function position_modify_sort() {
		$id		=$this->_REQUEST('id');	
		$sort	=$this->_REQUEST('sort');	
		$upt_data=array(
					'sort'=>$sort
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_position',$upt_data,"id='$id'",true);
        $this->L("Common")->ajax_json_success("操作成功");
    }
	//修改名称
	public function position_modify_name() {
		$id		=$this->_REQUEST('id');	
		$value	=$this->_REQUEST('value');
		$upt_data=array(
					'visible'=>$this->_REQUEST( "value" )
				 );
		$this->C( $this->cacheDir )->modify('fly_sys_position',$upt_data,"id='$id'",true);
        $this->L("Common")->ajax_json_success("操作成功");
    }
	//得到传入ID的子类
	public function position_get_child($pid=2){
		$data =$this->position();
		$tree =$this->L('Tree',$data);
		$child=$tree->get_child($pid);
		return $child;
	}
    /**获得所有指定id所有父级
     * @param int $positionid
     * @param array $data
     * @return array
     */
    public function get_position_all_pid($positionid=0, $data=[])
    {
        $sql	= "select *  from fly_sys_position where parentID='$positionid' order by sort asc;";
        $info = $this->C( $this->cacheDir )->findOne( $sql );
        if(!empty($info) && $info['parentID']){
            $data[]=$info['parentID'];
            return $this->get_position_all_pid($info['parentID'],$data);
        }
        return $data;
    }

    /**获得所有指定id所有子级
     * @param int $positionid
     * @param array $data
     * @return array
     */
    public function get_position_all_son($positionid=0, $data=[])
    {

        $sql	= "select *  from fly_sys_position where parentID='$positionid' order by sort asc;";
        $sons = $this->C( $this->cacheDir )->findAll( $sql );
        if (count($sons) > 0) {
            foreach ($sons as $v) {
                $data[] = $v['id'];
                $data = $this->get_position_all_son($v['id'], $data); //注意写$data 返回给上级
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
    public  function get_position_self_son($id){
        $sons=$this->get_position_all_son($id);
        $sons[]=$id;
        return $sons;
    }
}//
?>