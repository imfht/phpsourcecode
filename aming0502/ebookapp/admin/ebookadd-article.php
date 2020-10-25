<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>新建抓取</title>
</head>

<body>
<?php
include_once "mysql_class.php";

	if($_POST["url"];){
		$title = $_POST["title"];
		$url = $_POST["url"];
		$db =  new mysql();
		$insert_sql =“INSERT INTO `t_article` (`id` ,`title` ,`url`)VALUES (NULL , '".$title."', '".$url."')”;
		$db->query($insert_sql);
		echo "插入成功";
	}else{
?>
<form name="form1" method="post" action="add-article.php">
  <p>文章标题：
    <input name="title" type="text" id="title" size="50">
</p>
  <p>文章url:
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
