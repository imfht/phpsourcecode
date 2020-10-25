<?php
namespace Control\Controller;
use Think\Controller;
class WeixinController extends Controller {
    public function index(){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		if($_POST['weixin_appid'] && $_POST['weixin_appsecret'] && $_POST['weixin_token']) {
		    		mc_update_option('weixin_appid',I('param.weixin_appid'));
		    		mc_update_option('weixin_appsecret',I('param.weixin_appsecret'));
		    		mc_update_option('weixin_token',I('param.weixin_token'));
		    		$this->success('更新成功');
		    	} else {
			    	$this->theme('admin')->display('Control/weixin');
		    	}
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function qunfa(){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		if($_POST['content'] && is_numeric($_POST['group'])) {
		    		$appid = mc_option('weixin_appid');
		    		$appsecret = mc_option('weixin_appsecret');
		    		$access_token = mc_get_access_token($appid,$appsecret);
			        /*
			        $thumb_media_id = $_POST['thumb_media_id'];
				    $articles = '{
		   "articles": [
				 {
		                        "thumb_media_id":"'.$thumb_media_id.'",
		                        "author":"xxx",
					 "title":"Happy Day",
					 "content_source_url":"www.mao10.com",
					 "content":"如果收到这条消息，就证明我成功了，所以要睡觉！",
					 "digest":"digest",
		                        "show_cover_pic":"1"
				 }
		   ]
		}';
				    $ptw_media_id = mc_publish_to_weixin($articles,$access_token);
				    */
				    $msg = '{
		   "filter":{
		      "group_id":"'.$_POST['group'].'"
		   },
		   "msgtype": "text", "text": { "content": "'.$_POST['content'].'"}
		}';
				    $haha = mc_weixin_send_msg($msg,$access_token);
				    //var_dump($haha);
				    $this->success('发布成功！',U('control/weixin/qunfa'));
				} else {
					$this->theme('admin')->display('Control/qunfa');
				};
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function huifu($page=1){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		if($_POST['msg'] && $_POST['return']) {
		    		mc_add_option($_POST['msg'],$_POST['return'],'wx_huifu');
		    		$this->success('添加成功！',U('control/weixin/huifu'));
	    		} elseif($_POST['del']) {
	    			M('option')->where("id = '".$_POST['del']."'")->delete();
		    		$this->success('删除成功！',U('control/weixin/huifu'));
	    		} else {
		    		$this->theme('admin')->display('Control/huifu');
	    		}
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    //上传媒体 暂不可用
    public function add_weixin(){
        if(mc_is_admin() && $_POST['fileup']=='ok') {
        	$file_name = $_FILES['file']['name'];
        	//获取文件扩展名
        	$temp_arr = explode(".", $file_name);
			$file_ext = array_pop($temp_arr);
			$file_ext = trim($file_ext);
			$file_ext = strtolower($file_ext);
			//创建文件
        	$new_files = "./Public/weixin/" . $file_name;
	        move_uploaded_file($_FILES["file"]["tmp_name"],$new_files);
	        $filex = $new_files;
	        $appid = mc_option('weixin_appid');
		    $appsecret = mc_option('weixin_appsecret');
		    $access_token = mc_get_access_token($appid,$appsecret);
	        $callback = mc_upload_media($filex,$access_token);
		    $this->assign('callback',$callback);
	        $this->theme('admin')->display('Publish/add_weixin');
	     } else {
		     $this->error('未知错误！',U('home/index/index'));
	     };
    }
    public function callback_url(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET["echostr"];
        $token = mc_option('weixin_token');
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
        	echo $echostr;
        	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        	if (!empty($postStr)){
		                
		              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		                $fromUsername = $postObj->FromUserName;
		                $toUsername = $postObj->ToUserName;
		                $msgtype = $postObj->MsgType;
		                $content = trim($postObj->Content);
		                $date = strtotime("now");        
						if($content!='')
		                {
		              		$return_to = M('option')->where('type="wx_huifu" AND meta_key="'.mc_magic_in($content).'"')->getField('meta_value');
		              		if($return_to!='') :
		              			$return_to_user = $return_to;
		              		else :
		              			$return_to_user = '我没有理解您的问题，请访问我们的网站：'.mc_site_url();
		              		endif;
		              		echo "<xml>
<ToUserName>$fromUsername</ToUserName>
<FromUserName>$toUsername</FromUserName>
<CreateTime>$date</CreateTime>
<MsgType>text</MsgType>
<Content>$return_to_user</Content>
</xml>";
		                }
		        }
        } else {
        	$this->error('Callback页面不允许直接访问！',U('home/index/index'));
        };
    }
}