<?php
namespace app\common\upgrade;
use think\Db;

class U14{
	public function up(){
		if(!is_file(ROOT_PATH.'application/common/hook/CallMe.php') ){
			return ;
		}
	    //$id = Db::name('hook_plugin')->where('hook_class','like',"app\\\\common\\\hook\\\CallMe")->count('id');
	    into_sql("INSERT INTO `qb_hook_plugin` (`id`, `hook_key`, `plugin_key`, `hook_class`, `about`, `ifopen`, `list`, `author`, `author_url`, `version`, `version_id`) VALUES(0, 'cms_content_show', '', 'app\\common\\hook\\CallMe', '发表主题@某人自动设置为已读标志', 1, 0, '', '', '', 95);",true,0);
	}
}