<?php
class ColumnAction extends Action{
	public $tpl;
	public $model;

	
	public function __construct(&$tpl){
		$this->model = new ColumnModel();
		$this->tpl = $tpl;
		$this->action();
	}


	public function action(){

		switch($_GET['action']){
			case 'showColumn':
				$this->showColumn();
				break;
			case 'addColumn':
				$this->addColumn();
				break;
			case 'deleteColumn':
				$this->deleteColumn();
				break;
			case 'updateColumn':
				$this->updateColumn();
				break;
		}
	}

	private function showColumn(){
	
		$this->tpl->assign('showColumn',true);
		$this->tpl->assign('rs',$this->treeColumn($this->model->showColumn()));
	}

	private function deleteColumn(){
		$this->model->id = $_GET['id'];
		if($this->model->deleteColumn()){
			exit('栏目删除成功！');
		}
	}


	private function addColumn(){
		$this->tpl->assign('rs',$this->treeColumn($this->model->showColumn()));
		if($_POST['send']){
			$this->model->name = $_POST['name'];
			$this->model->info = $_POST['info'];
			$this->model->sort = $_POST['sort'];
			$this->model->is_show = $_POST['is_show'];
			$this->model->pid = $_POST['pid'];
			if(!$this->model->addColumn()){
				exit('栏目添加失败！');
			}else{
				header('Location:./column.php?action=showColumn');
			}
		}

		$this->tpl->assign('addColumn',true);
	}

	public function updateColumn(){
		$this->model->id = $_GET['id'];
		$this->tpl->assign('rs',$this->treeColumn($this->model->showColumn()));
		$one = $this->model->oneColumn();
		$this->tpl->assign('one',$one);
		
		if ($one[0]['is_show']) {
			$str = '<input type="radio" value="1" checked="checked" name="is_show"/>是 <input type="radio" value="0" name="is_show"/>否';
		}else{
			$str = '<input type="radio" value="1" name="is_show"/>是 <input type="radio" value="0" checked="checked" name="is_show"/>否';
		}
		$this->tpl->assign('str',$str);

		if($_POST['send']){
			$this->model->name = $_POST['id'];
			$this->model->name = $_POST['name'];
			$this->model->info = $_POST['info'];
			$this->model->sort = $_POST['sort'];
			$this->model->is_show = $_POST['is_show'];
			$this->model->pid = $_POST['pid'];
			if($this->model->updateColumn()){
				exit('栏目修改成功！');
			}else{
				header('Location:./column.php?action=updateColumn');
			}
		}

		$this->tpl->assign('updateColumn',true);
	}

	public function treeColumn($data,$id = 0,$level = 0){
		$tree = array();
		foreach($data as $v){
			if ($v['pid'] == $id) {
				$v['level'] = $level;
				$v['treename'] =str_repeat('　　', $level).$v['name'];
				$tree[] = $v;
				$tree = array_merge($tree,$this->treeColumn($data,$v['id'],$level+1));
			}
		}
		return $tree;
	}



}

?>