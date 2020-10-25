<?php
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
 $list = $database->select("replay","*",["bid[=]" =>$_GET["sid"]]);
//替换数组
 if(empty($_GET['index']) || $_GET['index'] !=="yes"){
    session();
    for($i=0;$i<count($list);$i++){
		$list[$i]['time']=date('Y-m-d H:i:s',$list[$i]['time']);
		$list[$i]['del']=$list[$i]['id'];
		if($system_viewcity=="on"){
	        $city = getCity($list[$i]["ip"]);
	        $list[$i]["ip"] = "- 位置信息：".$city["country"]."-".$city["region"]."-".$city["city"];
	    }else{
	         $list[$i]["ip"] = "未开启显示位置";
	    }
	}
  }else{
	for($i=0;$i<count($list);$i++){
		$list[$i]['time']=date('Y-m-d H:i:s',$list[$i]['time']);
		$list[$i]['del']="";
		if($system_viewcity=="on"){
	        $city = getCity($list[$i]["ip"]);
	        $list[$i]["ip"] = "- 位置信息：".$city["country"]."-".$city["region"]."-".$city["city"];
        }else{
         $list[$i]["ip"] = "";
        }
	}
}
echo json_encode($list);
?>