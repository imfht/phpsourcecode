<?php

class MessageClass

{

	static function ShowMessage($success_msg,$success_url=null,$result = true,$fail_msg =null,$fail_url=null,$sec = NULL,$title="Message",$onTop = false)

	{

		global $GVar;
		global $Config;

		$result = empty($result)?0:1;
		if(empty($sec))
        {
			$sec = $Config->data['MessageDelay'];
        }

		$success_url = empty($success_url)?$_SERVER["HTTP_REFERER"]:$success_url;
		$success_url = empty($success_url)?$_SERVER["PHP_SELF"] . "?m=admin&t=login":$success_url;
		//$GVar->ClearCookieData();
		$GVar->session['success_msg'] = empty($success_msg)?null:$success_msg ;
        $GVar->session['title'] = empty($title)?null:$title ;
        $GVar->session['fail_msg'] = empty($fail_msg)?null:$fail_msg ;
        $GVar->session['success_url'] = empty($success_url)?null:$success_url ;
        $GVar->session['fail_url'] = empty($fail_url)?null:$fail_url ;
        $GVar->session['sec'] = $sec ;

        if(!$result)
        	$GVar->session['result'] = "0" ;
		else 
			$GVar->session['result'] = 1 ;

        $GVar->SetSessionData();

        $url="index.php?m=admin&t=ShowMessage";
        
        echo "<script>\n";
		echo "top.location.href='$url';\n";
		echo "</script>\n";
		/*if ($onTop){
			echo "<script>\n";
			echo "top.location.href='$url';\n";
			echo "</script>\n";
		}else{
			header("location:$url");
		}*/

	
		die();
	}

	static function ShowMessageComponent($success_msg,$success_url=null,$result = true,$fail_msg =null,$fail_url=null,$sec = NULL,$title="Message",$onTop = false,$type=1,$save_post_value=0){

		global $GVar;
		global $Config;

		if ($result)
			$result = 1;
		else
			$result = 0;

		if(empty($sec))
        {
			//設定除錯模式

			if ($Config->GetVar("UsePHPDebugMode") == 1)

			{
				ini_set('display_errors', true);
				error_reporting(E_ERROR | E_WARNING | E_NOTICE);
			}

			$sec = $Config->data['MessageDelay'];

        }

		$success_url = empty($success_url)?$_SERVER["HTTP_REFERER"]:$success_url;
		$success_url = empty($success_url)?$_SERVER["PHP_SELF"] . "?t=login":$success_url;

		//如果信息类型等于真，跳到正确信息页

        if ((is_int($type) && $type > 0) || (is_bool($type) && $type == true) || (is_string($type) && !empty($type)) || is_object($type)) {

        } else {

        	$GVar->ClearCookieData();

            //如果保留上頁數據

            if($save_post_value) {
                foreach ($GVar->post as $key => $value) {
                    $GVar->cookie['post__'.$key] = $value ;
                }
            }
        }

		$GVar->cookie['success_msg'] = empty($success_msg)?null:$success_msg ;

        $GVar->cookie['title'] = empty($title)?null:$title ;

        $GVar->cookie['fail_msg'] = empty($fail_msg)?null:$fail_msg ;

        $GVar->cookie['success_url'] = empty($success_url)?null:$success_url ;

        $GVar->cookie['fail_url'] = empty($fail_url)?null:$fail_url ;

        $GVar->cookie['sec'] = $sec ;

        if(!$result){
        	$GVar->cookie['result'] = "0" ;
        }else{ 
			$GVar->cookie['result'] = 1 ;
        }
        
        $GVar->SetCookieData();
		if ($onTop){
			echo "<script>\n";
			echo "top.location.href='/index.php?t=ShowMessageComponent';\n";
			echo "</script>\n";
		}else{
			header("location:/index.php?t=ShowMessageComponent");
		}
		die();
	}
}
?>