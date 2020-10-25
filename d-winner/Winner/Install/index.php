<?php
/*
 * @varsion		Winner权限管理系统 2.1var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
if(file_exists('lock.txt')){
    echo '系统已安装，请不要重复安装！如需安装，请删除install文件夹下的lock.txt文件。';
    exit();
}else{
	header('Location:install.php');
    exit();
}
