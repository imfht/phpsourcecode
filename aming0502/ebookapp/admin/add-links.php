<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>新增链接</title>
</head>

<body>
<?php
include_once "../init.php";
include_once "../util/mysql_class.php";

	
	
	function addLinks($name,$url){
		$db =  new mysql();
		$insert_sql="insert into t_links (name,url) values('".$name."','".$url."')";
		$db->query($insert_sql);
		echo $name."插入成功<br>";
	}
	
	if(@$_POST["url"]){
		$name = $_POST["name"];
		$url = $_POST["url"];
		addLinks($name,$url);
	}else{
		
?>
<form name="form1" method="post" action="#">
  <p>网站名称:
    <input name="name" type="text" id="name" size="100" maxlength="200">
  </p>
  <p>网站url:
    <input name="url" type="text" id="url" size="100" maxlength="200">
  </p>
  <p>
    <input type="submit" name="Submit" value="提交">
  </p>
</form>
<?php
	} 
?>
</body>
</html>
