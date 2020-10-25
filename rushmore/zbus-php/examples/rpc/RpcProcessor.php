<?php  
require_once '../../zbus.php';

//函数返回类型除了Message之外都按JSON格式处理
class MyService {   
	public function echo($msg){
		return $msg . ", From PHP";
	}
	
	public function plus($a, $b) {
	    return $a + $b;
	}  
	
	public function testEncoding() {
		return "中文";
	}
	
	public function noReturn() {
		
	}
	
	public function getUser(){
	    return array("name"=>"Hong", "age"=>"18");
	}
	
	public function getBin(){ 
	    $bytes = array();
	    for($i = 0; $i < 10; $i++){
	        array_push($bytes, 0);
	    } 
	    
	    $string = implode(array_map("chr", $bytes));
	    return base64_encode($string);
	}
	
	public function throwException(){
	    throw new Exception("exception throw!");
	}  
	
	//页面跳转
	public function redirect(){
	    $msg = new Message();
	    $msg->status = 302;
	    $msg->headers['location'] = "/"; 
	    return $msg;
	}
}  



Logger::$Level = Logger::DEBUG;
  
$b = new ServiceBootstrap(); 
$b->addModule("InterfaceExample", new MyService()); //模块标识，URL中体现 

$b->serviceName('MyRpc')
  ->serviceAddress('localhost:15555;localhost:15556')
  ->connectionCount(1)
  ->enableDoc(true)
  ->start();
 