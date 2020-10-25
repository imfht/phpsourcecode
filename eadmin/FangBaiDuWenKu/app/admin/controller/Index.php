<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

/**
 * 首页控制器
 */
class Index extends AdminBase
{

    /**
     * 首页方法
     */
    public function adminindex()
    {
    	$baseUrl = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])).'/';
    	 
    	$root='http://'.$_SERVER['HTTP_HOST'].$baseUrl;
    	$this->assign('root',$root);
       
    	
        
        return $this->fetch('adminindex');
    }
    public function home(){
    	// 获取首页数据
    	$index_data = $this->adminBaseLogic->getIndexData();
    	
    	//$domain=showyourdomain();
    	//$this->assign('domaininfo',$domain);
/*     	if($domain['sqstatus']==0){
    		$m=  file_get_contents('./template/default/index_footer.html');
    		if(strpos($m,'es.imzaker.com')!== false){
    			 
    		}else{
    			file_put_contents('./template/default/index_footer.html','<div class="footer" id="footer"><hr>  <p><a href="http://es.imzaker.com/">EasySNS极简社区</a> 2017 &copy; <a href="http://es.imzaker.com/">Es.imzaker.com</a></p></div>');
    		}
    		 
    	}
    	$this->assign('shouquanname',$domain['msg']); */
    	$domaininfo['version']='1.0.0';
    	$this->assign('shouquanname','未授权');
    	$this->assign('domaininfo',$domaininfo);
    	
    	$this->assign('data', $index_data);
    	return $this->fetch('home');
    }
    public function deal_sql() {
    
    	$path = dirname($_SERVER['SCRIPT_FILENAME']) . '/update/updatedb.php';
    	 
    	if (! file_exists ( $path )) {
    		return json(array('code' => 0, 'msg' => '升级文件不存在，请先把升级文件updatedb.php放置在/update/ 目录下'));
    
    	}
    }
}
