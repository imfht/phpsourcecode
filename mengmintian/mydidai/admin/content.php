<?php
require '../includes/init.php';

$navModel = new ColumnModel();
$contentModel = new ContentModel();

switch($_GET['action']){
	case 'showContent':
		$showContent = true;
		$rs = $contentModel->showContent();
		break;
	case 'addContent':
		$addContent = true;
		foreach (getTreeColumn() as $key => $value) {
			$strtree .= '<option value="'.$value['id'].'">'.$value['treename'].'</option>';
		}
		add();
		break;
	case 'deleteContent':
		$contentModel->id = $_GET['id'];
		if($contentModel->deleteContent()){
			exit('删除成功！');
		}
		break;
	case 'updateContent':
		$one = $contentModel->oneContent($_GET['id']);
		$updateContent = true;
		$tree = getTreeColumn();
		foreach ($tree as $key => $value) {
			if($value['id'] != $one[0]['column_id']){
				$strtree .= '<option value="'.$value['id'].'" >'.$value['treename'].'</option>';
			}else{
				$strtree .= '<option value="'.$value['id'].'" selected="selected">'.$value['treename'].'</option>';
			}
		}

		$colorArr = array('black','red','blue','green','yellow');
		foreach($colorArr as $c){
			if ($c == $one[0]['color']){
				$strcolor .= '<option selected="selected" value="'.$c.'" style="color:'.$c.';font-weight:bold;">标题颜色</option>';
			}else{
				$strcolor .= '<option value="'.$c.'" style="color:'.$c.';font-weight:bold;">标题颜色</option>';
			}
		}

		$aArr = array('推荐','幻灯','跳转');
		$onearr = explode(',',$one[0]['attribute']);
		$i = 0;
		foreach ($aArr as $a) {
			if (in_array($a,$onearr)){
				$attrArr .= '　　<input type="checkbox" checked="checked" name="attribute[]" value="'.$a.'"/>　'.$a;
			}else{
				$attrArr .= '　　<input type="checkbox" name="attribute[]" value="'.$aArr[$i].'"/>　'.$aArr[$i];
			}
			$i++;
		}
		update();
		break;
}

function add(){
	global $contentModel;
	if($_POST['send']){
		$contentModel->title = $_POST['title'];
		$contentModel->column_id = $_POST['column_id'];
		$contentModel->color = $_POST['color'];
		$contentModel->comment = $_POST['comment'];
		$contentModel->attribute = implode(',', $_POST['attribute']);
		$contentModel->tag = $_POST['tag'];
		$contentModel->thumb = UploadTool::up('thumb');
		$contentModel->author = $_POST['author'];
		$contentModel->description = $_POST['description'];
		$contentModel->content = $_POST['content'];
		$contentModel->source = $_POST['source'];
		$contentModel->time = time();
		//$contentModel->user = $_POST['user'];
		$contentModel->is_show = $_POST['is_show'];

		if ($contentModel->addContent()) {
			echo '添加成功！';
		}
	}
}


function update(){
	if($_POST['send']){
		$contentModel->title = $_POST['title'];
		$contentModel->column_id = $_POST['column_id'];
		$contentModel->color = $_POST['color'];
		$contentModel->comment = $_POST['comment'];
		$contentModel->attribute = $_POST['attribute'];
		$contentModel->tag = $_POST['tag'];
		$contentModel->thumb = $_POST['thumb'];
		$contentModel->author = $_POST['author'];
		$contentModel->description = $_POST['description'];
		$contentModel->content = $_POST['content'];
		$contentModel->source = $_POST['source'];
		$contentModel->time = time();
		//$contentModel->user = $_POST['user'];
		$contentModel->is_show = $_POST['is_show'];
		if ($contentModel->updateContent()) {
			echo '修改成功！';
		}
	}

}

function getTreeColumn($id = 0,$level = 0){

	global $navModel;
	$data = $navModel->showColumn();
	$tree = array();
	foreach($data as $v){
		if ($v['pid'] == $id) {
			$v['level'] = $level;
			$v['treename'] =str_repeat('　　', $level).$v['name'];
			$tree[] = $v;
			$tree = array_merge($tree,getTreeColumn($v['id'],$level+1));
		}
	}
	return $tree;
}

include TEMP_DIR.'admin/'.ADMIN_THEME.'/content.html';
?>