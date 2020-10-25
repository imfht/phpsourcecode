<?php 

require_once '../../zbus.php';

$b = new ClientBootstrap();

$b->ha(false)       
  ->async(true)    //异步模式
  ->serviceName("MyRpc")
  ->serviceAddress("localhost:15555");

$b->run(function ($rpc, $boostrap) {
    
    $rpc->module = "InterfaceExample";
    
    //1) Raw invocation
    $req = new Request("plus", array(1, 2));
    $rpc->invokeAsync($req, function($res){
        echo $res . PHP_EOL;
    });
        
    //2) strong typed
    $rpc->plus(1, 2,  
        
        function($res) use($boostrap){ //成功
            echo $res . PHP_EOL;
            
            $boostrap->close();  //需要在合适的地方关闭资源
        }, 
        
        function($err){ //失败
            
        }
    );   
});
     