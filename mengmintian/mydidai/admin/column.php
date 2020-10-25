<?php
require '../includes/init.php';
$navModel = new ColumnModel();
switch($_GET['action']){
	case 'showColumn':
		$showColumn = true;
		$rs = treeColumn($navModel->showColumn());
		break;
	case 'addColumn':
		add();
		break;
	case 'deleteColumn':
		$navModel->id = $_GET['id'];
		if($this->model->deleteColumn()){
			exit('栏目删除成功！');
		}
		break;
	case 'updateColumn':
		update();
		$rs =treeColumn($navModel->showColumn());
		$one = $navModel->oneColumn();
		$updateColumn = true;
		break;
}


function add(){
	global $navModel;
	$rs = treeColumn($navModel->showColumn());
	if($_POST['send']){
		$this->model->name = $_POST['name'];
		$this->model->info = $_POST['info'];
		$this->model->sort = $_POST['sort'];
		$this->model->is_show = $_POST['is_show'];
		$this->model->pid = $_POST['pid'];
		if(!$navModel->addColumn()){
			exit('栏目添加失败！');
		}else{
			header('Location:./column.php?action=showColumn');
		}
	}

	$addColumn = true;
}

function update(){
	global $navModel;
	$navModel->id = $_GET['id'];
	
	if ($one[0]['is_show']) {
		$str = '<input type="radio" value="1" checked="checked" name="is_show"/>是 <input type="radio" value="0" name="is_show"/>否';
	}else{
		$str = '<input type="radio" value="1" name="is_show"/>是 <input type="radio" value="0" checked="checked" name="is_show"/>否';
	}

	if($_POST['send']){
		$navModel->name = $_POST['id'];
		$navModel->name = $_POST['name'];
		$navModel->info = $_POST['info'];
		$navModel->sort = $_POST['sort'];
		$navModel->is_show = $_POST['is_show'];
		$navModel->pid = $_POST['pid'];
		if($navModel->updateColumn()){
			exit('栏目修改成功！');
		}else{
			header('Location:./column.php?action=updateColumn');
		}
	}
}


function treeColumn($data,$id = 0,$level = 0){
	$tree = array();
	foreach($data as $v){
		if ($v['pid'] == $id) {
			$v['level'] = $level;
			$v['treename'] =str_repeat('　　', $level).$v['name'];
			$tree[] = $v;
			$tree = array_merge($tree,treeColumn($data,$v['id'],$level+1));
		}
	}
	return $tree;
}

include TEMP_DIR.'admin/'.ADMIN_THEME.'/column.html';
?>