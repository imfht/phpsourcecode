# pasync
async in php

## usage

For handler:

	$pms = $_POST;
	$cls = $pms['cls'];
	$act = $pms['act'];
	try{
	    $r = AsyncHandler::handle($cls,$act,$pms);
	}catch (InjectorException $e){
	    //handle the error
	}
	//finished,return
	
For sender:

	//....
	Async::getInstance()->init('localhost',8888,'/handler.php')->send(OperateRecordService::class,'test',['value' => 'pasync']);
	//....
	
The "OperateRecordService::test($value)" in example is a function that does not require real-time but will take a long time to run.So use pasync can make it running async and does not affect the main process.

## requires

require [jesusslim/pinject](https://github.com/jesusslim/pinject)

use pinject to execute function with injector

## install

use composer

for example | composer.json

	{
       "require-dev": {
       	"jesusslim/pasync": "dev-master"
    	}
	}