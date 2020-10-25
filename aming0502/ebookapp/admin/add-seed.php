<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>新建种子</title>
</head>

<body>
<?php
include_once "../init.php";
include_once "../util/Model_class.php";

function addSeed($seed_url,$category_id,$parse_class){
	$model = new Model();
	$model->insert("t_seeds", array("category_id","url","parse_class"), array($category_id,$seed_url,$parse_class));
	echo $seed_url."插入成功<br>";
}

if(@$_POST["url"]){
	$category_id = $_POST["category_id"];
	$seed_url = $_POST["url"];
	$parse_class = $_POST["parse_class"];
	//http://www.aa.com/aa[start-end].html
	$bracket_left = strpos($seed_url,"[");
	if($bracket_left){
		$bracket_right = strpos($seed_url,"]");
		$url_prefix = substr($seed_url,0,$bracket_left);
		$middle = substr($seed_url,$bracket_left+1,$bracket_right-$bracket_left-1);
		$tag = strpos($middle,"-");
		$tag_start = substr($middle,0,$tag);
		$tag_end = substr($middle,$tag+1);
		$url_suffix = substr($seed_url,$bracket_right+1);
		for(;$tag_start<=$tag_end;$tag_start++){
			$seed_e = $url_prefix.$tag_start.$url_suffix;
			addSeed($seed_e,$category_id,$parse_class);
			//echo $seed_e."<br>";
		}
		//echo $url_prefix."<br>";
		//echo $tag_start."<br>";
		//echo $tag_end."<br>";
			
	}else{
		addSeed($seed_url,$category_id,$parse_class);
	}
}else{
	$model = new Model();
	$rows = $model->select("t_category");
	//print_r($rows[0]);
	?>
<form name="form1" method="post" action="#">
<p>种子分类： <select name="category_id">
<?php
foreach($rows as $row){
	?>
	<option value="<?php echo $row["id"];?>"><?php echo $row["category_name"];?></option>
	<?php }?>
</select></p>
<p>文章url(支持规则:如[11-99].html): <input name="url" type="text" id="url" size="100"
	maxlength="200"></p>
<p>解析类名: <input name="parse_class" type="text" id="parse_class" size="100"
	maxlength="200"></p>
<p><input type="submit" name="Submit" value="提交"></p>
</form>
	<?php
}
?>
</body>
</html>
