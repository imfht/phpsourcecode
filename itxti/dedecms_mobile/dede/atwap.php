<?php
require_once(dirname(__FILE__)."/config.php");
$dsql->SetQuery('select `id` , `typename` from `#@__arctype`');
$dsql->Execute();
while($tempArray = $dsql->GetArray()){
	$typeArray[] = $tempArray;
	foreach ($typeArray as $key => $value) {
		$typebyid[$value['id']] = $value['typename'];
	}
}
//更新设置
if(!empty($at_set_submit)){
	$query = "
		UPDATE `#@__atwap`
		SET `sitename` = '$at_sitename',
		`domain` = '$at_domin',
		`list_num` = '$at_list_num',
		`template_path` = '$at_template_path',
		`index_template` = '$at_index_template',
		`list_template` = '$at_list_template',
		`content_template` = '$at_content_template'
		WHERE `id` = '$id'	
	";
	$rs = $dsql->ExecuteNoneQuery($query);
	if($rs){
		showMsg('设置成功','atwap.php?a=setting');
	}else{
		showMsg('设置失败'.$dsql->GetError(),'javascript:;');
	}
}
//编辑栏目
if(!empty($at_cateedit_submit)){
	$query = "
		UPDATE `#@__atwap_type`
		SET `cat` = '$at_eidt_btypeid',
		`typename` = '$at_edit_typename'
		WHERE `typeid` = '$id'
	";
	$rs = $dsql->ExecuteNoneQuery($query);
	if($rs){
		showMsg('设置成功','atwap.php?a=category');
	}else{
		showMsg('设置失败'.$dsql->GetError(),'javascript:;');
	}

}


//增加栏目
if(!empty($addty_submit)){
	//print_r($_POST);
	//exit();
	$query = "INSERT INTO `#@__atwap_type`(cat,typename) VALUES('$btype','$typename')";
	$rs = $dsql->ExecuteNoneQuery($query);
	if($rs){
		showMsg('添加成功','atwap.php?a=category');
	}else{
		showMsg('添加失败'.$dsql->GetError(),'javascript:;');
	}
}


if(@$a=='setting'){
	//设置开始
	$dsql->SetQuery('select * from `#@__atwap`');
	$dsql->Execute();
	$setArray = $dsql->GetArray();
	include DedeInclude('templets/atwap_set.htm');
	//设置结束
}elseif(@$a=='category'){
	//分类开始
	$dsql->SetQuery('select * from `#@__atwap_type` ORDER BY `typeid` DESC');
	$dsql->Execute();
	while($tempArray = $dsql->GetArray()){
		$at_type_info[] = $tempArray;
	}
		@include DedeInclude('templets/atwap_category.htm');
	//分类结束
}elseif(@$a=='typeedit'){
	$typeid = $id;
	$dsql->SetQuery("select * from `#@__atwap_type` where typeid = '$typeid'");
	$dsql->Execute();
	$at_type_info = $dsql->GetArray();
	include DedeInclude('templets/atwap_categoryedit.htm');
}elseif(@$a=='typedel'){
	$rs = $dsql->ExecuteNoneQuery("delete from `#@__atwap_type` where typeid = '$id'");
	if($rs){
		showMsg('删除成功','atwap.php?a=category');
	}else{
		showMsg('删除失败'.$dsql->GetError(),'javascript:;');
	}
	
	
	
}
?>