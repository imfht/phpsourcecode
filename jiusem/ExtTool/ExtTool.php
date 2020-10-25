<?php 
/*
ExtTool.exe源代码
*/
$c = $argv[1]; //控制器
$a = $argv[2]; //模型
$p = @$argv[3]; //参数

$copyright = file_get_contents('D:/ExtTool/copyright.txt');

if($c=='create'){
	if($a=='app'){
		//recurse_copy('D:/tptool/thinkphp','./');
		echo 'no this method.';
	}else if($a=='c'){
		$arr = explode('/',$p);
		$dir = 'controller';
		@mkdir($dir); //创建控制器文件夹
		$fileName = $dir.'/'.$arr[0].'Controller.js';
		$fp = fopen($fileName,'w');
		$tpl = file_get_contents('D:/ExtTool/TplController.js');
		$tpl = str_replace('{NAME}',$arr[0],$tpl);
		fwrite($fp,$tpl);
		fclose($fp);
		echo 'create controller successfully.';
		//创建文件后立即打开
		//system('notepad++.exe '.$fileName,$res);

	}else if($a=='m'){

		$arr = explode('/',$p);
		$dir = 'model';
		@mkdir($dir);
		$fileName = $dir.'/'.$arr[0].'Model.js';
		$fp = fopen($fileName,'w');
		$tpl = file_get_contents('D:/ExtTool/TplModel.js');
		$tpl = str_replace('{NAME}',$arr[0],$tpl);
		//$tpl = str_replace('{copyright}',$copyright,$tpl);
		fwrite($fp,$tpl);
		fclose($fp);
		echo 'create model successfully.';
		//创建文件后立即打开
		//system('notepad++.exe '.$fileName,$res);

	}else if($a=='s'){
		
		$arr = explode('/',$p);
		$dir = 'store';
		@mkdir($dir);
		$fileName = $dir.'/'.$arr[0].'Store.js';
		$fp = fopen($fileName,'w');
		$tpl = file_get_contents('D:/ExtTool/TplStore.js');
		$tpl = str_replace('{NAME}',$arr[0],$tpl);
		$tpl = str_replace('{MODEL}',$arr[0],$tpl);
		fwrite($fp,$tpl);
		fclose($fp);
		echo 'create store successfully.';
	}
	else if($a=='grid' || $a=='window' ){
		$arr = explode('/',$p);
		
		@mkdir('view');//创建view文件夹
		if(count($arr)==2){
			//创建子文件夹
			@mkdir('view/'.$arr[0]);
			$fileName = 'view/'.$arr[0].'/'.$arr[1].'.js';
			$viewName = $arr[0].'.'.$arr[1];
			$xtype = $arr[1];
		}else{
			$fileName = 'view/'.$arr[0].'.js';
			$viewName = $arr[0];
			$xtype = $arr[0];
		}		
		$fp = fopen($fileName,'w');
		
		if($a=='grid'){
			$tplName = 'TplGrid.js';
		}else{
			$tplName = 'TplWindow.js';
		}
		
		$tpl = file_get_contents('D:/ExtTool/'.$tplName);
		$tpl = str_replace('{NAME}',$viewName,$tpl);
		$tpl = str_replace('{XTYPE}',$xtype,$tpl);
		fwrite($fp,$tpl);
		fclose($fp);
		echo 'create view successfully.';
		//创建文件后立即打开
		//system('notepad++.exe '.$fileName,$res);
	}
}

?>