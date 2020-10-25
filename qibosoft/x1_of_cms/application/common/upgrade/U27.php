<?php
namespace app\common\upgrade;
use think\Db;

class U27{
	public function up(){
	    $id = Db::name('plugin')->where('keywords','marketing')->value('id');
	    $gid = Db::name('config_group')->where('sys_id',-$id)->value('id');
	    into_sql("INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, {$gid}, '提现周期(T+N)', 'getout_rmb_tn', '0', 'number', '', 0, '', '单位是天,0或留空则不限,类似微信商户号,用户收入冻结几天后才能提现,比如商城防止用户申请退货', 0, '-{$id}');",true,0);
	}
}