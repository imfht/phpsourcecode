<?php
/*
 *
 * sysmanage.SysLog  系统日志管理   
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

 
class SysLog extends Action{	
	
	private $cacheDir='';//缓存目录
	
	public function __construct() {
		 _instance('Action/sysmanage/Auth');
	}

	//获取操作日志
	public function sys_log(){
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize= $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum)?1:$pageNum;
        $pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
        //**************************************************************************

		//用户查询参数
		$keywords= $this->_REQUEST("keywords");

		$where 		  = " 1 ";
		if(!empty($keywords)){
			$where .= " and (content like '%$keywords%' or editor like '%$keywords%'  or ipaddr like '%$keywords%' )";
		}
		$countSql	= "select * from fly_sys_log where $where";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
        $beginRecord = ($pageNum-1)*$pageSize;
		$sql		= "select * from fly_sys_log  where $where order by id desc limit $beginRecord,$pageSize";
		$list		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循环
		$assignArray=array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
		return $assignArray;
	}

    public function sys_log_show_json(){
        $list	= $this->sys_log();
        echo json_encode($list);
    }

	//调用显示
	public function sys_log_show(){
		$list	= $this->sys_log();
		$smarty = $this->setSmarty();
		$smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('sysmanage/sys_log_show.html');
	}
	
	public function sys_log_add($title,$info,$editor=null){
		if(empty($editor)) $editor  = SYS_USER_ACC;
		$ip		 = $this->L('Common')->get_client_ip();
		if(is_array($info)){
		    $info=array2string($info);
        }
		$data=array(
		    "ipaddr"=>$ip,
		    "title"=>$title,
		    "content"=>$info,
		    "editor"=>$editor,
		    "create_time"=>date("Y-m-d H:i:s",time()),
        );
        $rtn=$this->C($this->cacheDir)->insert('fly_sys_log',$data);
		if($rtn){
			return false;
		}else{
			return true;
		}
	}

	//删除选中记录
	public function sys_log_del (){
		$id	  = $this->_REQUEST("id");
		$sql="delete from fly_sys_log where id in (".$id.");";
		if($this->C($this->cacheDir)->update($sql)>=0){
			$this->L("Common")->ajax_json_success("操作成功");
		}	
	}	
	
	//删除全部记录
	public function sys_log_del_all (){
		$sql="delete from fly_sys_log";							 
		if($this->C($this->cacheDir)->update($sql)>=0){
			$this->L("Common")->ajax_json_success("操作成功");
		}	
	}

}//end class
?>