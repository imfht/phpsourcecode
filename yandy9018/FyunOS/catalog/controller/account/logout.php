<?php 
class ControllerAccountLogout extends Controller {
	public function index() {
		unset($this->session->data);
		$this->customer->logout();
  	}
}
?>