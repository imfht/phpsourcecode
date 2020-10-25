<?php
class IndexController extends Controller{
	public function index()
	{
		$this->lang=LANG_SET;
		$this->display();
	}
}