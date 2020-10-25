<?php
namespace app\common\upgrade;
use think\Db;

class U10{
	public function up(){
		 
	    $info = Db::name('config')->where('c_key','money_ratio')->find();
	    if (empty($info)) {
	        $sysid = Db::name('plugin')->where('keywords','marketing')->value('id');
	        $sysid = "-{$sysid}";
	        $type = Db::name('config_group')->where('sys_id',$sysid)->value('id');
	        into_sql("INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, {$type}, '1块钱兑换多少个积分', 'money_ratio', '10', 'number', '', 0, '', '', 0, '{$sysid}');");
	    }
		 
	}
}