<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 应用类
*/

defined('INPOP') or exit('Access Denied');

class app extends Model{

	//初始化
    public function __construct(){
		parent::__construct("apps", "appid");
    }


	//更新当前站点
	public function updateSessionDefaultSiteId($siteid){
		$siteid = (int)$siteid;
		if($siteid < 1) return false;
		$_SESSION['defaultsiteid'] = $siteid;
		return true;
	}
	
	//获取当前站点
	public function getSessionDefaultSiteId(){
		return $_SESSION['defaultsiteid'];
	}
	
	//获取用户的站点
	public function getUserSite(){
		//获取当前登录用户信息
		$userInfo = $_SESSION['user'];
		if(empty($userInfo)) return false;
		$siteids = $userInfo['siteids'];
		if(!$siteids) return false;
		$sql = " siteid in (".$siteids.") ";
		//获取用户创建的站点，先计数
		$count = $this->getCount($sql);
		if($count < 1) return false;
		$sites = $this->getList($sql, '', 0, $count);
		if(empty($sites)) return false;
		$_SESSION['sites'] = $sites;
		return $sites;
	}
	
	//获取所有信息
	public function getFullList($order = array(), $offset = 0, $pagesize = PAGE_SIZE){
		$return = array();
		//$_sort = new sortModel();
		$sites = $this->getList($sql, $orderby, $offset, $pagesize);
		$this_count = $this->getCount();
		$return['Total'] = $this_count;
		if(!empty($sites)){
			foreach($sites as $key=>$value){
				if($value['deleted'] == 1) continue;
				$sort_sql = " siteid = ".(int)$value['siteid']." ";
				/*
				$sorts = array();
				$sorts = $_sort->getList($sort_sql);
				if(!empty($sorts)){
					$zhufenlei = array();
					$fufenlei = array();
					foreach($sorts as $sort){
						if($sort['type'] == 1) $zhufenlei[] = $sort['name'];
						if($sort['type'] == 2) $fufenlei[] = $sort['name'];
					}
					$value['zhufenlei'] = $zhufenlei;
					$value['fufenlei'] = $fufenlei;
				}
				*/
				$return['Rows'][] = $value;
			}
		}
		return $return;
	}
	
	//获取完全信息
	public function getInfo($siteid){
		$siteid = (int)$siteid;
		if($siteid < 1) $siteid = $_SESSION['defaultsiteid'];
		if(!$siteid) return false;
		$return = $this->getOne($siteid);
		return $return;
	}
	
	//删除站点，只是逻辑删除，不物理删除
	public function deleteThis($siteids){
		if(!$siteids) return false;
		$sql = "update ".$this->table." set deleted = 1 where siteid in (".$siteids.");";
		$done = $this->doSQL($sql, false);
		return $done;
	}
	
	//添加站点同时添加默认栏目
	public function addNew($_add){
		if(!$_add['name']) return false;
		$done = $this->add($_add);
		$thissiteid = $this->keyId;
		$this->addDefaultNode($thissiteid);
		return $done;
	}
	
	//添加站点默认栏目
	public function addDefaultNode($siteid){
		if(!$siteid) return false;
		$siteid = (int)$siteid;
		$thissite = $this->getOne($siteid);
		if(empty($thissite)) return false;
		$add_array = array();
		$_node = new nodeModel();
		$sql = " siteid = ".$siteid;
		$node = $_node->getOneBy($sql);
		if(!$node['nodeid']){
			$name = "首页";
			$add_array['name'] = $thissite['name'].$name;
			$add_array['siteid'] = $siteid;
			$done = $_node->add($add_array);
		}else{
			$done = false;
		}
		return $done;
	}	
	
}
?>