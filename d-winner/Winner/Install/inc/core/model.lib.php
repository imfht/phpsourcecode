<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
class Model{
	public $db_host;								//数据库主机名
	public $db_name;								//数据库名称
	public $db_user;								//数据库用户名称
	public $db_pwd;									//数据库用户密码
	public $db_prefix = 'dwin_';					//默认数据表前缀
	public $db_language = 'utf8';					//默认数据库编码	
	
	public $db_cache_dir;							//数据缓存目录
	public $db_cache_time = 0;						//设置数据缓存时间(单位:秒),0为马上过期,-1为永不过期
	
	//构造函数,数组方式配置数据库
    function __construct($cfg=NULL) {
        if ($cfg) {
            $this->config($cfg);
        }
    }
	function config($cfg){
		if(isset($cfg['DB_HOST'])){
			$this->db_host = $cfg['DB_HOST'];
		}
		if(isset($cfg['DB_NAME'])){
			$this->db_name = $cfg['DB_NAME'];
		}
		if(isset($cfg['DB_USER'])){
			$this->db_user = $cfg['DB_USER'];
		}
		if(isset($cfg['DB_PWD'])){
			$this->db_pwd = $cfg['DB_PWD'];
		}
		if(isset($cfg['DB_PREFIX'])){
			$this->db_prefix = $cfg['DB_PREFIX'];
		}
	}
	
	//用于储存查询的数据
	public $info = array();
	
	//数据库连接方法
	function conn(){
		$conn = mysql_connect($this->db_host,$this->db_user,$this->db_pwd) or die ('数据库服务器连接错误，'.mysql_error());
		mysql_select_db($this->db_name,$conn) or die ('数据库连接错误，数据库"<b style="color:red">'.$this->db_name.'</b>"不存在');
		mysql_query('set names '.$this->db_language);
		return $conn;
	}
	
	var $tb_name;
	var $cache;
	var $order;
	var $where;
	var $way;
	var $mode;
	private $arr_info;
	//数据表操作方法---数据查询(无分页功能)  $way是查询数据表的delete的方式
	//'-1'查询全部数据(全部), '0'查询delete为0的数据(未删除的/默认), '1'查询delete为1的数据(已删除的), 为2时,$name输入sql语句
	function sele($name,$cache,$order=NULL,$where=NULL,$way=-1,$mode=1){
		$this->tb_name = $name;
		$this->cache = $cache;
		$this->order = $order;
		$this->where = $where;
		$this->way = $way;
		$this->mode = $mode;
		$this->arr_info = array();
		$_cache_path = $this->cache_path($this->cache);
		if(!$this->is_cached($_cache_path)){
			if($this->way==0){
				if($where==NULL){
					$sql = "SELECT * FROM `".$this->tb_name."` where `delete`=0 order by ".$this->order."id desc";
				}else{
					$sql = "SELECT * FROM `".$this->tb_name."` where ".$this->where." `delete`=0 order by ".$order."id desc";
				}
			}elseif($this->way==1){
				if($this->where==NULL){
					$sql = "SELECT * FROM `".$this->tb_name."` where `delete`=1 order by ".$this->order."id desc";
				}else{
					$sql = "SELECT * FROM `".$this->tb_name."` where ".$this->where." `delete`=1 order by ".$this->order."id desc";
				}
			}elseif($this->way==2){
					$sql = $this->tb_name;
			}elseif($this->way==-1){
				if($this->where==NULL){
					$sql = "SELECT * FROM `".$this->tb_name."` order by ".$this->order."id desc";
				}else{
					$sql = "SELECT * FROM `".$this->tb_name."` where ".$this->where." order by ".$this->order."id desc";
				}
			}
			$rs = mysql_query($sql,$this->conn());
			if(!$rs){
				return mysql_error();
				exit();
			}
			while($ain=mysql_fetch_array($rs,$this->mode)){
				$this->arr_info[] = $ain;
			}
			if($this->db_cache_time!=0 && $cache!=NULL){
				$this->put_cache($this->arr_info,$_cache_path);
			}
			return $this->arr_info;
			unset($ain);
			mysql_free_result($rs);
			
		}else{
			$fp = fopen($_cache_path,'rb');
			while(!feof($fp)){
				$fg = fgets($fp);
				$t = explode(' @,@ ',$fg);
				$new = array();
				foreach($t as $s){
					list($k,$v) = explode(' s=> ',$s);
					$new[$k] = $v;
				}
				$this->arr_info[] = $new;
			}
			fclose($fp);
			return $this->arr_info;
			unset($new);
		}
	}
	
	//数据表操作方法---数据查询GROUP(无分页功能)  $way是查询数据表的delete的方式
	//'-1'查询全部数据(全部), '0'查询delete为0的数据(未删除的/默认), '1'查询delete为1的数据(已删除的), 为2时,$name输入sql语句
	function gSele($name,$field,$join,$cache,$order=NULL,$where=NULL,$way=-1,$mode=1){
		$this->tb_name = $name;
		$this->cache = $cache;
		$this->order = $order;
		$this->where = $where;
		$this->way = $way;
		$this->mode = $mode;
		$this->arr_info = array();
		$_cache_path = $this->cache_path($this->cache);

		if(!$this->is_cached($_cache_path)){
			if($this->way==0){
				if($this->where==NULL){
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$join." where ".$this->tb_name.".delete=0 order by ".$this->order."id desc";
				}else{
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$join." where ".$this->where." ".$this->tb_name.".delete=0 order by ".$this->order."id desc";
				}
			}elseif($this->way==1){
				if($this->where==NULL){
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$join." where ".$this->tb_name.".delete=1 order by ".$this->order."id desc";
				}else{
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$join." where ".$this->where." ".$this->tb_name.".delete=1 order by ".$this->order."id desc";
				}
			}elseif($this->way==-1){
				if($this->where==NULL){
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$join." order by ".$this->order."id desc";
				}else{
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$join." where ".$this->where." order by ".$this->order."id desc";
				}
			}
			$rs = mysql_query($sql,$this->conn());
			if(!$rs){
				return mysql_error();
				exit();
			}
			while($ain=mysql_fetch_array($rs,$this->mode)){
				$this->arr_info[] = $ain;
			}
			if($this->db_cache_time!=0 && $cache!=NULL){
				$this->put_cache($this->arr_info,$_cache_path);
			}
			return $this->arr_info;
			unset($ain);
			mysql_free_result($rs);
			
		}else{
			$fp = fopen($_cache_path,'rb');
			while(!feof($fp)){
				$fg = fgets($fp);
				$t = explode(' @,@ ',$fg);
				$new = array();
				foreach($t as $s){
					list($k,$v) = explode(' s=> ',$s);
					$new[$k] = $v;
				}
				$this->arr_info[] = $new;
			}
			fclose($fp);
			return $this->arr_info;
			unset($new);
		}
	}
	
	//数据表操作方法---数据查询(带分页功能)  $way是查询数据表的delete的方式
	//'-1'查询全部数据(全部), '0'查询delete为0的数据(未删除的/默认), '1'查询delete为1的数据(已删除的), 为2时,输入sql语句
	private $p_name;
	private $p_order;
	private $p_join;
	private $p_where;
	private $p_way;
	private $p_sway;
	var $page_size;
	var $page_weight;
	public $page;
	function page($name,$page_size,$page_weight,$cache,$order=NULL,$where=NULL,$way=-1,$mode=1){
		$this->tb_name = $name;
		$this->p_order = $order;
		$this->p_where = $where;
		$this->p_way = $way;
		//$this->p_sway = $sway;
		$this->page_size = $page_size;
		$this->page_weight = $page_weight;
		if(isset($_GET['page'])){
			$this->page = $_GET['page'];
		}else{
			$this->page = 1;
		}
		$this->arr_info = array();
		$_cache_path = $this->cache_path($cache,$this->page);
		if(!$this->is_cached($_cache_path)){
			if($this->p_way==0){
				if($this->p_where==NULL){
					$sql = "SELECT * FROM `".$this->tb_name."` where `delete`=0 order by ".$this->p_order."id desc limit ".$this->setpage().",".$this->page_size;
				}else{
					$sql = "SELECT * FROM `".$this->tb_name."` where ".$this->p_where." `delete`=0 order by ".$this->p_order."id desc limit ".$this->setpage().",".$this->page_size;
				}
			}elseif($this->p_way==1){
				if($this->p_where==NULL){
					$sql = "SELECT * FROM `".$this->tb_name."` where `delete`=1 order by ".$this->p_order."id desc limit ".$this->setpage().",".$this->page_size;
				}else{
					$sql = "SELECT * FROM `".$this->tb_name."` where ".$this->p_where." `delete`=1 order by ".$this->p_order."id desc limit ".$this->setpage().",".$this->page_size;
				}
			}elseif($this->p_way==2){
					$sql = $this->p_order." limit ".$this->setpage().",".$this->page_size;
			}elseif($this->p_way==-1){
				if($this->p_where==NULL){
					$sql = "SELECT * FROM `".$this->tb_name."` order by ".$this->p_order."id desc limit ".$this->setpage().",".$this->page_size;
				}else{
					$sql = "SELECT * FROM `".$this-tb_name."` where ".$this->p_where." order by ".$this->p_order."id desc limit ".$this->setpage().",".$this->page_size;
				}
			}
			$rs = mysql_query($sql,$this->conn());
			if(!$rs){
				return mysql_error();
				exit();
			}
			while($ain=mysql_fetch_array($rs,$mode)){
				$this->arr_info[] = $ain;
			}
			if($this->db_cache_time!=0 && $cache!=NULL){
				$this->put_cache($this->arr_info,$_cache_path);
			}
			return $this->arr_info;
			unset($ain);
			mysql_free_result($rs);
			
		}else{
			$this->setpage();
			$fp = fopen($_cache_path,'rb');
			while(!feof($fp)){
				$fg = fgets($fp);
				$t = explode(' @,@ ',$fg);
				$new = array();
				foreach($t as $s){
					list($k,$v) = explode(' s=> ',$s);
					$new[$k] = $v;
				}
				$this->arr_info[] = $new;
			}
			fclose($fp);
			return $this->arr_info;
			unset($new);
		}
	}
	
	//数据表操作方法---数据查询(带分页功能)  $way是查询数据表的delete的方式
	//'-1'查询全部数据(全部), '0'查询delete为0的数据(未删除的/默认), '1'查询delete为1的数据(已删除的)
	function gPage($name,$field,$join,$page_size,$page_weight,$cache,$order=NULL,$where=NULL,$way=-1,$mode=1){
		$this->tb_name = $name;
		$this->p_order = $order;
		$this->p_join = $join;
		$this->p_where = $where;
		$this->p_way = $way;
		//$this->p_sway = $sway;
		$this->page_size = $page_size;
		$this->page_weight = $page_weight;
		if(isset($_GET['page'])){
			$this->page = $_GET['page'];
		}else{
			$this->page = 1;
		}
		$this->arr_info = array();
		$_cache_path = $this->cache_path($cache,$this->page);
		if(!$this->is_cached($_cache_path)){
			if($this->p_way==0){
				if($this->p_where==NULL){
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$this->p_join." where ".$this->tb_name.".delete=0 order by ".$this->p_order."id desc limit ".$this->setgPage().",".$this->page_size;
				}else{
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$this->p_join." where ".$this->p_where." ".$this->tb_name.".delete=0 order by ".$this->p_order."id desc limit ".$this->setgPage().",".$this->page_size;
				}
			}elseif($this->p_way==1){
				if($this->p_where==NULL){
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$this->p_join." where ".$this->tb_name.".delete=1 order by ".$this->p_order."id desc limit ".$this->setgPage().",".$this->page_size;
				}else{
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$this->p_join." where ".$this->p_where." ".$this->tb_name.".delete=1 order by ".$this->p_order."id desc limit ".$this->setgPage().",".$this->page_size;
				}
			}elseif($this->p_way==-1){
				if($this->p_where==NULL){
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$this->p_join." order by ".$this->p_order."id desc limit ".$this->setgPage().",".$this->page_size;
				}else{
					$sql = "SELECT ".$field." FROM `".$this->tb_name."` ".$this->p_join." where ".$this->p_where." order by ".$this->p_order."id desc limit ".$this->setgPage().",".$this->page_size;
				}
			}
			$rs = mysql_query($sql,$this->conn());
			if(!$rs){
				return mysql_error();
				exit();
			}
			while($ain=mysql_fetch_array($rs,$mode)){
				$this->arr_info[] = $ain;
			}
			if($this->db_cache_time!=0 && $cache!=NULL){
				$this->put_cache($this->arr_info,$_cache_path);
			}
			return $this->arr_info;
			unset($ain);
			mysql_free_result($rs);
			
		}else{
			$this->setgPage();
			$fp = fopen($_cache_path,'rb');
			while(!feof($fp)){
				$fg = fgets($fp);
				$t = explode(' @,@ ',$fg);
				$new = array();
				foreach($t as $s){
					list($k,$v) = explode(' s=> ',$s);
					$new[$k] = $v;
				}
				$this->arr_info[] = $new;
			}
			fclose($fp);
			return $this->arr_info;
			unset($new);
		}
	}	

	//分页设置		$this->p_sway为1时,$sql不能为空
	var $total;
	var $count_page;
	var $min_page;
	var $max_page;
	var $now_page;
	public $offset;
	function setpage(){
		if($this->p_way==0){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` where ".$this->tb_name.".delete=0";
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` where ".$this->p_where." ".$this->tb_name.".delete=0";
			}
		}elseif($this->p_way==1){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` where ".$this->tb_name.".delete=1";
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` where ".$this->p_where." ".$this->tb_name.".delete=1";
			}
		}elseif($this->p_way==2){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` where ".$this->tb_name.".delete=0";
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` where ".$this->tb_name.".delete=1";
			}
		}elseif($this->p_way==-1){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."`";
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` where ".$this->p_where;
			}
		}
		$query = mysql_query($tsql,$this->conn());
		$this->total = mysql_result($query,0,"total");
		$this->count_page = ceil($this->total/$this->page_size);
		if(isset($_GET['page'])){
			$this->now_page = $_GET['page'];
		}else{
			$this->now_page = 1;
		}
		$this->offset = ($this->now_page-1)*$this->page_size;
		
		if($this->page_weight%2==1 and $this->count_page>=$this->page_weight){
			if($this->now_page<=($this->page_weight+1)/2){
				$this->min_page = 1;
				$this->max_page = $this->page_weight;
			}elseif($this->now_page>($this->page_weight+1)/2 and $this->now_page<=$this->count_page-(($this->page_weight+1)/2)){
				$this->min_page = $this->now_page-(($this->page_weight+1)/2)+1;
				$this->max_page = $this->now_page+(($this->page_weight+1)/2)-1;
			}elseif($this->now_page>$this->count_page-(($this->page_weight+1)/2)){
				$this->min_page = $this->count_page-$this->page_weight+1;
				$this->max_page = $this->count_page;
			}
		}elseif($this->page_weight%2==0 and $this->count_page>=$this->page_weight){			
			if($this->now_page<=$this->page_weight/2){
				 $this->min_page = 1;
				 $this->max_page = $this->page_weight;
			}elseif($this->now_page>$this->page_weight/2 and $this->now_page<$this->count_page-($this->page_weight/2)){
				$this->min_page = $this->now_page-(($this->page_weight)/2)+1;
				$this->max_page = $this->now_page+(($this->page_weight)/2);
			}elseif($this->now_page>=$this->count_page-($this->page_weight/2)){
				$this->min_page = $this->count_page-$this->page_weight+1;
				$this->max_page = $this->count_page;
			}
		}elseif($this->count_page<$this->page_weight){
			$this->min_page = 1;
			$this->max_page = $this->count_page;
		}	
		return $this->offset;
	}
	
		//分页设置带group by		$this->p_sway为1时,$sql不能为空
		function setgPage(){
		if($this->p_way==0){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join." where ".$this->tb_name.".delete=0";
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join." where ".$this->p_where." ".$this->tb_name.".delete=0";
			}
		}elseif($this->p_way==1){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join." where ".$this->tb_name.".delete=1";
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join." where ".$this->p_where." ".$this->tb_name.".delete=1";
			}
		}elseif($this->p_way==2){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join." where ".$this->tb_name.".delete=0";
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join." where ".$this->tb_name.".delete=1";
			}
		}elseif($this->p_way==-1){
			if($this->p_where==NULL){
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join;
			}else{
				$tsql = "SELECT count(*) as total FROM `".$this->tb_name."` ".$this->p_join." where ".$this->p_where;
			}
		}
		$query = mysql_query($tsql,$this->conn());
		$this->total = mysql_result($query,0,"total");
		$this->count_page = ceil($this->total/$this->page_size);
		if(isset($_GET['page'])){
			$this->now_page = $_GET['page'];
		}else{
			$this->now_page = 1;
		}
		$this->offset = ($this->now_page-1)*$this->page_size;
		
		if($this->page_weight%2==1 and $this->count_page>=$this->page_weight){
			if($this->now_page<=($this->page_weight+1)/2){
				$this->min_page = 1;
				$this->max_page = $this->page_weight;
			}elseif($this->now_page>($this->page_weight+1)/2 and $this->now_page<=$this->count_page-(($this->page_weight+1)/2)){
				$this->min_page = $this->now_page-(($this->page_weight+1)/2)+1;
				$this->max_page = $this->now_page+(($this->page_weight+1)/2)-1;
			}elseif($this->now_page>$this->count_page-(($this->page_weight+1)/2)){
				$this->min_page = $this->count_page-$this->page_weight+1;
				$this->max_page = $this->count_page;
			}
		}elseif($this->page_weight%2==0 and $this->count_page>=$this->page_weight){			
			if($this->now_page<=$this->page_weight/2){
				 $this->min_page = 1;
				 $this->max_page = $this->page_weight;
			}elseif($this->now_page>$this->page_weight/2 and $this->now_page<$this->count_page-($this->page_weight/2)){
				$this->min_page = $this->now_page-(($this->page_weight)/2)+1;
				$this->max_page = $this->now_page+(($this->page_weight)/2);
			}elseif($this->now_page>=$this->count_page-($this->page_weight/2)){
				$this->min_page = $this->count_page-$this->page_weight+1;
				$this->max_page = $this->count_page;
			}
		}elseif($this->count_page<$this->page_weight){
			$this->min_page = 1;
			$this->max_page = $this->count_page;
		}	
		return $this->offset;
	}
	
	//数据库操作方法---添加数据
	private $nkey;
	private $nval;
	function add($tb_name,$name,$value=NULL){
		$this->tb_name = $tb_name;
		if($name==NULL){
			$sql = $this->tb_name;
		//数组形式插入数据       $name = array('字段'=>'值','字段1'=>'值1','字段n'=>'值n',)
		}elseif(is_array($name)){
			foreach($name as $key=>$val){
				$this->nkey .= "`".$key."`,";
				$this->nval .= "'".$val."',";
			}
			$sql = "INSERT INTO `".$this->tb_name."` ( ".substr($this->nkey,0,-1).") VALUES ( ".substr($this->nval,0,-1).")";
			unset($name,$key,$val,$this->nkey,$this->nval);
		//字符串形式插入数据     $name = "`字段`,`字段1`,`字段n`";     $value = "'值','值1','值n'"
		}elseif(is_string($name) && is_string($value)){
			$sql = "INSERT INTO `".$this->tb_name."` ( ".$name.") VALUES ( ".$value.")";
		}
		$rs = mysql_query($sql,$this->conn());
		if(!$rs){
			return mysql_error();
			exit();
		}else{
			return 1;
		}
		mysql_free_result($rs);
		
	}
	
	//数据库操作方法---放入回收站
	function dTrash($tb_name,$id,$field='id'){
		$this->tb_name = $tb_name;
		if(!$this->filter($id)){
			return -1;
			exit();
		}
		if($id!=NULL){
			$sql = "UPDATE `".$this->tb_name."` SET `delete` = '1' WHERE `".$this->tb_name."`.`".$field."` ='".$id."'";
			$rs = mysql_query($sql,$this->conn());
		}
		if(!$rs){
			return mysql_error();
			exit();
		}else{
			return 1;
		}	
		mysql_free_result($rs);
		
	}
	
	//数据库操作方法---还原回收站
	function rTrash($tb_name,$id,$field='id'){
		$this->tb_name = $tb_name;
		if(!$this->filter($id)){
			return -1;
			exit();
		}
		if($id){
			$sql = "UPDATE `".$this->tb_name."` SET `delete` = '0' WHERE `".$this->tb_name."`.`".$field."` ='".$id."'";
			$rs = mysql_query($sql,$this->conn());
		}
		if(!$rs){
			return mysql_error();
			exit();
		}else{
			return 1;
		}
		mysql_free_result($rs);		
		
	}
	
	//数据库操作方法---更新数据
	private $uval;
	function up($tb_name,$name,$id,$field='id'){
		$this->tb_name = $tb_name;
		
		//数组形式更新数据       $name = array('字段'=>'值','字段1'=>'值1','字段n'=>'值n',)
		if($name==NULL && $id==NULL){
			$sql = $this->tb_name;
		}elseif(is_array($name)){
			foreach($name as $key=>$val){
				$this->uval .= "`".$key."` = '".$val."',";
			}
			$sql = "UPDATE `".$this->tb_name."` SET ".substr($this->uval,0,-1)." WHERE `".$this->tb_name."`.`".$field."` ='".$id."'";
			unset($name,$key,$val,$this->uval);
			
		//字符串形式插入数据     $name = "`字段` = '值',`字段1` = '值1',`字段n` = '值n'"
		}elseif(is_string($name)){
			$sql = "UPDATE `".$this->tb_name."` SET ".$name." WHERE `".$this->tb_name."`.`".$field."` ='".$id."'";
		}
		$rs = mysql_query($sql,$this->conn());
		if(!$rs){
			return mysql_error();
			exit();
		}else{
			return 1;
		}
		mysql_free_result($rs);
		
	}
	
	//数据库操作方法---删除数据
	function del($tb_name,$id,$field='id'){
		$this->tb_name = $tb_name;
		if(!$this->filter($id)){
			return -1;
			exit();
		}
		if($id==NULL){
			$sql = $this->tb_name;
		}elseif($id){
			$sql = "DELETE FROM `".$this->tb_name."` WHERE `".$this->tb_name."`.`".$field."` ='".$id."'";
			
		}
		$rs = mysql_query($sql,$this->conn());
		if(!$rs){
			return mysql_error();
			exit();
		}else{
			return 1;
		}
		mysql_free_result($rs);
		
	}
	
	
	//关闭数据库
	function closeconn(){
		mysql_close($this->conn);
	}
	
	//防sql注入函数
	function filter($word){
		$words = array();
		$words[] = "add";
		$words[] = "count";
		$words[] = "create";
		$words[] = "delete";
		$words[] = "drop";
		$words[] = "from";
		$words[] = "grant";
		$words[] = "insert";
		$words[] = "truncate";
		$words[] = "update";
		$words[] = "use";
		$words[] = "like";
		$words[] = "or";
		$words[] = "cas";
		$words[] = "rename";
		$words[] = "alter";
		$words[] = "modify";
		$words[] = "select";
		$words[] = "join";
		$words[] = "union";
		$words[] = "where";
		$words[] = "and";
		$w = strtolower($word);
		foreach($words as $t){
			if(preg_match("/\b$t\b/",$w)){
				return false;
				exit();
			}
		}
		if(strstr($w,'--')){
			return false;
			exit();
		}
		return true;
	}
	
    // 获取数据缓存路径
    private function cache_path($cache_name,$page=NULL) {
        return $this->db_cache_dir . md5($cache_name).'_'.$page . '.ca';
    }
	// 数据缓存是否有效
    private function is_cached($cache_path) {
        if (!file_exists($cache_path)) {
            return false;
        }
        if ($this->db_cache_time<0) {
            return true;
        }
        $cache_time = filemtime($cache_path);
        if ( time()-$cache_time > $this->db_cache_time ) {
            return false;
        }
        return true;
    }
	//编译数据缓存
	function put_cache($info,$cache_path){
		@mkdir(dirname($cache_path), 0777, true);
		$son='';$body='';
		foreach($info as $key=>$val){
			foreach($val as $skey=>$sval){
				$son .= $skey." s=> ".$sval." @,@ ";
				unset($val,$skey,$sval);
			}
			$body .= substr($son,0,-5)."\n";
			unset($key);
			$son = '';
		}
		file_put_contents($cache_path,substr($body,0,-1));
		$body = '';
	}
}