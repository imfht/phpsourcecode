<?php
/**
 * User: weicaihong.com
 * Date: 14-10-14 17:14
 * Copyright: http://www.weicaihong.com
 */

// 表前缀 $prefix
$tb_user_bonus = $prefix.'user_bonus';
$keyword = $post_data['keyword'];
$inputArray = explode(",",$keyword);
$method = $inputArray[0];

//输出数组
$data = array();

//同步红包
if($method == "synchro"){
	//红包类型
	$typeId = $inputArray[1];
	//已经取过的最大id
	$maxId = $inputArray[2];
	//获取limit参数
	$limitNum = $inputArray[3];	
	
	
	$query_sql = "SELECT bonus_id,bonus_sn,bonus_type_id FROM
					`$tb_user_bonus` WHERE user_id = 0 and bonus_type_id = ".$typeId.
					" and bonus_id > $maxId order by bonus_id limit ".$limitNum;
	// 查询sql
	$sth = $pdo_db->prepare($query_sql);
	$sth->execute();

	$data = $sth->fetchAll(PDO::FETCH_ASSOC);
	//2015-05-25 增加返回参数记录数据长度 liuli
	$num = count($data);
	//如果没有取到数据则判断原来是否有数据
	if($num == 0){
		$query_sql = "SELECT count(*) as count FROM
					`$tb_user_bonus` WHERE user_id = 0 and bonus_type_id = ".$typeId;
		// 查询sql
		$sth = $pdo_db->prepare($query_sql);
		$sth->execute();
		$dataT = $sth->fetchAll(PDO::FETCH_ASSOC);
		//没有该类型的数据则置num为-1
		if($dataT[0]['count'] == 0){
			$num = -1;
		}
	}
	$data['num'] = $num;
	$data['typeId'] = $typeId;
	
	//	$dataTmp['num'] = count($dataTmp);
	//	$dataTmp['num'] = 5;
	//	$allNum = "";
	//	for($i = 0;$i < $dataTmp['num'];$i++){
	//		if($dataTmp[$i]['bonus_sn']!=""){
	//			if($i < $dataTmp['num'] && ($i+1)!= $dataTmp['num']){
	//				$allNum = $allNum.$dataTmp[$i]['bonus_sn'].",";
	//			}else{
	//				$allNum = $allNum.$dataTmp[$i]['bonus_sn'];
	//			}
	//		}
	//	}
	//	$data['allNum'] = $allNum;
	//	$data['typeId'] = $typeId;
}else if($method == "getBonusType"){
	// 表前缀 $prefix
	$tb_bonus_type = $prefix.'bonus_type';
	$query_sql = "SELECT * FROM `$tb_bonus_type`";
	// 查询sql
	$sth = $pdo_db->prepare($query_sql);
	$sth->execute();

	$data = $sth->fetchAll(PDO::FETCH_ASSOC);
}


// 转换为json
$json_data = json_encode($data);

// 全部数据以UTF8 编码
if($ec_charset != 'UTF8')
{
	$json_data = mb_convert_encoding($json_data,'UTF-8','GBK');
}

// 输出
echo $json_data;
exit;


