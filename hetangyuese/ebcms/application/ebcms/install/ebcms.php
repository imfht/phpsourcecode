<?php 
if (!defined('THINK_PATH')) exit();

/**
 * 
 * 温馨提醒
 * 
 * 开发助手提供四个方法管理数据
 * 1.\ebcms\Install::exports('模块英文名称') 打包数据 （配置 权限节点 表单 菜单）
 * 2.\ebcms\Install::imports('模块英文名称') 导入打包的数据 （配置 权限节点 表单 菜单）
 * 3.\ebcms\Install::delete('模块英文名称')  删除数据库中相关数据 （配置 权限节点 表单 菜单）
 * 4.\ebcms\Install::exec_sql_file('sql文件') 执行sql文件
 * 
 * 通过开发助手导出的相关数据统一存放在 当前应用路径/install/config.dat 里面
 * 安装时 可以通过 \ebcms\Install::imports('模块英文名称') 导入
 * 如果你有自建数据表，可以通过数据库管理软件将数据表导出，并将表前缀替换成{prefix}
 * 然后通过 \ebcms\Install::exec_sql_file('sql文件') 执行
 * 
 * 当然，开发助手提供的方法只是作为参考，如果您不嫌繁琐，您也可以用您自己的方法导入导出数据。
 * 
 */

/**
 * 安装函数
 * @return string|true 返回字符串表示错误信息 true表示安装成功！
 */
function ebcms_install(){
	return true;
}