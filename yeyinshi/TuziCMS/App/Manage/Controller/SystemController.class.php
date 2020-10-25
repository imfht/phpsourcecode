<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Manage\Controller;
use Think\Controller;
class SystemController extends CommonController {
	/**
	 * 网站配置信息展示
	 */
    public function index(){
    	$m=M('Config');
    	$arr=$m->select();
    	//dump($arr);
    	
    	$this->assign('data',$arr);//获取登录管理员的上次登录时间，交给变量time显示
    	$this->assign('config_webname',$arr[0]['config_webname']);
    	$this->assign('config_weburl',$arr[0]['config_weburl']);
    	$this->assign('config_webtitle',$arr[0]['config_webtitle']);
    	$this->assign('config_webname',$arr[0]['config_webname']);
    	$this->assign('config_webkw',$arr[0]['config_webkw']);
    	$this->assign('config_cp',$arr[0]['config_cp']);
    	$this->assign('config_address',$arr[0]['config_address']);
    	$this->assign('config_qq',$arr[0]['config_qq']);
    	$this->assign('config_email',$arr[0]['config_email']);
    	$this->assign('config_powerby',$arr[0]['config_powerby']);
    	$this->assign('config_name',$arr[0]['config_name']);
    	$this->assign('config_tel',$arr[0]['config_tel']);
    	$this->assign('config_icp',$arr[0]['config_icp']);
    	$this->assign('config_company',$arr[0]['config_company']);
    	$this->assign('config_weburl',$arr[0]['config_weburl']);
    	$this->assign('id',$arr[0]['id']);
		$this->display();
		
    }
    /**
     * modify_system方法
     * 修改网站配置
     */
    public function modify_system(){
//     	dump($_POST);
//     	exit;
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	//写入到数据库中
        $m=D('Config'); //读取Message表的model模型文件MeesageModel.class.php    	
        if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	
    	//**需要另外添加到数据库的在这里填写 
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库	
    	//$m->uid=$_SESSION['id'];
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	
    	$arr=$m->save(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('修改成功',U('System/index'));
    	}else {
    		$this->error('修改失败');
    		//$this->error($m->geterror());
    	}

    }
    
    /**
     * 显示电脑版缓存开关
     */
    public function pcruntime() {
    	$this->display();	 
    }
    
    /**
     * 处理电脑版缓存开关
     */
    public function do_pcruntime() {
    	//定义配置文件的路径
    	$setfile=CONF_PATH."config_pctime.php";
//     	dump($setfile);
//     	exit;
    	if (C('HTML_CACHE_ON__HOME')){
    		$a=array(
    		'HTML_CACHE_ON__HOME' => 'true',//将配置参数HTML_CACHE_ON的内容赋值给$a
    		);
    	}else {
    		 $a=array(
    		'HTML_CACHE_ON__HOME' => 'false',//将配置参数HTML_CACHE_ON的内容赋值给$a
    		);
    	}

//     	dump($a);
//     	exit;
    	$b=array(
    			'HTML_CACHE_ON__HOME' => I('post.HTML_CACHE_ON__HOME'),
    	);
//     	dump($b);
//     	exit;
    	//这里将新的参数值，通过后台的表单提交过来
    	$c=array_merge($a,$b);
//     	dump($c);
//     	exit;
		
    	//首页缓存参数
    	$arr=C('HTML_TIME_INDEX__HOME');
    	$cache_index=array(
    			'HTML_TIME_INDEX__HOME' => $arr,
    	);


    	$cache_index_post=array(
    			'HTML_TIME_INDEX__HOME' => I('post.HTML_TIME_INDEX__HOME'),
    	);
    	
		//合并数组
       $cache_index_ok=array_merge($cache_index,$cache_index_post);

       
       //栏目缓存参数
       $arr=C('HTML_TIME_GROUP__HOME');
       $cache_group=array(
       		'HTML_TIME_GROUP__HOME' => $arr,
       );

       
       $cache_group_post=array(
       		'HTML_TIME_GROUP__HOME' => I('post.HTML_TIME_GROUP__HOME'),
       );
       
       //合并数组
       $cache_group_ok=array_merge($cache_group,$cache_group_post);
       
       
       //文章缓存参数
       $arr=C('HTML_TIME_DETAIL__HOME');
       $cache_detail=array(
       		'HTML_TIME_DETAIL__HOME' => $arr,
       );

       
       $cache_detail_post=array(
       		'HTML_TIME_DETAIL__HOME' => I('post.HTML_TIME_DETAIL__HOME'),
       );
       
       //合并数组
       $cache_detail_ok=array_merge($cache_detail,$cache_detail_post);

    	//将配置文件的结构列出来，然后把要修改的参数用变量代替，执行覆盖操作即可。
    	$settingstr="<?php \n return array(\n";
    	foreach($c as $key=>$v){
    		$settingstr.= "\t'".$key."'=>".$v.",\n";
    	}
    	
    	foreach($cache_index_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	foreach($cache_group_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	foreach($cache_detail_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}	
    	
    	$settingstr.="\n);\n?>\n";
    	
//     	dump($settingstr);
//     	exit;
    	
    	if (file_put_contents($setfile,$settingstr)){//通过file_put_contents保存setting.config.php文件
    		$this->success('修改成功');
    	}else {
    		$this->error('修改失败,请修改要更改文件的权限！');
    	}
 
    }
    
    
    
    /**
     * 显示手机版缓存开关
     */
    public function mbruntime() {
    	$this->display();
    }
    
    /**
     * 处理手机版缓存开关
     */
    public function do_mbruntime() {
    	//定义配置文件的路径
    	$setfile=CONF_PATH."config_mbtime.php";
    	//     	dump($setfile);
    	//     	exit;
    	if (C('HTML_CACHE_ON__MOBILE')){
    		$a=array(
    				'HTML_CACHE_ON__MOBILE' => 'true',//将配置参数HTML_CACHE_ON的内容赋值给$a
    		);
    	}else {
    		$a=array(
    				'HTML_CACHE_ON__MOBILE' => 'false',//将配置参数HTML_CACHE_ON的内容赋值给$a
    		);
    	}
    	
    	//     	dump($a);
    	//     	exit;
    	$b=array(
    			'HTML_CACHE_ON__MOBILE' => I('post.HTML_CACHE_ON__MOBILE'),
    	);
    	//     	dump($b);
    	//     	exit;
    	//这里将新的参数值，通过后台的表单提交过来
    	$c=array_merge($a,$b);
    	//     	dump($c);
    	//     	exit;
    
    	//首页缓存参数
    	$arr=C('HTML_TIME_INDEX__MOBILE');
    	$cache_index=array(
    			'HTML_TIME_INDEX__MOBILE' => $arr,
    	);

    	$cache_index_post=array(
    			'HTML_TIME_INDEX__MOBILE' => I('post.HTML_TIME_INDEX__MOBILE'),
    	);
    
    	//合并数组
    	$cache_index_ok=array_merge($cache_index,$cache_index_post);
    
    	//栏目缓存参数
    	$arr=C('HTML_TIME_GROUP__MOBILE');
    	$cache_group=array(
    			'HTML_TIME_GROUP__MOBILE' => $arr,
    	);
    	
    	$cache_group_post=array(
    			'HTML_TIME_GROUP__MOBILE' => I('post.HTML_TIME_GROUP__MOBILE'),
    	);
    
    	//合并数组
    	$cache_group_ok=array_merge($cache_group,$cache_group_post);
    	 
    	//文章缓存参数
    	$arr=C('HTML_TIME_DETAIL__MOBILE');
    	$cache_detail=array(
    			'HTML_TIME_DETAIL__MOBILE' => $arr,
    	);
    
    	$cache_detail_post=array(
    			'HTML_TIME_DETAIL__MOBILE' => $_POST['HTML_TIME_DETAIL__MOBILE'],
    	);
    
    	//合并数组
    	$cache_detail_ok=array_merge($cache_detail,$cache_detail_post);
    
    	//将配置文件的结构列出来，然后把要修改的参数用变量代替，执行覆盖操作即可。
    	$settingstr="<?php \n return array(\n";
    	foreach($c as $key=>$v){
    		$settingstr.= "\t'".$key."'=>".$v.",\n";
    	}
    	 
    	foreach($cache_index_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	foreach($cache_group_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	foreach($cache_detail_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	 
    	$settingstr.="\n);\n?>\n";
    	 
//     	    	dump($settingstr);
//     	    	exit;
    	 
    	if (file_put_contents($setfile,$settingstr)){//通过file_put_contents保存setting.config.php文件
    		$this->success('修改成功');
    	}else {
    		$this->error('修改失败,请修改要更改文件的权限！');
    	}
    
    }
    
    /**
     * setting展示
     */
    public function setting() {
    	$this->display();
    	 
    }
    /**
     * 处理setting
     */
    public function do_setting() {
//     	dump($_POST);
//     	exit;
    	//setting.config.php文件的路径，通过settingfile_path来设定；
    	$setfile=CONF_PATH."config_setting.php";
//     	dump($setfile);
//     	exit;
    	$a=C('setting');  //将默认配置参数的内容赋值给$a;
//     	dump($a);
//     	exit;
    	$b=array(
    			'tel' => $_POST['tel'],
    			'qq' => $_POST['qq'],
    	);
    	//dump($b);
    	//exit;
    	//这里将新的参数值，通过后台的表单提交过来
    	$c=array_merge($a,$b);//把两个数组合并为一个数组
//     	dump($c);
//     	exit;
    	//将配置文件的结构列出来，然后把要修改的参数用变量代替，执行覆盖操作即可。
		$settingstr="<?php \n return array(\n'Setting' =>array(\n";
		 foreach($c as $key=>$v){
		    $settingstr.= "\t'".$key."'=>'".$v."',\n";
		 }
		$settingstr.="),\n);\n?>\n";
// 		dump($settingstr);
// 		exit;
		if (file_put_contents($setfile,$settingstr)){//通过file_put_contents保存setting.config.php文件
			$this->success('修改成功');
		}else {
			$this->error('修改失败,请修改要更改文件的权限！');
		}
		
    	
    }
    

    
    /**
     * 显示模板管理
     */
    public function moban() {
    	$this->display();	 
    }
    /**
     * 处理模板管理
     */
    public function do_moban() {
//     	dump($_POST);
//     	exit;
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	//setting.config.php文件的路径，通过settingfile_path来设定；
    	$setfile=CONF_PATH."config_theme.php";
//     	dump($setfile);
//     	exit;

    	$a=array(
    			'DEFAULT_THEME__HOME' => C('DEFAULT_THEME__HOME'),
    			'DEFAULT_THEME__MOBILE' => C('DEFAULT_THEME__MOBILE'),	
    			
    	);
//     	dump($a);
//     	exit;
		//判断目录是否存在，不存在则不让修改。目的在于手动创建目录中才可以修改
    	$theme_home=$_POST['DEFAULT_THEME__HOME'];
    	$theme_mobile=$_POST['DEFAULT_THEME__MOBILE'];
    	$dir_home='./'.'Public'.'/'.'Home'.'/'.$theme_home;
    	$dir_mobile='./'.'Public'.'/'.'Mobile'.'/'.$theme_mobile;
//     	    	dump($dir);
//     	    	exit;
    	if (!is_dir($dir_home)){
    		$this->error($dir_home.'不存在!'.'<br>'.'请先创建模板风格目录!','moban',4);
    		//echo $dir.'<br>'.'模板风格目录不存在,请先创建！';
    	}
    	if (!is_dir($dir_mobile)){
    		$this->error($dir_mobile.'不存在!'.'<br>'.'请先创建模板风格目录!','moban',4);
    		//echo $dir.'<br>'.'模板风格目录不存在,请先创建！';
    	}
    	
    	
    	$b=array(
    			'DEFAULT_THEME__HOME' => $_POST['DEFAULT_THEME__HOME'],
    			'DEFAULT_THEME__MOBILE' => $_POST['DEFAULT_THEME__MOBILE'],
    	);
//     	dump($b);
//     	exit;
    	//这里将新的参数值，通过后台的表单提交过来
    	$c=array_merge($a,$b);//把两个数组合并为一个数组
//     	dump($c);
//     	exit;
    	//将配置文件的结构列出来，然后把要修改的参数用变量代替，执行覆盖操作即可。
		$settingstr="<?php \n return array(\n";
		 foreach($c as $key=>$v){
		    $settingstr.= "\t'".$key."'=>'".$v."',\n";
		 }
		$settingstr.="'DEFAULT_THEME__MANAGE'=>'Default',";
		$settingstr.="\n);\n?>\n";
// 		dump($settingstr);
// 		exit;
		
		if (file_put_contents($setfile,$settingstr)){//通过file_put_contents保存setting.config.php文件
			$this->success('修改成功');
		}else {
			$this->error('修改失败,请修改要更改文件的权限！');
		}
    }
    
    /**
     * 显示清理Runtime缓存
     */
    public function clearRuntime() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    
    /**
     * 清理缓存方法(动态缓存)
     */  
    public function do_clearRuntime($dellog = false) {
    	header("Content-Type:text/html; charset=utf-8");//不然返回中文乱码
    
    	//清除缓存
    	is_dir(DATA_PATH . '_fields/') && del_dir_file(DATA_PATH . '_fields/', false);
    	is_dir(CACHE_PATH) && del_dir_file(CACHE_PATH, false);//模板缓存（混编后的）
    	echo ('<p>清除Cache缓存成功!</p>');
    	
    	is_dir(DATA_PATH) && del_dir_file(DATA_PATH, false);//项目数据（当使用快速缓存函数F的时候，缓存的数据）
    	echo ('<p>清除Data缓存成功!</p>');
    	
    	is_dir(TEMP_PATH) && del_dir_file(TEMP_PATH, false);//项目缓存（当S方法缓存类型为File的时候，这里每个文件存放的就是缓存的数据）
    	echo ('<p>清除Temp缓存成功!</p>');
    	
    	is_dir(LOG_PATH) && del_dir_file(LOG_PATH, false);//项目缓存（当S方法缓存类型为File的时候，这里每个文件存放的就是缓存的数据）
    	echo ('<p>清除Logs缓存成功!</p>');
    	
    	if ($dellog) {
    		is_dir(LOG_PATH) && del_dir_file(LOG_PATH, false);//日志
    	}
    	
    	is_file(RUNTIME_PATH.APP_MODE.'~runtime.php') && @unlink(RUNTIME_PATH.APP_MODE.'~runtime.php');//RUNTIME_FILE
    
    	echo '清除完成';
    }
    
    
    /**
     * 显示清理html缓存
     */
    public function clearhtml() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    	 
    }
    
    /**
     * 清理html缓存方法(静态缓存)
     */
    public function do_clearhtml() {

    	//RUNTIME_FILE常量是入口文件中配置的runtimefile的路径及文件名；
		 if(file_exists(RUNTIME_FILE)){
		    unlink(RUNTIME_FILE); //删除RUNTIME_FILE;
		 }
		 //光删除runtime_file还不够，要清空一下Cache文件夹中的文件。代码如下：
		$cachedir=APP_PATH."/Html/Home/";   //Cache文件的路径
// 		dump($cachedir);
// 		exit;
		 if ($dh = opendir($cachedir)) {     //打开App/Html文件夹
		    while (($file = readdir($dh)) !== false) {    //遍历Cache目录
		    	
// 		    			dump($cachedir.$file);
// 		    			exit;
		              unlink($cachedir.$file);                //删除遍历到的每一个文件；
		    }
		    closedir($dh);
		 }
		 $this->success('清理更新成功');
	    	
    }
    
    
    /**
     * 显示清理首页缓存
     */
    public function clearhome() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    
    
    /**
     * 清理首页缓存方法(静态缓存)
     */
    public function do_clearhome() {
    	//光删除runtime_file还不够，要清空一下Cache文件夹中的文件。代码如下：
    	$cachedir=APP_PATH."/Html/Home/";   //Cache文件的路径
//     			dump($cachedir);
//     			exit;
    	if ($dh = opendir($cachedir)) {     //打开App/Html文件夹
    		while (($file = readdir($dh)) !== false) {    //遍历Cache目录
    			$a=$cachedir.'Home_index_index.html';
    			unlink($a);                //删除首页文件
    		}
    		closedir($dh);
    	}
    	$this->success('清理更新成功');
    
    }
    
    public function cleargroup() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    
    public function do_cleargroup() {
    	$dir=APP_PATH."/Html/Home/";   //Cache文件的路径
    	$list = scandir($dir); // 得到该文件下的所有文件和文件夹
    	foreach($list as $value){//遍历
			 if(strpos($value,'group')){
			 	$value=$dir.$value;
			 	unlink($value);
			 } 
    	}
    	$this->success('清理更新成功');
//     	dump($list);
//     	exit;
    }
    
    public function cleardetail() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    
    public function do_cleardetail() {
    	$dir=APP_PATH."/Html/Home/";   //Cache文件的路径
    	$list = scandir($dir); // 得到该文件下的所有文件和文件夹
    	foreach($list as $value){//遍历
			 if(strpos($value,'detail')){
			 	$value=$dir.$value;
			 	unlink($value);
			 } 
    	}
    	$this->success('清理更新成功');
//     	dump($list);
//     	exit;
    }
    
    public function mclearhtml() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();

    }
    public function do_mclearhtml() {
    	//RUNTIME_FILE常量是入口文件中配置的runtimefile的路径及文件名；
    	if(file_exists(RUNTIME_FILE)){
    		unlink(RUNTIME_FILE); //删除RUNTIME_FILE;
    	}
    	//光删除runtime_file还不够，要清空一下Cache文件夹中的文件。代码如下：
    	$cachedir=APP_PATH."/Html/Mobile/";   //Cache文件的路径
    	// 		dump($cachedir);
    	// 		exit;
    	if ($dh = opendir($cachedir)) {     //打开App/Html文件夹
    		while (($file = readdir($dh)) !== false) {    //遍历Cache目录
    		  
    			// 		    			dump($cachedir.$file);
    			// 		    			exit;
    			unlink($cachedir.$file);                //删除遍历到的每一个文件；
    		}
    		closedir($dh);
    	}
    	$this->success('清理更新成功');
    
    }
    public function mclearhome() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    
    public function do_mclearhome() {
    	//光删除runtime_file还不够，要清空一下Cache文件夹中的文件。代码如下：
    	$cachedir=APP_PATH."/Html/Mobile/";   //Cache文件的路径
    	//     			dump($cachedir);
    	//     			exit;
    	if ($dh = opendir($cachedir)) {     //打开App/Html文件夹
    		while (($file = readdir($dh)) !== false) {    //遍历Cache目录
    			$a=$cachedir.'Mobile_index_index.html';
    			unlink($a);                //删除首页文件
    		}
    		closedir($dh);
    	}
    	$this->success('清理更新成功');
    
    }
    
    public function mcleargroup() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    public function do_mcleargroup() {
    	$dir=APP_PATH."/Html/Mobile/";   //Cache文件的路径
    	$list = scandir($dir); // 得到该文件下的所有文件和文件夹
    	foreach($list as $value){//遍历
    		if(strpos($value,'group')){
    			$value=$dir.$value;
    			unlink($value);
    		}
    	}
    	$this->success('清理更新成功');
    	//     	dump($list);
    	//     	exit;
    }
    
    public function mcleardetail() {
    	$this->assign('module',MODULE_NAME);
    	$this->display();
    }
    
    public function do_mcleardetail() {
    	$dir=APP_PATH."/Html/Mobile/";   //Cache文件的路径
    	$list = scandir($dir); // 得到该文件下的所有文件和文件夹
    	foreach($list as $value){//遍历
    		if(strpos($value,'detail')){
    			$value=$dir.$value;
    			unlink($value);
    		}
    	}
    	$this->success('清理更新成功');
    	//     	dump($list);
    	//     	exit;
    }
    
    
    /**
     * 处理数据分页
     */
    public function do_fenye() {
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	//定义配置文件的路径
    	$setfile=CONF_PATH."config_fenye.php";
    	//     	dump($setfile);
    	//     	exit;
    
    	//文章模型分页参数
    	$arr=C('PAGE_ARTICLE__HOME');
    	$cache_index=array(
    			'PAGE_ARTICLE__HOME' => $arr,
    	);
    
    	$cache_index_post=array(
    			'PAGE_ARTICLE__HOME' => I('post.PAGE_ARTICLE__HOME'),
    	);
    	//合并数组
    	$cache_index_ok=array_merge($cache_index,$cache_index_post);
    
    	//产品模型分页参数
    	$arr=C('PAGE_PRODUCT__HOME');
    	$cache_group=array(
    			'PAGE_PRODUCT__HOME' => $arr,
    	);
    	 
    	$cache_group_post=array(
    			'PAGE_PRODUCT__HOME' => I('post.PAGE_PRODUCT__HOME'),
    	);
    	//合并数组
    	$cache_group_ok=array_merge($cache_group,$cache_group_post);
    
    	//图片模型分页参数
    	$arr=C('PAGE_PHOTO__HOME');
    	$cache_detail=array(
    			'PAGE_PHOTO__HOME' => $arr,
    	);
    
    	$cache_detail_post=array(
    			'PAGE_PHOTO__HOME' => I('post.PAGE_PHOTO__HOME'),
    	);
    	//合并数组
    	$cache_detail_ok=array_merge($cache_detail,$cache_detail_post);
    	
    	
    	
    	//下载模型分页参数
    	$arr=C('PAGE_DOWNLOAD__HOME');
    	$download_detail=array(
    			'PAGE_DOWNLOAD__HOME' => $arr,
    	);
    	
    	$download_detail_post=array(
    			'PAGE_DOWNLOAD__HOME' => I('post.PAGE_DOWNLOAD__HOME'),
    	);
    	//合并数组
    	$download_detail_ok=array_merge($download_detail,$download_detail_post);
    
    	//将配置文件的结构列出来，然后把要修改的参数用变量代替，执行覆盖操作即可。
    	$settingstr="<?php \n return array(\n";
    	foreach($c as $key=>$v){
    		$settingstr.= "\t'".$key."'=>".$v.",\n";
    	}
    
    	foreach($cache_index_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	foreach($cache_group_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	foreach($cache_detail_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    	
    	foreach($download_detail_ok as $key=>$v){
    		$settingstr.= "\n\t'".$key."'=>'".$v."',\n";
    	}
    
    	$settingstr.="\n);\n?>\n";
    
    	//     	    	dump($settingstr);
    	//     	    	exit;
    
    	if (file_put_contents($setfile,$settingstr)){//通过file_put_contents保存setting.config.php文件
    		$this->success('修改成功');
    	}else {
    		$this->error('修改失败,请修改要更改文件的权限！');
    	}
    
    }
  
    
}