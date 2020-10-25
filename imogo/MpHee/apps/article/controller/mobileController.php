<?php
class mobileController extends baseController{
	protected $layout = 'layout';
	
	public function show()
	{
		$id = $_GET["id"];
		$this->info = model("article")->info("id", $id);
		$this->ppacountinfo = model("article")->ppacountinfo($this->info['ppid']);
		$this->display();
	}
	
}