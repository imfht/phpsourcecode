<?php
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();
if(empty($_GET["type"])){
 $list = $database->select("book","*",["ORDER" => ["date"=>"DESC"]]);
}else{
	switch ($_GET['type'])
		{
			case 1:
			 	 $list = $database->select("book","*",["view" => "1","ORDER" => ["date" => "DESC"]]);
			 	break;
			case 2:
			 	 $list = $database->select("book","*",["view" => "0","ORDER" => ["date" => "DESC"]]);
			 	break;
		}
}
//替换type ID为name
for($i=0;$i<count($list);$i++){
	$typename = $database->select("type", "name", ["id[=]" =>$list[$i]["type"]]);
	if(!empty($typename[0])){
		$list[$i]['type']=$typename[0];//重新赋值name
	}else{
		$list[$i]['type']="<span style='color:red'>索引数据不完整</span>";
	}
	$num = $database->count("replay", ["bid[=]" =>$list[$i]['id']]);
	$list[$i]['count']=$num; //格式化时间
	$list[$i]['date']=date('Y-m-d',$list[$i]['date']); //格式化时间
}
echo json_encode($list);
?>