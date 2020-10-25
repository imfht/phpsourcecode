<?php
	header("Content-Type: text/html;charset=utf-8"); 
	
	class DateBase {
		private $host;
		private $user;
		private $pass;
		private $table;
		
		function __construct($host,$user,$pass,$table) {
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->table = $table;
			$this->connect();
		}
		
		function connect() {
			$link = mysql_connect($this->host,$this->user,$this->pass) or die("<h3>数据库连接失败！</h3>");
			$se = mysql_select_db($this->table,$link);
			return $link;
		}
		
		function select($table,$con) {
			$select = mysql_select_db($table,$con);
			return $select;
		}
		function query($v,$con) {
			mysql_query("set names 'utf8'");//防止插入数据库的时候乱码
			$query = mysql_query($v,$con);
			return $query;
		}
		function fetch_row($data) {
			$row = mysql_fetch_row($data);
			return $row;
		}
		function fetch_assoc($data) {
			$assoc = mysql_fetch_assoc($data);
			return $assoc;
		}
		function fetch_arr($result,$f) {
			$fetch_arr = mysql_fetch_array($result,$f);
			return $fetch_arr;
		}
		
		function rows($row) {
			$my_rows = mysql_num_rows($row);
			return $my_rows;
		}
		
		function close($c) {
			mysql_close($c);
		}
		//权限函数
		function power() {
			if(!isset($_SESSION['account'])) {
				echo "<center>您无权访问此页面，请先登录！</center>";
				echo "<meta http-equiv='refresh' content='5; url=./login.php' />";
				exit();
				return false;
			}
		}
		//SEO
		function seo() {
			$sql = "SELECT * FROM `seo` WHERE 1";
			$con = $this->connect();
			$query = $this->query($sql,$con);
			$seo = $this->fetch_arr($query);
			return $seo;
		}
		//输出文章
		function article($id) {
			$sql = "SELECT `id`, `date`, `title`, `keywords`, `description`, `content` FROM `article` WHERE `id`= $id";
			$con = $this->connect();
			$query = $this->query($sql,$con);
			$article = $this->fetch_assoc($query);
			return $article;
		}
		//修改文章
		function edit($title,$keywords,$description,$content,$id) {
			$sql = "UPDATE `article` SET `date`=NOW(),`title`='$title',`keywords`='$keywords',`description`='$description',`content`='$content' WHERE `id` = '$id'";
			$con = $this->connect();
			$query = $this->query($sql,$con);
			echo "<script>alert('更新成功！');</script>";
		}
		
	}
	
	$db = new DateBase('localhost','root','','blog');
?>