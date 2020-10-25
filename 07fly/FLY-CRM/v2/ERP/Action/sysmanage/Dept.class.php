<?php
/*
 *
 * sysmanage.Depth  部门管理   
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

class Dept extends Action {
	private $cacheDir = ''; //缓存目录
	private $auth;
	public function __construct() {
		$this->auth = _instance( 'Action/sysmanage/Auth' );
	}
	public function dept() {
		$sql	= "select *  from fly_sys_dept order by sort asc;";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		return $list;
	}

    public function dept_json() {
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize= $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum)?1:$pageNum;
        $pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
        //**************************************************************************

        //**获得传送来的数据做条件来查询
        $keywords  = $this->_REQUEST("keywords");
        $pid   	        = $this->_REQUEST("pid");
        $pid_son=$this->get_dept_self_son($pid);
        $pid_txt=implode(",",$pid_son);

        $where_str 	   = " id>'0' ";
        if( !empty($keywords) ){
            $where_str .=" and name like '%$keywords%'";
        }
        if( !empty($pid) ){
            $where_str .=" and parentID in ($pid_txt)";
        }
        $countSql    = "select *  from fly_sys_dept where  $where_str order by sort asc;";
        $totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
        $beginRecord= ($pageNum-1)*$pageSize;//计算开始行数

        $sql		 = "SELECT *  FROM fly_sys_dept WHERE  $where_str  order by sort asc limit $beginRecord,$pageSize";
        $list		 = $this->C($this->cacheDir)->findAll($sql);
        $assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
        echo json_encode($assignArray);
    }

    public function dept_tree_json() {
        $list=$this->dept();
        $tree=list2tree($list,0,0,'id','parentID','name');
        echo json_encode($tree);
    }

	
	public function dept_show() {
		$smarty = $this->setSmarty();
		$smarty->display( 'sysmanage/dept_show.html' );
	}

	public function dept_add() {
		if ( empty( $_POST ) ) {
			$pid=$this->_REQUEST('id');
            $dept_list = $this->dept_select_tree();
			$smarty = $this->setSmarty();
			$smarty->assign( array( "dept_list" => $dept_list ) );
			$smarty->display( 'sysmanage/dept_add.html' );
		} else {
			$data=array(
					'name'=>$this->_REQUEST( "name" ),
					'tel'=>$this->_REQUEST( "tel" ),
					'fax'=>$this->_REQUEST( "fax" ),
					'parentID'=>$this->_REQUEST( "parentID" ),
					'sort'=>$this->_REQUEST( "sort" ),
					'visible'=>$this->_REQUEST( "visible" ),
					'intro'=>$this->_REQUEST( "intro" )
					   );
			$this->C( $this->cacheDir )->insert('fly_sys_dept',$data );
			$this->L("Common")->ajax_json_success("操作成功");	
		}
	}
	
	public function dept_modify() {
		$id = $this->_REQUEST( "id" );
		if ( empty( $_POST ) ) {
			$sql = "select * from fly_sys_dept where id='$id'";
			$one = $this->C( $this->cacheDir )->findOne( $sql );
			$dept_list = $this->dept_select_tree();
			$smarty = $this->setSmarty();
			$smarty->assign( array( "one" => $one, "dept_list" => $dept_list ) ); //框架变量注入同样适用于smarty的assign方法
			$smarty->display( 'sysmanage/dept_modify.html' );
		} else {

		    $pid=$this->_REQUEST( "parentID" );
		    if($pid==$id){
                $this->L("Common")->ajax_json_error("父级栏目不能选择自己");
                return false;
                exit;
            }

            $data=array(
                'name'=>$this->_REQUEST( "name" ),
                'tel'=>$this->_REQUEST( "tel" ),
                'fax'=>$this->_REQUEST( "fax" ),
                'parentID'=>$this->_REQUEST( "parentID" ),
                'sort'=>$this->_REQUEST( "sort" ),
                'visible'=>$this->_REQUEST( "visible" ),
                'intro'=>$this->_REQUEST( "intro" )
            );
			$this->C( $this->cacheDir )->modify('fly_sys_dept', $data,"id='$id'" );
			$this->L("Common")->ajax_json_success("操作成功");	
		}
	}
	
	public function dept_del() {
		$id = $this->_REQUEST( "id" );
		$sql = "delete from fly_sys_dept where id='$id'";
		$this->C( $this->cacheDir )->update( $sql );
        $this->L("Common")->ajax_json_success("操作成功");
	}

    /**树形下拉
     * @return array|string
     * Author: lingqifei created by at 2020/4/3 0003
     */
    public function dept_select_tree()
    {
        $sql = "select * from fly_sys_dept order by sort asc;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $listselect=list2select($list,0,0,'id','parentID','name');
        return $listselect;
    }
	//得到一个部门的得到下面子部门的编号
	public function dept_get_sub_dept( $deptID ) {
		$sql = "select id,name,parentID from fly_sys_dept where parentID='$deptID'";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		if ( !empty( $list ) ) {
			$dept_ids = $deptID . ",";
		} else {
			$dept_ids = $deptID;
		}
		foreach ( $list as $key => $row ) {
			$dept_ids .= $this->dept_get_sub_dept( $row[ "id" ] );
		}
		return $dept_ids;
	}
	//排序
	public function dept_modify_sort() {
		$id		=$this->_REQUEST('id');	
		$sort	=$this->_REQUEST('sort');	
		$upt_data=array('sort'=>$sort);
		$this->C( $this->cacheDir )->modify('fly_sys_dept',$upt_data,"id='$id'",true);
        $this->L("Common")->ajax_json_success("操作成功");
    }
	//修改名称
	public function dept_modify_visible() {
		$id		=$this->_REQUEST('id');	
		$visible =$this->_REQUEST('value');
		$upt_data=array( 'visible'=>$visible);
		$this->C( $this->cacheDir )->modify('fly_sys_dept',$upt_data,"id='$id'",true);
        $this->L("Common")->ajax_json_success("操作成功");
	}
	
	//得到传入ID的子类
	public function dept_get_child($pid=1){
		$data =$this->dept();
		$tree =$this->L('Tree');
		$child=$tree->get_all_child($data,$pid);
		return $child;
	}

    /**获得所有指定id所有父级
     * @param int $deptid
     * @param array $data
     * @return array
     */
    public function get_dept_all_pid($deptid=0, $data=[])
    {
        $sql	= "select *  from fly_sys_dept where parentID='$deptid' order by sort asc;";
        $info = $this->C( $this->cacheDir )->findOne( $sql );
        if(!empty($info) && $info['parentID']){
            $data[]=$info['parentID'];
            return $this->get_dept_all_pid($info['parentID'],$data);
        }
        return $data;
    }

    /**获得所有指定id所有子级
     * @param int $deptid
     * @param array $data
     * @return array
     */
    public function get_dept_all_son($deptid=0, $data=[])
    {

        $sql	= "select *  from fly_sys_dept where parentID='$deptid' order by sort asc;";
        $sons = $this->C( $this->cacheDir )->findAll( $sql );
        if (count($sons) > 0) {
            foreach ($sons as $v) {
                $data[] = $v['id'];
                $data = $this->get_dept_all_son($v['id'], $data); //注意写$data 返回给上级
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
    public  function get_dept_self_son($id){
        $sons=$this->get_dept_all_son($id);
        $sons[]=$id;
        return $sons;
    }

} //
?>