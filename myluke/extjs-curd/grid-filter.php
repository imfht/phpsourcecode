<?php

mysql_pconnect('10.4.12.46','uQiWsyHIqIsKh', 'pqeDBUTaX5DNG') or die("Could not connect");
mysql_select_db('d400ce84a4b994ea982d199337b4c8b2b') or die("Could not select database");

$action				= isset($_GET['action'])?$_GET['action']:'';
$id					= isset($_POST['id'])?$_POST['id']:'';
$product_name		= isset($_POST['product_name'])?$_POST['product_name']:'';
$product_sell		= isset($_POST['product_sell'])?$_POST['product_sell']:'';
$product_purchas	= isset($_POST['product_purchas'])?$_POST['product_purchas']:'';
switch($action){
	case "save":
		$time = date('Y-m-d H:i:s',time());
		$sql = "insert into goods_product (`product_name`,`product_sell_price`,`product_purchasing_price`,`product_updatetime`,`product_class_id`,`product_danwei_id`)values('$product_name','$product_sell','$product_purchas','$time',1,5)";
		//echo $sql;
		$insert_id = mysql_query($sql);
		echo '{success:true, file:'.json_encode($insert_id).'}';
		exit();
		break;
	case "del":
		$sql = "delete from goods_product where id=".$id;
		//echo $sql; 
		$re = mysql_query($sql);
		echo '{success:true, file:'.json_encode($re).'}';
		exit();
		break;
	case "edit":
		$sql = "select * from goods_product where id=".$id;
		$re = mysql_query($sql);
		$rs = mysql_fetch_object($re);
		echo '{"success":true,"data":['.json_encode($rs).']}';
		exit();
		break;
	case "update":
		$time = date('Y-m-d H:i:s',time());
		$sql = "update goods_product set  `product_name`= '$product_name',`product_purchasing_price`='$product_purchas',`product_sell_price`='$product_sell',`product_updatetime`='$time' where id=".$id;
		//echo $sql; 
		$re = mysql_query($sql);
		echo '{success:true, file:'.json_encode($re).'}'; 
		exit();
		break;
}
$start = ($_REQUEST["start"] == null)? 0 : $_REQUEST["start"];
$count = ($_REQUEST["limit"] == null)? 20 : $_REQUEST["limit"];
$sort = ($_REQUEST["sort"] == null)? "" : $_REQUEST["sort"];
$dir = ($_REQUEST["dir"] == "DESC")? "DESC" : "";
$filter = $_REQUEST["filter"];

$where = " 0 = 0 ";

if (is_array($filter)) {
	for ($i=0;$i<count($filter);$i++){
		switch($filter[$i]['data']['type']){
			case 'string' : $qs .= " AND ".$filter[$i]['field']." LIKE '%".$filter[$i]['data']['value']."%'"; Break;
			case 'list' : 
				if (strstr($filter[$i]['data']['value'],',')){
					$fi = explode(',',$filter[$i]['data']['value']);
					for ($q=0;$q<count($fi);$q++){
						$fi[$q] = "'".$fi[$q]."'";
					}
					$filter[$i]['data']['value'] = implode(',',$fi);
					$qs .= " AND ".$filter[$i]['field']." IN (".$filter[$i]['data']['value'].")"; 
				}else{
					$qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'"; 
				}
			Break;
			case 'boolean' : $qs .= " AND ".$filter[$i]['field']." = ".($filter[$i]['data']['value']); Break;
			case 'numeric' : 
				switch ($filter[$i]['data']['comparison']) {
					case 'eq' : $qs .= " AND ".$filter[$i]['field']." = ".$filter[$i]['data']['value']; Break;
					case 'lt' : $qs .= " AND ".$filter[$i]['field']." < ".$filter[$i]['data']['value']; Break;
					case 'gt' : $qs .= " AND ".$filter[$i]['field']." > ".$filter[$i]['data']['value']; Break;
				}
			Break;
			case 'date' : 
				switch ($filter[$i]['data']['comparison']) {
					case 'eq' : $qs .= " AND ".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
					case 'lt' : $qs .= " AND ".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
					case 'gt' : $qs .= " AND ".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
				}
			Break;
		}
	}	
	$where .= $qs;
}

$query = "SELECT * FROM goods_product WHERE ".$where;
if ($sort != "") {
	$query .= " ORDER BY ".$sort." ".$dir;
}
$query .= " LIMIT ".$start.",".$count;

//echo $query;
$rs = mysql_query($query);
$total = mysql_query("SELECT COUNT(id) FROM goods_product WHERE ".$where);
$total = mysql_result($total, 0, 0);

$arr = array();
while($obj = mysql_fetch_object($rs)) {
	$arr[] = $obj;
}
	
$output = json_encode($arr);
echo '{"total":"'.$total.'","data":'.$output.'}';
?>