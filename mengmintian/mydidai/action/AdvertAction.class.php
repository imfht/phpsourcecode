<?php
class AdvertAction extends Action{
	public $tpl;
	public $model;

	
	public function __construct(&$tpl){
		$this->model = new AdvertModel();
		$this->tpl = $tpl;
		$this->action();
	}


	public function action(){

		switch($_GET['action']){
			case 'showAdvert':
				$this->showAdvert();
				break;
			case 'addAdvert':
				$this->addAdvert();
				break;
			case 'deleteAdvert':
				$this->deleteAdvert();
				break;
			case 'updateAdvert':
				$this->updateAdvert();
				break;
		}
	}

	private function showAdvert(){

	}

	private function deleteAdvert(){

	}


                    private function addAdvert(){

                    }


	public function updateAdvert(){
                    
	}


}

?>