<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 处理后台首页
 */
class IndexController extends CommonController
{
	protected function _initialize()
	{
		parent::_initialize();
        $this->bcItemPush('服务器信息', U('Index/index'));
	}

    public function index(){
    	mysql_connect(C('DB_HOST'), C('DB_USER'), C('DB_PWD'));	//root链接数据库来提权读取版本信息

    	$info = array(
            'ThinkPHP版本'	=>	THINK_VERSION,
            '操作系统'		=>	PHP_OS,
            '服务器环境'		=>	$_SERVER["SERVER_SOFTWARE"],
            'PHP环境'		=>	PHP_VERSION.'/'.php_sapi_name(),
            'MySQL版本'		=>	mysql_get_server_info(),
            '主机信息'		=>	$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].' '.$_SERVER['SERVER_PROTOCOL'],
            'WEB目录'		=>	$_SERVER["DOCUMENT_ROOT"],
            '服务器域名/IP'	=>	$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '服务器时间'	=>	date("Y年n月j日 H:i:s"),
            '北京时间'		=>	gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '执行时间限制'	=>	ini_get('max_execution_time').'秒',
            '上传附件限制'	=>	ini_get('upload_max_filesize'),
            '剩余空间'		=>	round((disk_free_space(".")/(1024*1024)),2).'M',
        );

        $this->assign('info', $info);

        $this->display();
    }
    
}
?>