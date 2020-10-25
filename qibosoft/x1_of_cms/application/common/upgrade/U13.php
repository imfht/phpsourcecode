<?php
namespace app\common\upgrade;
use think\Db;

class U13{
	public function up(){
	    $id = Db::name('plugin')->where('keywords','marketing')->value('id');
	    $gid = Db::name('config_group')->where('sys_id',"-{$id}")->value('id');
	    into_sql("INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, '$gid', '是否要求先绑定手机号才能提现', 'getout_need_yzphone', '', 'radio', '0|不强制\r\n1|强制绑定手机号', 0, '', '', 0, '-{$id}');",true,0);
	}
}