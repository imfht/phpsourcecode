<?php
namespace app\common\upgrade;
use think\Db;

class U26{
	public function up(){
	    $id = Db::name('module')->where('keywords','cms')->value('id');
	    $gid = Db::name('config_group')->where('sys_id',$id)->value('id');
	    into_sql("INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, $gid, '发表修改主题使用辅栏目的用户组', 'post_use_category', '3', 'usergroup2', '', 0, '', '', 0, $id);",true,0);
	}
}