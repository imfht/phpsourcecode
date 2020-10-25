                /\\\                                                            /\\\                               
                \/\\\                                                           \/\\\                              
                 \/\\\                                               /\\\\\\\\\  \/\\\           /\\\\\\\\\        
     /\\\\\\\\\\\ \/\\\         /\\\    /\\\  /\\\\\\\\\\            /\\\/////\\\ \/\\\          /\\\/////\\\      
     \///////\\\/  \/\\\\\\\\\  \/\\\   \/\\\ \/\\\//////            \/\\\\\\\\\\  \/\\\\\\\\\\  \/\\\\\\\\\\      
           /\\\/    \/\\\////\\\ \/\\\   \/\\\ \/\\\\\\\\\\           \/\\\//////   \/\\\/////\\\ \/\\\//////      
          /\\\/      \/\\\  \/\\\ \/\\\   \/\\\ \////////\\\           \/\\\         \/\\\   \/\\\ \/\\\           
         /\\\\\\\\\\\ \/\\\\\\\\\  \//\\\\\\\\\   /\\\\\\\\\\           \/\\\         \/\\\   \/\\\ \/\\\          
         \///////////  \/////////    \/////////   \//////////            \///          \///    \///  \///          


zbus strives to make Message Queue and Remote Procedure Call fast, light-weighted and easy to build your own service-oriented architecture for many different platforms. Simply put, zbus = mq + rpc.

zbus carefully designed on its protocol and components to embrace KISS(Keep It Simple and Stupid) principle, but in all it delivers power and elasticity. 

- Working as MQ, compare it to RabbitMQ, ActiveMQ.
- Working as RPC, compare it to many more.

# zbus-php-client
NO threading in PHP client, HA not ready yet! Pull request if you are interested.

- Works for both PHP5(crack on Throwable?) and PHP7
- zbus.php is the only source file required


## API Demo

Only demos the gist of API, more configurable usage calls for your further interest.

### Produce message

    function biz($broker){ 
		$producer = new Producer($broker);
		$msg = new Message();
		$msg->url = '/abc';
		$msg->topic = 'MyTopic';
		$msg->tag = 'Stock.A.中文';
		$msg->body = 'From PHP sync 中文';
		
		$res = $producer->publish($msg);
		echo $res . PHP_EOL;
	}
	
	
	$loop = new EventLoop(); 
	$broker = new Broker($loop, "localhost:15555;localhost:15556", true); // enable sync mode
	
	$broker->on('ready', function() use($loop, $broker){  
		//run after ready
		try {  biz($broker); } catch (Exception $e){ echo $e->getMessage() . PHP_EOL; }
		
		$broker->close();  
		$loop->stop(); //stop anyway
	}); 
	
	$loop->runOnce();

### Consume message

    $messageHandler = function($msg, $client){ //where you should focus on 
	    echo $msg->tag . PHP_EOL;
		echo $msg . PHP_EOL;
	};
	
	
	Logger::$Level = Logger::DEBUG; //change to info to disable verbose message
	$loop = new EventLoop(); 
	$broker = new Broker($loop, "localhost:15555;localhost:15556"); //HA, test it?!
	 
	
	$ctrl = new ControlHeaders();
	$ctrl->topic = "MyTopic"; 
	$ctrl->topic_mask = Protocol::MASK_DISK;
	//$ctrl->group_name_auto = true;
	//$ctrl->consume_group = "MyTopic_Group12";
	//$ctrl->group_mask = Protocol::MASK_DISK;
	//$ctrl->group_filter = "abc.*";  
	
	
	$c = new Consumer($broker, $ctrl);  
	$c->connectionCount = 1;
	$c->messageHandler = $messageHandler; 
	
	$c->start();  
	$loop->run();

### RPC client

    require_once '../../zbus.php';

	$b = new ClientBootstrap();
	
	$b->ha(false)    //直连模式
	->async(false)
	->serviceName("MyRpc")
	->serviceAddress("localhost:15555");//当HA使能时，地址解释为Tracker/NameServer
	
	$b->run(function ($rpc, $boostrap) {
	    
	    $rpc->module = "InterfaceExample";
	    
	    // 1)弱类型调用
	    $req = new Request("plus", array( 1, 2 ));  //构造出请求，方法+参数+【模块】
	    $res = $rpc->invoke($req);
	    echo $res . PHP_EOL;
	    
	    // 2)强类型调用
	    $res = $rpc->plus(1, 2);
	    echo $res . PHP_EOL;
	    
	    $res = $rpc->testEncoding();
	    echo $res . PHP_EOL;
	    
	    $res = $rpc->noReturn();
	    echo $res . PHP_EOL;
	    
	    try {
	        $rpc->throwException();
	    } catch (Exception $e){
	        error_log($e);
	    }
	    
	    
	    $boostrap->close(); //
	});

### RPC service

    
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