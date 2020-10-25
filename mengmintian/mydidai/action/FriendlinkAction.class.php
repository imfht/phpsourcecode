<?php
class FriendlinkAction extends Action{
	public $tpl;
	public $model;

	
	public function __construct(&$tpl){
		$this->model = new FriendlinkModel();
		$this->tpl = $tpl;
		$this->action();
	}


	public function action(){

		switch($_GET['action']){
			case 'showFriendlink':
				$this->showFriendlink();
				break;
			case 'addFriendlink':
				$this->addFriendlink();
				break;
			case 'deleteFriendlink':
				$this->deleteFriendlink();
				break;
			case 'updateFriendlink':
				$this->updateFriendlink();
				break;
		}
	}

	private function showFriendlink(){
       		 $this->tpl->assign('showFriendlink',true);
		$this->tpl->assign('rs',$this->model->showFriendlink());
	}

	private function deleteFriendlink(){
        $this->model->id = $_GET['id'];
        if ($this->model->deleteFriendlink()){
            header('Location:./friendlink.php?action=showFriendlink');
        }
	}


    private function addFriendlink(){
        if ($_POST['send']){
            $this->model->title = $_POST['title'];
            $this->model->url = $_POST['url'];
            $this->model->logo_url = UploadTool::up('logo_url');
            $this->model->sort = $_POST['sort'];
            if(!$this->model->addFriendlink()){
				exit('栏目添加失败！');
			}else{
				header('Location:./friendlink.php?action=showFriendlink');
			}
        }
        $this->tpl->assign('addFriendlink',true);
    }


	public function updateFriendlink(){
        $this->model->id = $_GET['id'];
        $this->tpl->assign('one',$this->model->oneFriendlink());
        if ($_POST['send']){
            $this->model->title = $_POST['title'];
            $this->model->url = $_POST['url'];
            $this->model->logo_url = UploadTool::up('logo_url');
            $this->model->sort = $_POST['sort'];
            if(!$this->model->updateFriendlink()){
				exit('栏目添加失败！');
			}else{
				header('Location:./friendlink.php?action=showFriendlink');
			}
        }
        $this->tpl->assign('updateFriendlink',true);
	}


}

?>