<?php
 include "../../libs/function.php";
 if(empty($_GET["type"])){
 	//全部查询方法
	$list = $database->select("book", "*", ["view" => "1","ORDER" => ["date" => "DESC"]]);
}else{
	//按分类查询方法
	$list = $database->select("book", "*", [
		"AND" => [
			"type" => $_GET["type"],
			"view" => "1"
		],"ORDER" => ["date" => "DESC"]
	]);
}
//替换数组
for($i=0;$i<count($list);$i++){
	$typename = $database->select("type", "name", ["id[=]" =>$list[$i]["type"]]);
	
	if(!empty($typename[0])){
		$list[$i]['type']=$typename[0];//重新赋值name
	}else{
		$list[$i]['type']="<span style='color:red'>索引数据不完整</span>";
	}
	$list[$i]['date']=date('Y-m-d',$list[$i]['date']);
}

echo json_encode($list);
?>