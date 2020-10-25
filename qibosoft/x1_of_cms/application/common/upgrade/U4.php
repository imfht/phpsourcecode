<?php
namespace app\common\upgrade;

class U4{
	public function up(){
	    
		 @unlink(ROOT_PATH.'template/admin_style/default/plugins/weixin/weixin_autoreply/index.htm');		 
		 
		 copy('http://x1.php168.com/public/static/layui/css/modules/layer/default/icon.png',
		         ROOT_PATH.'public/static/layui/css/modules/layer/default/icon.png');
		 
		 copy('http://x1.php168.com/public/static/layui/css/modules/layer/default/icon-ext.png',
		         ROOT_PATH.'public/static/layui/css/modules/layer/default/icon-ext.png');
		 
		 copy('http://x1.php168.com/public/static/layui/css/modules/layer/default/loading-0.gif',
		         ROOT_PATH.'public/static/layui/css/modules/layer/default/loading-0.gif');
		 
		 copy('http://x1.php168.com/public/static/layui/css/modules/layer/default/loading-1.gif',
		         ROOT_PATH.'public/static/layui/css/modules/layer/default/loading-1.gif');
		 
		 copy('http://x1.php168.com/public/static/layui/css/modules/layer/default/loading-2.gif',
		         ROOT_PATH.'public/static/layui/css/modules/layer/default/loading-2.gif');
		 
	}
}