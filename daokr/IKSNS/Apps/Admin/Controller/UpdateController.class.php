<?php
/*
* @copyright (c) 2012-3000 IKPHP All Rights Reserved
* @author 小麦 改版时间 2014年4月22日 23:24 修改
* @Email:810578553@qq.com
* @IKPHP 新增网站升级功能
*/
namespace Admin\Controller;
use Common\Util\Update;
use Common\Controller\BackendController;

class UpdateController extends BackendController {
	
	protected  $ikphp_updateurl = null;
	
    public function _initialize() {
        parent::_initialize();
        $this->ikphp_updateurl = IKPHP_SITEURL;//IKPHP_SITEURL; 升级地址
        //下载地址地址
        C('IK_UPDATE_URL',IKPHP_UPDATE_URL);
    }
    
    /**
     * 升级首页
     * @param  String  $tables 表名
     * @param  Integer $id     表ID
     * @param  Integer $start  起始行数
     * @author 小麦 <810578553@qq.com>
     */
    public function index() {
		//目录可写权限判断
		$dirList = array (
				'Addons',
				'Apps',
				'Data',
				'Public',
		);
		$noWritable = array();	
	
    	foreach ($dirList as $dir){
			$dirPath = SITE_PATH.'/'.$dir; 
			if(is_dir($dirPath) && !is_writable($dirPath)){
				$noWritable[] = $dir; 
			}
		}
		//当前版本
		$currt_version = 'version.php';		
		if(!is_writable($currt_version)){
			$noWritable[] = 'version.php';
		}
		$this->assign('noWritable', $noWritable);
    	$this->title ( '在线升级' );
    	$this->display();
    }
   
    /**
     * 增加一键升级的功能 暂时不用
     * @author 小麦 <810578553@qq.com>
     */    	
	public function upateall(){
		$versionArr = F ( 'versions', '', IKPHP_DATA . 'update/' );
		$key = 0;
		$packageName = '';
		foreach ($versionArr as $k=>$vo){
			if($vo['status']==2) continue;
			
			if($key==0 || $key>$k){
				$key = $k;
				$packageName = $vo['package'];
			}
		}
		
		if($key!=0){
			session('admin_update_all',true);
			$this->redirect('update/index',array('step'=>'isDownBefore','packageName'=>$packageName,'key'=>$key));
		}else{
			session('admin_update_all',null);
			$this->redirect('update/index');
		}
	}
    /**
     * 查询是否有更新版本
     * @author 小麦 <810578553@qq.com>
     */ 
    public function step01_checkversionbyajax(){
		// 取当前版本号
		$path = IKPHP_DATA . 'update/';
		$versionArr =  F ( 'versions', '', $path ); 
		if (! $versionArr) {
			$versionArr [0] = array ();
		}
		$keyArr = array_keys ( $versionArr ); 
		
		// 取官方最新版本信息
		$url = $this->ikphp_updateurl . '/index.php?app=home&c=notice&a=getVersionInfo'; 
		
		$remote = file_get_contents ( $url ); 
		$remote = json_decode ( $remote, true );  

		$newArr = $this->_getSubByKey($remote, 'id'); 
		
		$diff = array_diff ( $newArr, $keyArr ); 
		
    	foreach ( $diff as $d ) { 
			$list [$d] = $remote [$d];
			$this->_writeVersion ( $d, $remote [$d] );
		}
		//未安装的包
    	foreach ( $versionArr as $k=>$d ) {
			if($k!=0 && $d['status']!=2)
			    $list [$k] = $versionArr [$k];
		}		
	
    	if(isset($_POST['isCheck'])){ 
			echo empty($list) ? 0 : 1;
			exit;
		}
		
		//安装当前的版本
    	$nowkey = 0;
		$title = '';
		foreach ($list as $k=>$vo){ 
			if($nowkey==0 || $nowkey>$k){ 
				$nowkey = $k;  
				$nowtitle = $vo['title'];
				$nowver = $vo['version'];
			}
		}		

		$this->assign ( 'list', $list );
		$this->assign ( 'nowkey', $nowkey );
		$this->assign ( 'nowtitle', $nowtitle );
		$this->assign ( 'nowver', $nowver );
		$this->display ();		
    } 
    /**
     * 判断是否需要自动升级，如果已经手工下载覆盖代码，则不需要
     * @author 小麦 <810578553@qq.com>
     */     
	function step02_isdownbefore(){
		// 更新当前版本为升级中的版本状态 1：为正在更新 2：已经更新 0：未更新
		$this->_updateVersionStatus ( I('get.key') , 1);
				
		$packageName = I('get.packageName');
		//$lockName = IKPHP_DATA .'update/download/'.str_replace('.zip', '.lock', $packageName);
		$lockName = IKPHP_DATA .'update/download/'.$packageName; 
		if(file_exists($lockName)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	function step03_download() {
		header ( "content-Type: text/html; charset=utf-8" );
		
		$packageName = I('get.packageName'); 
		
		$updateClass = new Update();
		
		$packageURL = IKPHP_UPDATE_URL . '/' . $packageName;
		
		$status = $updateClass->downloadFile($packageURL);
		//如果失败 重置key
		if($status == 0){
			$this->_updateVersionStatus ( I('get.key') , 0);	
		}
		echo $status;
	}
	// 下载完成开始解压
	function step04_unzippackage() {
		$updateClass = new Update();
		
		$packageName = I('get.packageName');
		echo $updateClass->unzipPackage($packageName);
	}
	
	function step05_checkfileiswritable(){
		$list = $this->_checkFileIsWritable();
		if(empty($list)){
			echo 1;
			exit;
		}
		
		//删除更新锁
		$packageName = I('get.packageName');
		$lockName = IKPHP_DATA.'update/'.str_replace('.zip', '.lock', $packageName);
		unlink($lockName);
			
		$this->assign('list', $list);
		$this->display();
	}
	//自动覆盖文件
	function step06_overwritten() {
		// 提示需要删除的文件
		$filePath = $targetDir = IKPHP_DATA . 'update/download/unzip/fileForDeleteList.php';
		if (file_exists ( $filePath )) {
			$deleteList = require_once ($filePath);
			foreach ($deleteList as $d){
				unlink ( SITE_PATH.'/'. $d);
			}
			unlink ( $filePath );
		}
		
		// 执行文件替换
		$updateClass = new Update();
		$res = $updateClass->overWrittenFile ();
		if(!empty($res['error'])){
			$this->assign ( 'error', $res ['error'] );
			$this->display ();
		}else{
			echo 1;
		}
	}
    /**
     * 自动更新数据库
     * @author 小麦 <810578553@qq.com>
     */ 	
	function step07_dealsql() {
		//$this->closeSite();
		
		$filePath = $targetDir = IKPHP_DATA . 'update/download/unzip/updateDB.php'; 
		if (! file_exists ( $filePath )) { // 如果本次升级没有数据库的更新，直接返回
			echo 1;
			exit ();
		}
		
		require_once ($filePath);
		updateDB ();
		unlink ( $filePath );
		
		// 数据库验证
		$filePath = $targetDir = IKPHP_DATA . 'update/download/unzip/checkDB.php';
		if (! file_exists ( $filePath )) { // 如果本次升级没有数据库的更新后的验证代码，直接返回
			echo 1;
			exit ();
		}
		
		require_once ($filePath);
		// checkDB方法正常返回1 否则返回异常的说明信息，如：ik_xxx数据表创建不成功
		checkDB ();	
		
		unlink ( $filePath );
		echo 1;
	}
	/**
	 * 升级完成
	 * @author 小麦 <810578553@qq.com>
	 */		
	function step08_finishupate(){
		// 清除缓存
		$this->cleanCache ();
		
		// 开启站点
		//$this->openSite ();
		
		// 更新本地版本号信息
		$this->_updateFinishVersionStatus ();
		
		//如果是一键升级的话
		if(session('admin_update_all')==true){
			echo 1;
		}else{
			echo 0;
		}
	}
	private function _updateFinishVersionStatus() {
		$path = IKPHP_DATA . 'update/';
		$versionArr = $this->_getVersionInfo ( $path );
		
		foreach ( $versionArr as $k => &$vo ) {
			if ($vo ['status'] != 1)
				continue;
			
			$vo ['status'] = 2; // 升级完成的状态]
			$this->_updateVersion(array('version'=>$vo['version'],'release'=>$k));
		}
		
		F ( 'versions', $versionArr, $path );
	}
	//更新主版本version.php
	private function _updateVersion($data){
		$currt_version = 'version.php';

		$new_version = array(
				'IKPHP_VERSION' => $data['version'],
				'IKPHP_RELEASE' => Date('Y-m-d',strtotime($data['release'])),
		);
		
		$this->update_config($new_version, $currt_version);
	}	
	function cleanCache(){
        $obj_dir = new \Org\Util\Dir;
        is_dir(DATA_PATH . '_fields/') && $obj_dir->del(DATA_PATH . '_fields/');
        is_dir(CACHE_PATH) && $obj_dir->delDir(CACHE_PATH);
        is_dir(DATA_PATH) && $obj_dir->del(DATA_PATH);
        is_dir(TEMP_PATH) && $obj_dir->delDir(TEMP_PATH);
        is_dir(LOG_PATH) && $obj_dir->delDir(LOG_PATH);
        @unlink(RUNTIME_FILE);
	}
	/**
	 * 更新版本数据中的状态
	 * @param $key 键值
	 */	
	private function _updateVersionStatus($key, $status) {
		$path = IKPHP_DATA . 'update/';
		$versionArr = $this->_getVersionInfo ( $path ); 
		
		foreach ( $versionArr as $k => &$vo ) {
			if ($k != $key)
				continue;
			
			$vo ['status'] = $status; // 升级中的状态 1：为正在更新 2：已经更新 0：未更新
		}
		
		F ( 'versions', $versionArr, $path );
	} 
	// 写入当前版本信息
	private function _writeVersion($key, $arr) {
		$path = IKPHP_DATA . 'update/';
		$arr ['status'] = 0; // 未升级状态
		
		$versionArr = $this->_getVersionInfo ( $path );
		$versionArr [$key] = $arr;
		
		F ( 'versions', $versionArr, $path );
		
		return $versionArr;
	}
	// 获取当前版本信息
	private function _getVersionInfo($path) {
		$file = $path . 'versions.php';
		
		$versionArr = array (); 
		if (file_exists ( $file )) { 
			$versionArr = F ( 'versions', '', $path );
		}
		
		return $versionArr;
	}	
 
	/**
	 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
	 * @param $pArray 一个二维数组
	 * @param $pKey 数组的键的名称
	 * @return 返回新的一维数组
	 */
	private function _getSubByKey($pArray, $pKey="", $pCondition=""){
	    $result = array();
	    if(is_array($pArray)){
	        foreach($pArray as $temp_array){
	            if(is_object($temp_array)){
	                $temp_array = (array) $temp_array;
	            }
	            if((""!=$pCondition && $temp_array[$pCondition[0]]==$pCondition[1]) || ""==$pCondition) {
	                $result[] = (""==$pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
	            }
	        }
	        return $result;
	    }else{
	        return false;
	    }
	}
	//递归检查文件的可写权限
	private function _checkFileIsWritable($source = '',  $res=array()) {
		if (empty ( $source ))
			$source = IKPHP_DATA . 'update/download/unzip';
	
		$handle = dir ( $source );
		while ( $entry = $handle->read () ) {
			if (($entry != ".") && ($entry != "..")) {
				$file = $source . "/" . $entry;
				if (is_dir ( $file )) {
					$res = $this->_checkFileIsWritable ( $file, $res );
				} else {
					if(!is_writable($file)){
						$res[] = $file;
					}
				}
			}
		}
	
		return $res;
	}	 
}