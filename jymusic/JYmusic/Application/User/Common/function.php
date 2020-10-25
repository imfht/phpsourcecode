<?php
/**
 * 获取导航URL
 * @param  string $url 导航URL
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U('/'.$url);
            //$arr = array('/user.php'=>'/index.php');
			//$url =strtr($url,$arr);
            break;
    }
    return $url;
}
/**
 * 获取回复
 */
function get_reply($id){
	return M('Message')->where(array('reply_msg_id'=>$id))->select();
}

