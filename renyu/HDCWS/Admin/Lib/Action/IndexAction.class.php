<?php

class IndexAction extends Action {
    
	public function index(){
		
		$appData = json_encode(array(
		
			'root' => __ROOT__ . '\/',
		
			'desktop' => C('HD_Desktop_Bg')

		));
		
		$this -> assign('appData', $appData);

		$this -> display();
	
	}

}