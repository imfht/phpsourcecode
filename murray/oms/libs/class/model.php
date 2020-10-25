<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 模型类
*/

defined('INPOP') or exit('Access Denied');

abstract class Model{

	public $data; //定义数据储存
	public $table; //定义数据表
	public $db; //定义数据连接
	public $keyId; //定义主键id
	public $keyField; //定义主字段
	public $cache; //定义缓存实例
	public $cacheType; //定义缓存类型，file为文件缓存，memcache为MEMCACHE缓存
	public $tagArray; //定义标签

	//必须继承
	public function __construct($table, $fieldName){
		if(!$table || !$fieldName) return false;
		$this->db = DB::getInstance();
		$this->table = getTable($table);
		$this->cache = Base::Create('cache');  
		//检测memcache，并实例化
		if(defined('MEMCACHE_HOST')){
			$this->cacheType = "memcache";
		}else{
			$this->cacheType = "file";
		}
		//由于在SAE中，只能用MEMCACHE
		if(defined('IN_SAE')) $this->cacheType = "memcache";
		$this->keyField = $fieldName;
		$this->tagArray = array();		
	}

	//销毁
	public function __destruct(){
		unset($this->db);
		unset($this->table);
	}
	
	public function __set($name, $value){
		$this->data[$name] = $value;
	}
	
	public function __get($name){
		return $this->data[$name];
	}
	
	//添加
	public function add($data = ''){
		$csql1 = $csql2 = $cs = "";
		$fsql1 = $fsql2 = $fs = "";
		$data = $data ? $data : $this->data;
		foreach($data as $key=>$value){
			$csql1 .= $cs."`".$key."`";
			$csql2 .= $cs."'".$value."'";
			$cs = ",";
		}
		$isdone = $this->db->query("INSERT INTO ".$this->table." ($csql1) VALUES($csql2) ;");
		if($isdone) $this->keyId = $this->db->insert_id();
		$cacheData = $this->getOne($keyId);
		$this->cache($keyId, $cacheData);
		return $isdone;
	}

	//修改
	public function edit($data = ''){
		if(!$this->keyId) return false;
		$sql = $s = "";
		$fsql = $fs = "";
		$data = $data ? $data : $this->data;
		foreach($data as $key=>$value){
			$sql .= $s."`".$key."`"."='".$value."'";
			$s = ",";
		}
		$isdone = $this->db->query("UPDATE ".$this->table." SET ".$sql." WHERE ".$this->keyField." = '".$this->keyId."' ;");
		$cacheData = $this->getOne($keyId , false);
		$this->cache($keyId, $cacheData);
		return $isdone;
	}

	//修改
	public function editBy($data = '', $where = ''){
		if(!$where) return false;
		$this_one = $this->db->get_one("SELECT ".$this->keyField." FROM ".$this->table." WHERE ".$where." ;");
		if(empty($this_one)) return false;
		$this->keyId = $this_one[$this->keyField];
		$sql = $s = "";
		$fsql = $fs = "";
		$data = $data ? $data : $this->data;
		foreach($data as $key=>$value){
			$sql .= $s."`".$key."`"."='".$value."'";
			$s = ",";
		}
		$isdone = $this->db->query("UPDATE ".$this->table." SET ".$sql." WHERE ".$where." ;");
		$cacheData = $this->getOne($keyId, false);
		$this->cache($keyId, $cacheData);
		return $isdone;
	}
	
	//删除
	public function delete($ids){
		$ids = is_array($ids) ? implode(',',$ids) : $ids;
		if($ids) $sql = " ".$this->keyField." IN( ".$ids." )";
		$isdone = $this->db->query("DELETE FROM ".$this->table." WHERE $sql ");
		return $isdone;
	}
	
	//获取列表
	public function getList($sql = '', $order = '', $offset = 0, $pagesize = PAGE_SIZE){
		$sql = $sql ? " WHERE ".$sql : "";
		$order = $order ? " ".$order.", " : "";
		$result = $this->db->query("SELECT * FROM ".$this->table." $sql ORDER BY $order ".$this->keyField." DESC LIMIT ".$offset.",".$pagesize." ;");
		$return = array();
		while($r = $this->db->fetch_array($result)){
			$return[$r[$this->keyField]] = $r;
		}
		$this->db->free_result($result);
		$return = !empty($return) ? $return : array();
		return $return;
	}

	//获取内容
	public function getOne($keyId = 0, $fromcache = true){
		$keyId = $keyId ? $keyId : $this->keyId;
		$data = $this->cache($keyId);
		if(empty($data) || !$fromcache){
			$return = $this->db->get_one("SELECT * FROM ".$this->table." WHERE ".$this->keyField." = '".$keyId."' limit 0,1;");
			$this->cache($keyId, $return);
		}else{
			$return = $data;
		}
		return $return;
	}

	//通过SQL获取内容
	public function getOneBy($sql = '', $order = ''){
		if(!$sql) return false;
		$sql .= $order ? " order by ".$order : '';
		$return = $this->db->get_one("SELECT * FROM ".$this->table." WHERE ".$sql." limit 0,1;");
		return $return;
	}
	
	//直接执行SQL，如果是update和delete可以用needreturn控制返回
	public function doSQL($sql = '', $needreturn = true){
		if(!$sql) return false;
		$result = $this->db->query($sql);
		if(!$needreturn) return $result;
		$return = array();
		while($r = $this->db->fetch_array($result)){
			$return[] = $r;
		}
		$this->db->free_result($result);
		$return = !empty($return) ? $return : array();
		return $return;		
	}
	
	//统计
	public function getCount($sql = ''){
		if($sql) $sql = " where ".$sql." ";
		$return = $this->doSQL("SELECT count(*) as total FROM ".$this->table.$sql.";");
		return $return[0]['total'];
	}
	
	//出错信息
	public function errormsg(){
		return $this->errormsgs[$this->errormsg];
	}
	
	//执行缓存,s为缓存时间
	public function cache($name, $var='', $s=10){
		$name = $this->getCacheName($name);
		if($this->cacheType == 'memcache'){
			$return = $this->cache->memcacheCache($name,$var,$s);
		}else{
			$return = $this->cache->fileCache($name,$var,$s);
		}
		return $return;
	}
	
	//生成缓存名称
	public function getCacheName($name = ''){
		if(!$name) return false;
		$return = $this->table.'-'.$name;
		//$return = md5($this->table.'-'.$name);
		return $return;
	}
	
	//导出CSV
	public function exportCSV($filename = '', $data = ''){
		$filename = $filename ? $filename : time();
		header("Content-Type:text/csv; charset=gbk");
		header("Content-Disposition:attachment;filename=".$filename.".csv");
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');
		mb_convert_encoding($data, "GBK", "UTF-8");
		echo $data;
	}
	
	
	//获取标签地址
	public function getTagUrl($tagName, $tagSortName){
		$url = "/";
		if(!$tagName) return $url;
		$url .= $tagName;
		if($tagSortName) $url .= "-".$tagSortName;
		return $url;
	}
	
	//获取标签内容数量
	public function getTagNum($tagSortField, $tagSortSQL){
		$num = 0;
		$sql = $tagSortField.$tagSortSQL;
		$num = $this->getCount($sql);
		return $num;
	}	

	
	//缓存标签
	public function cacheTag($name, $tagArray){
		$tagCache = array();
		if(!empty($tagArray)){
			foreach($tagArray as $this_tagname=>$this_tag){
				$tagCache[$this_tagname] = $this_tag;
				$tagCache[$this_tagname]["url"] = $this->getTagUrl($this_tagname, $this_tag['name']);			
				if(!empty($this_tag['sort'])){
					foreach($this_tag['sort'] as $this_tag_sort_name=>$this_tag_sort){
						$tagCache[$this_tagname]['sort'][$this_tag_sort_name] = $this_tag_sort;
						$tagCache[$this_tagname]['sort'][$this_tag_sort_name]['url'] = $this->getTagUrl($this_tagname, $this_tag_sort_name);
						$tagCache[$this_tagname]['sort'][$this_tag_sort_name]['num'] = $this->getTagNum($this_tag['field'], $this_tag_sort['sql']);
					}
				}
			}
			$return = $this->cache($name, $tagCache);
			return $return;
		}
	}
	
}
?>