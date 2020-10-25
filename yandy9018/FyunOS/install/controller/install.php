<?php
class ControllerInstall extends Controller {
	public function index() {	
	    $this->children = array(
			'header',
			'footer'
		);
		$this->data['action'] = HTTP_SERVER . 'index.php?route=install/step1&m='.SNAME;
		
		
		$this->template = 'step_0.tpl';
		
		$this->response->setOutput($this->render(TRUE));

	/*	if (!$this->customer->isLogged()) {
	  		die("请您先登录，登陆后购买产品才可安装！");
    	} */
	/*	$url = HTTP_SERVER . 'index.php?route=install/step1';
		echo "<script language='javascript' 
		type='text/javascript'>";  
		echo "window.location.href='$url'";  
		echo "</script>"; */

	}
	public function step1(){
		$this->data['action'] = HTTP_SERVER . 'index.php?route=install/step2&m='.SNAME;
		
		$this->children = array(
			'header',
			'footer'
		);
		
		$this->template = 'step_3.tpl';
		
		$this->response->setOutput($this->render(TRUE));
	
		}
		
	public function step2(){
	        $this->load->model('install');
			
			if(!isset($_GET['wxappid'])){
				$wxappid = '';
				}else{
					$wxappid = $_GET['wxappid'];
			}
			
			if(!isset($_GET['wxappsecret'])){
				$wxappsecret = '';
				}else{
					$wxappsecret = $_GET['wxappsecret'];
			}
			if(!isset($_GET['wxtoken'])){
				$wxtoken = '';
				}else{
					$wxtoken = $_GET['wxtoken'];
			}
			// $mysqlInfo = array();
			 $mysqlInfo = array(
				'db_name'       => SNAME,
				'username'       => $_GET['username'],          //管理后台用户名
				'password'       => $_GET['password'],           //管理后台密码
				'shopname'       => $_GET['shopname'],           //管理后台密码
				'shoper'       => $_GET['shoper'], 
				'address'       => $_GET['address'], 
				'phone'       => $_GET['phone'], 
				'wxappid'       => $wxappid, 
				'wxappsecret'       => $wxappsecret, 
				'wxtoken'       => $wxtoken
			  );
			  
			$this->model_install->mysql($mysqlInfo);
			
			$this->data['adminAction'] =  HTTP_BAECLOUD . 'admin/?m='.SNAME;
			$this->data['wwwAction'] =  HTTP_BAECLOUD . SNAME;

			$json = array();
			
			$this->children = array(
			  'header',
			  'footer'
			);
			
			$this->template = 'step_4.tpl';
			
			$json['output'] = $this->render(); 
			
			$this->load->library('json');
	
			$this->response->setOutput(Json::encode($json));
			
		}
}
?>