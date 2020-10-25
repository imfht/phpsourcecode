<?php header("Content-type: text/html; charset=utf-8");
/*	程序升级页面 v15042113 By:shileiye	*/
require_once 'inc/pclzip.class.php';		//载入zip操作类
require_once 'inc/version.php';		//载入版本信息
$uptimes=fopen("inc/uptimes.txt","w" );
fwrite($uptimes,date('Y-m-d',time()));	//记录当前检查升级日期以便进行自动升级控制
fclose($uptimes);
@$uplisttext=fopen($uplisturl."uplist.txt","r");
stream_set_timeout($uplisttext,30);
$upinfo=stream_get_meta_data($uplisttext);
if ($upinfo['timed_out']){
	fclose($uplisttext);
	echo "下载更新列表超时！请稍后再试！";
	exit();
}
if($uplisttext){
	$uplista=array();
	$i=0;
	while (!feof($uplisttext)){
		$line=fgets($uplisttext);
		$uplista[$i]=trim($line);
		$i++;
	}
}else{
	fclose($uplisttext);
	echo "下载更新列表失败！请稍后再试！";
	exit();
}
fclose($uplisttext);
if($uplista[0]<=$isversion){
	echo "已经是最新版本，无须更新！";
	exit();
}else{
	if(@$_GET['m']=="up"){
		for($i=count($uplista)-1;$i>-1;$i--){
			if($uplista[$i]>$isversion){
				echo "开始更新: $uplista[$i].zip<br>";
				loadupdate("$uplisturl$uplista[$i].zip");
			}
		}
	}else{
		echo "程序有以下更新包: <br>";
		for($i=count($uplista)-1;$i>-1;$i--){
			if($uplista[$i]>$isversion){
				echo "文件: $uplista[$i].zip<br>";
			}
		}
		echo "<a href='?m=up'>点击此处一键更新</a>";
	}
}
function loadupdate($upurl){
	$upfolder="uptemp/";	//本地临时升级文件夹
	$bakdir=$upfolder."bak_".basename($upurl);	//备份文件路径
	$updir=$upfolder.basename($upurl);	//远程文件本地路径
	if(!is_file($updir)){	//如果本地不存在升级文件则进行远程下载
		@$upfile=fopen($upurl, "rb");	//打开升级文件
		if ($upfile){
			if(!is_dir($upfolder)){
				mkdir($upfolder,0777);	//创建升级文件夹
			}
			// 获取文件大小
			$filesize=-1;
			$headers = get_headers($upurl, 1);
			if ((!array_key_exists("Content-Length", $headers))){
				 $filesize=0; 
			}
			$filesize= $headers["Content-Length"];
			@$newf = fopen ($updir, "wb");
			$downlen=0;
			if ($newf){
				while(!feof($upfile)) {
					$data=fread($upfile, 1024 * 8 );	//每次获取8K
					$downlen+=strlen($data);	// 累计已经下载的字节数
					fwrite($newf, $data, 1024 * 8 );
					ob_flush();
					flush();
				}
				echo "下载升级文件成功！<br>";
			}else{
				fclose($newf);
				echo "下载升级文件失败！请检查目录权限！<br>";
				exit();
			}
		}else{
			fclose($upfile);
			echo "下载升级文件失败！请检查升级文件路径！<br>".$upurl;
			exit();
		}
		if ($upfile) {
			fclose($upfile);
		}
		if ($newf) {
			fclose($newf);
		}
	}else{
		echo "检测到本地升级文件，使用本地文件升级。<br>";
	}
	//开始备份
	 $upzip = new PclZip($updir);
	$bakzip=new PclZip($bakdir);
	//打开更新包
	if (($list = $upzip->listContent()) == 0) {
		die("打开升级文件出错！Error : ".$upzip->errorInfo(true));
		exit();
	}
	//备份涉及的文件
	$baklist="";
	for ($i=0; $i<sizeof($list); $i++) {
		$bakdirnames=dirname(__FILE__)."\\".$list[$i][key($list[1])];
		if(!preg_match("/\/$/",$bakdirnames)){
			if (is_file($bakdirnames)) {
				$bakdirnames=str_replace('/','\\',$bakdirnames);
				if($i==sizeof($list)-1){
					$baklist.=$bakdirnames;
				}else{
					$baklist.=$bakdirnames.",";
				}
			}
		}
	}
	//执行压缩备份
	if ($bakzip->create($baklist ,PCLZIP_OPT_REMOVE_PATH,dirname(__FILE__)) == 0) {
		die("备份文件出现错误！请手动备份升级！Error : ".$bakzip->errorInfo(true));
		exit();
	 }

	 //开始升级
	$uplist="";
	for ($i=0; $i<sizeof ($list); $i++) {
		$dirnames=dirname(__FILE__)."\\".$list[$i][key($list[1])];
		if(!preg_match("/\/$/",$dirnames)){
			if (is_file($dirnames)) {
				$dirnames=str_replace('/','\\',$dirnames);	
				$uplist.="更新文件：".$dirnames."<br>";
				if(!unlink($dirnames)){
					echo "更新：".$dirnames."文件失败！请检查文件权限！<br>";
				}
			}else{
				$uplist.="新增文件：".$dirnames."<br>";
			}
		}
	}
	//执行解压
	if ($upzip->extract(PCLZIP_OPT_PATH, dirname(__FILE__)) == 0) {
		die("检测升级包出错！Error : ".$upzip->errorInfo(true));
		exit();
	 }else{
		echo "本次更新涉及下列文件：<br>".$uplist."更新完成！<br><br>";
	}
	echo "请检查程序运行是否正常！若出现问题，请使用FTP进入程序目录找到uptemp目录将bak_开头的zip文件解压恢复相应文件即可。";
}
?>