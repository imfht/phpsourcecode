<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
	
		public function index(){
			//var_dump($video = D('video')->where()->find());
			$this->display();
		}		
		public function top(){

			$this->display();
		}
		public function left(){

			$this->display();
		}
		public function main(){

			$this->display();
		}
}