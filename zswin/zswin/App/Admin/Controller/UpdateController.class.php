<?php

namespace Admin\Controller;

/**
 * 在线升级控制器
 */
class UpdateController extends CommonController {
	public function index() {
		// 一键升级功能的目录可写权限判断
		$dirList = array (
				'Addons',
				'App',
				'Public',
				'ThinkPHP',
				'Theme',
				'Uploads' 
		);
		
		$noWritable = array ();
	    foreach ( $dirList as $dir ) {
			$dirPath = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $dir;
			if (is_dir ( $dirPath ) && ! is_writable ( $dirPath )) {
				$noWritable [] = $dir;
			}
		}
		$this->assign ( 'noWritable', $noWritable );
		$url = 'http://www.zswin.cn/index.php?m=Home&c=zswin&a=update_json&version=' . intval ( C ( 'SYSTEM_UPDATRE_VERSION' ) );
		
		$list = zs_get_contents( $url );
		
		
		if($list===false||$list==''){
			$verinfo='无法连接官方升级校验服务器！';
			
		}else{
			$list = json_decode ( $list,true);
			
			if($list['status']==1){
				$verinfo='最新升级版本号为'.$list['ver'].',需要到官方按照版本号下载相应升级包！';
			}else{
				$verinfo='当前为最新版本！不需要升级！';
			}
		}
		
		
		
		$this->assign ( 'verinfo', $verinfo );
		$this->display ();
	}
	function deal_sql() {
		
		$path = dirname($_SERVER['SCRIPT_FILENAME']) . '/update/updatedb.php';
		if (! file_exists ( $path )) {
			$this->mtReturn(300, '升级文件不存在，请先把升级文件updatedb.php放置在/update/ 目录下' );
		}
		
		require_once $path;
		
		
		//$this->ajaxReturn('更新完毕，请清理缓存！');
		
	}


	

	
}
