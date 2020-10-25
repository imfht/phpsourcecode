<?php
namespace TPHelper\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
    	$apps = array();
        $files = scandir(DATA_PATH);
        foreach ($files as $k) {
            if ($k == '.' || $k == '..' ) continue;
            $k = str_replace("_index.php", "", $k);
            $ap = F($k.'_index');
            if ($ap) $apps[] = $ap;
        }
        $this->assign('apps',$apps);
    	$this->display();
    }

    public function editApp($ap='')
    {
        $app = F($ap.'_index');
        $this->assign('app',$app);
    	$this->display();
    }

    public function updateApp()
    {
    	$post = I('post.');
        extract($post);
        F($appname.'_index',$post);
        $this->success('更新成功',U('Index/index'));
    }

    public function insertApp()
    {
    	$post = I('post.');
        extract($post);
        $app = F($appname.'_index');
        if ($app) {
            $this->error('应用'.$appname.'已存在');
            return;
        }
        F($appname.'_index',$post);
        $this->success('新增成功',U('Index/index'));
    }

    public function deploy($ap = '')
    {
        if (!$ap) return;
        $app = F($ap.'_index');
        if (!$app) return;
        $path = $app['appdir'];
        $APP_PATH = $path.'/App/';
        $COMMON_PATH = $APP_PATH.'/Common/Common/';
        $files  = scandir(COMMON_PATH.'Common/');
        foreach ($files as $k) {
            if ($k == '.' || $k == '..' || $k == 'index.html') continue;
            copy(COMMON_PATH.'Common/'.$k, $COMMON_PATH.$k);
        }

        $files  = scandir(COMMON_PATH.'Controller/');
        $COMMON_PATH = $APP_PATH.'/Common/Controller/';
        if(!is_dir($COMMON_PATH))  mkdir($COMMON_PATH,0777,true);
        foreach ($files as $k) {
            if ($k == '.' || $k == '..' || $k == 'index.html') continue;
            copy(COMMON_PATH.'Controller/'.$k, $COMMON_PATH.$k);
        }
    }




    public function deleteApp($app = '')
    {
        if (!$app) return;
        F($app.'_index',NULL);
    }


    public function testMysql($DB_HOST ='',$DB_USER='',$DB_PWD = '',$DB_NAME = '')
    {
    	$id = mysql_connect($DB_HOST,$DB_USER,$DB_PWD) or $this->error('数据库连接失败');
		$ok = mysql_select_db($DB_NAME,$id) or $this->error('不通选择数据库');
		if($ok){
        	$this->success('选择数据库成功！');
        }else{
        	$this->error('数据库连接失败');
        }
    }
}