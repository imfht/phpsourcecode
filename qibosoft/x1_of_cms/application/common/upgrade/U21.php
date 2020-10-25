<?php
namespace app\common\upgrade;

use think\Db;

class U21{
    public static function up(){
	    $id = Db::name('plugin')->where('keywords','weixin')->value('id');
	    $gid = Db::name('config_group')->where('sys_id',-$id)->value('id');
	    into_sql("
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, {$gid}, '微信开放平台移动应用AppID', 'wxopen_appid', '', 'text', '', 1, '', '如果需要申请并愿意付费认证,请点击 <a href=\"https://open.weixin.qq.com/\" target=\"_target\">付费申请</a>，未付费认证的话，就留空。', -2, '-{$id}');
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, {$gid}, '微信开放平台移动应用AppSecret', 'wxopen_appkey', '', 'text', '', 1, '', '没有付费认证的话,就不要填', -2, '-{$id}');
",true,0);
	}
}