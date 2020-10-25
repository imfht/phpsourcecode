<?php
namespace Admin\Controller;
use Think\Controller;
	class CommonController extends Controller {
		public function __construct(){
			Controller::__construct();
			if(!isset($_SESSION['isLogin'])||$_SESSION['isLogin']!=1){
				$this->redirect('login/index');
			}
			
		}		
	}