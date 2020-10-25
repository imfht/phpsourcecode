<?php
namespace plugins\weixin\admin;

use app\common\controller\AdminBase;
use plugins\weixin\model\Menu AS WeixinMenuModel;

class Menu extends AdminBase
{
	public function config()
    {
        if(!function_exists('curl_init')){
            $this->error('你的空间不支持“curl_init”函数，请联系空间商配置服务器使之支持该函数');
        }
		
        $model = new WeixinMenuModel();
		
		if(IS_POST){
			$data = get_post('post');
					 
			if ( $model->save_data($data['postdb']) ) {
			    $code = $model->build_menu_data();
			    if($this->create_menu($code)){
			        $this->success('公众号菜单已生成，你需要退出公众号过一会才能看到效果');
			    }else{
			        $this->error('菜单生成失败');
			    }                
            } else {
                $this->error('更新失败');
            }
		}
		
		$array = $model->getMenu();
		return $this->pfetch('config',['listdb1'=>$array]);
	}
	
	private function create_menu($data){
	    $access_token = wx_getAccessToken(true);
	    http_Curl("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=$access_token");	//先删除旧菜单
	    
	    $code = http_Curl("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token",$data);
	    
	    $res = json_decode($code);
	    
	    if($res->errcode!=0){	        
	        $wx_errorcodeArray = @include(ROOT_PATH.'plugins/weixin/api/errorcode.php');	        
	        $msg = $wx_errorcodeArray[$res->errcode];
	        if($msg!=''){
	            $this->error("菜单生成失败，原因是：$msg");
	        }else{
	            $this->error('<a href="http://mp.weixin.qq.com/wiki/17/fa4e1434e57290788bde25603fa2fcbd.html" target="_blank">生成失败,请点击查看对应的错误代码原因：</a><br>'.$code);
	        }
	        //string(58) "{"errcode":40054,"errmsg":"invalid sub button url domain"}"
	    }elseif(strstr($code,'ok')){
	        return true;   //'设置成功，你需要重新关注微信才能看得到效果'
	    }else{
	        $this->error('当前服务器空间配置有问题，导致菜单生成失败!');
	    }
	}
	
}
