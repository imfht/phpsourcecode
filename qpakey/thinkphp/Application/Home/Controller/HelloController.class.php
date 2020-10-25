<?php
class HelloController extends Controller{
	public function index($name="ThinkPHP")
	{
		$this->hello    =   'Hello,'.$name.'！';
		$this->display();
	}
}