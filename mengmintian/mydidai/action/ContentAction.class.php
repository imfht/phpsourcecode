<?php
class ContentAction extends Action{
	public $tpl;
	public $model;

	public function __construct(&$tpl){
		$this->model = new ContentModel();
		$this->tpl = $tpl;
		$this->action();
	}


	public function action(){

		switch($_GET['action']){
			case 'showContent':
				$this->showContent();
				break;
			case 'addContent':
				$this->addContent();
				break;
			case 'deleteContent':
				$this->deleteContent();
				break;
			case 'updateContent':
				$this->updateContent();
				break;
		}
	}

	private function showContent(){
	
		$this->tpl->assign('showContent',true);
		$this->tpl->assign('rs',$this->model->showContent());
	}

	private function deleteContent(){
		$this->model->id = $_GET['id'];
		if($this->model->deleteContent()){
			exit('删除成功！');
		}
	}


	private function addContent(){

		foreach ($this->getTreeColumn() as $key => $value) {
			$strtree .= '<option value="'.$value['id'].'">'.$value['treename'].'</option>';
		}
		$this->tpl->assign('rs',$strtree);
		if($_POST['send']){
			$this->model->title = $_POST['title'];
			$this->model->column_id = $_POST['column_id'];
			$this->model->color = $_POST['color'];
			$this->model->comment = $_POST['comment'];
			$this->model->attribute = implode(',', $_POST['attribute']);
			$this->model->tag = $_POST['tag'];
			$this->model->thumb = UploadTool::up('thumb');
			$this->model->author = $_POST['author'];
			$this->model->description = $_POST['description'];
			$this->model->content = $_POST['content'];
			$this->model->source = $_POST['source'];
			$this->model->time = time();
			//$this->model->user = $_POST['user'];
			$this->model->is_show = $_POST['is_show'];

			if ($this->model->addContent()) {
				echo '添加成功！';
			}
		}

		$this->tpl->assign('addContent',true);
	}

	private function updateContent(){

		$this->model->id = $_GET['id'];
		$one = $this->model->oneContent();
		$tree = $this->getTreeColumn();
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

		$this->tpl->assign('treers',$strtree);
		$this->tpl->assign('colorrs',$strcolor);
		$this->tpl->assign('attrrs',$attrArr);
		$this->tpl->assign('one',$one);
		if($_POST['send']){
			$this->model->title = $_POST['title'];
			$this->model->column_id = $_POST['column_id'];
			$this->model->color = $_POST['color'];
			$this->model->comment = $_POST['comment'];
			$this->model->attribute = $_POST['attribute'];
			$this->model->tag = $_POST['tag'];
			$this->model->thumb = $_POST['thumb'];
			$this->model->author = $_POST['author'];
			$this->model->description = $_POST['description'];
			$this->model->content = $_POST['content'];
			$this->model->source = $_POST['source'];
			$this->model->time = time();
			//$this->model->user = $_POST['user'];
			$this->model->is_show = $_POST['is_show'];
			if ($this->model->updateContent()) {
				echo '修改成功！';
			}
		}

		$this->tpl->assign('updateContent',true);
	}


		public function getTreeColumn($id = 0,$level = 0){

		$model = new ColumnModel();
		$data = $model->showColumn();
		$tree = array();
		foreach($data as $v){
			if ($v['pid'] == $id) {
				$v['level'] = $level;
				$v['treename'] =str_repeat('　　', $level).$v['name'];
				$tree[] = $v;
				$tree = array_merge($tree,$this->getTreeColumn($v['id'],$level+1));
			}
		}
		return $tree;
	}

}

?>