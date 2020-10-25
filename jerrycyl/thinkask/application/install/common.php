<?php
/*
*	测试是否可写
*	rainfer <81818832@qq.com>
*/
function testwrite($d) {
    $tfile = "_test.txt";
    $fp = @fopen($d . "/" . $tfile, "w");
    if (!$fp) {
        return false;
    }
    fclose($fp);
    $rs = @unlink($d . "/" . $tfile);
    if ($rs) {
        return true;
    }
    return false;
}
/*
*	建立文件夹
*	rainfer <81818832@qq.com>
*/
function create_dir($path) {
    if (is_dir($path))
        return true;
    $path = dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir))
            continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}
/*
*	返回路径
*	rainfer <81818832@qq.com>
*/
function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}
/*
*	执行sql文件
*	rainfer <81818832@qq.com>
*/
function execute_sql($db,$file,$tablepre){
    //读取SQL文件
    $sql = file_get_contents(ROOT_PATH.'public/data/'.$file);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    //替换表前缀
    $default_tablepre = "thinkask_";
    $sql = str_replace(" `{$default_tablepre}", " `{$tablepre}", $sql);
    //开始安装
    showmsg('开始安装数据库...');
    foreach ($sql as $item) {
        $item = trim($item);
        if(empty($item)) continue;
        preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
        if($matches) {
            $table_name = $matches[1];
            $msg  = "创建数据表{$table_name}";
            if(false !== $db->exec($item)){
                showmsg($msg . ' 完成');
            } else {
                session('error', true);
                showmsg($msg . ' 失败！', 'error');
            }
        } else {
            $db->exec($item);
        }
    
    }
}
/*
*	更新系统设置
*	rainfer <81818832@qq.com>
*/
function update_site_configs($db,$table_prefix){
    $sitename=input("sitename");
    $email=input("manager_email");
    $siteurl=input("siteurl");
    $seo_keywords=input("sitekeywords");
    $seo_description=input("siteinfo");
    $site_options=<<<helllo
            {
            		"site_name":"$sitename",
					"site_host":"$siteurl",
					"site_tpl":"default",
					"site_icp":"",
					"site_tongji":"",
					"site_copyright":"",
					"site_co_name":"",
					"site_address":"",
					"map_lat":"23.029759",
					"map_lng":"113.752114",
					"site_tel":"+86 769 8888 8888",
					"site_admin_email":"$email",
					"site_qq":"81818832",
					"site_seo_title":"$sitename",
					"site_seo_keywords":"$seo_keywords",
					"site_seo_description":"$seo_description",
					"site_logo":"http:\\/\\/ohjmksy46.bkt.clouddn.com\\/image\\/iw7sxvxs_6n9tgd6cbu4o58417156d5943.png"
        }
		
helllo;
    $sql="INSERT INTO `{$table_prefix}options` (option_value,option_name) VALUES ('$site_options','site_options')";
    $db->exec($sql);
    $sql="INSERT INTO `{$table_prefix}options` (option_value,option_name,option_l) VALUES ('$site_options','site_options','en-us')";
    $db->exec($sql);
    showmsg("网站信息配置成功!");
}
/*
*	创建管理员
*	rainfer <81818832@qq.com>
*/
function create_admin_account($db,$table_prefix){
    $username=input("manager");
	$admin_pwd_salt=random(10);
    $password=compile_password(input("manager_pwd"),$admin_pwd_salt);
    $email=input("manager_email");
    $create_date=time();
    $sql =<<<hello
    INSERT INTO `{$table_prefix}users` 
    ( user_name, password, salt, email, reg_time,group_id) VALUES
    ('{$username}', '{$password}','{$admin_pwd_salt}','{$email}', {$create_date},1);
	
hello;
    $re = $db->exec($sql);
    // show($re);
    showmsg("管理员账号创建成功!");
}


/*
*	写入配置
*	rainfer <81818832@qq.com>
*/
function create_config($config){
    if(is_array($config)){
        //读取配置内容
        $conf = file_get_contents(ROOT_PATH. 'public/data/database.php');
        //替换配置项
        foreach ($config as $key => $value) {
            $conf = str_replace("#{$key}#", $value, $conf);
        }
        $re = file_put_contents(APP_PATH. '/database.php', $conf);
        //写入应用配置文件
        if(file_put_contents(APP_PATH. '/database.php', $conf)){
            showmsg('配置文件写入成功');
        } else {
            session('error', true);
            showmsg('配置文件写入失败！', 'error');
        }
        return '';
    }
}