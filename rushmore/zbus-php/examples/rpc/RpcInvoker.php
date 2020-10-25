<?php 

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
    