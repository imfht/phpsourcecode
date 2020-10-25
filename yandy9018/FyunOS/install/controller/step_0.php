<?php
class ControllerStep0 extends Controller {
	private $error = array();
	
	public function index() {
		
		$this->data['action'] = HTTP_SERVER . 'index.php?route=step_1';
		
		$this->children = array(
			'header',
			'footer'
		);
		
		$this->template = 'step_0.tpl';
		
		$this->response->setOutput($this->render(TRUE));
	}
	
}
?>