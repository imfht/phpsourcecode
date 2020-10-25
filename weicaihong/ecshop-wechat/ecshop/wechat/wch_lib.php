<?php

/**
 *
 * wch_lib.php UTF-8
 * User: weicaihong
 * Date: 2013-11-17
 * link http://www.weicaihong.com
 *
 */


$wxid = isset($_GET['wxid']) ? $_GET['wxid'] : '';

//刘立 2015-02-03 增加如果从get获取不到则尝试从post获取
if($wxid==''){
    $wxid = isset($_POST['wxid']) ? $_POST['wxid'] : '';
}
//解密微信id
require(ROOT_PATH . 'wechat/wch_crpt.php');

$wxid = wch_decrypt($wxid);


if(!empty($wxid) && strlen($wxid) == 28){

    $query_sql = "SELECT `user_name`,`user_id` FROM  ".$ecs->table('users')." WHERE `wxid` = '$wxid'";
    $row = $db->getRow($query_sql);
    $username = $row['user_name'];
    $user_id = $row['user_id'];

    $endurl = '';

    if(!empty($username)){
        $user->set_session($username);
        $user->set_cookie($username);
        update_user_info();
        recalculate_price();

        if(!empty($wch_affiliate) and $wch_affiliate == 'on')
        {
            $wch_u = isset($_GET['u']) ? $_GET['u'] : '';
            if(empty($wch_u))
            {
                $endurl = '?wch=1&u='.$user_id;
            }

        }

    }
    if(empty($endurl))
    {
        $endurl = '?wch=1';
    }


    $query_string = $_SERVER['QUERY_STRING'];
    $q_arr = explode('&',$query_string);
    unset($_GET['wxid']);
    foreach($_GET as $k=>$v)
    {
        $endurl .= '&'.$k.'='.$v;
    }

    $Loaction = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$endurl;

    ecs_header("Location: $Loaction\n");
}

//环境判断
if (strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger"))
{
    if(!strpos($_SERVER['REQUEST_URI'],'region.php'))
    {
        if (empty($_SESSION['user_id']) and empty($wxid))
        {
            $wch_back = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $go_url = 'http://mp.weicaihong.com/index.php/weixin/wxch_oauth/back'.'?wchToken='.md5(appId).'&url='.$wch_back;
            wch_header($go_url);
        }
    }
}


function wch_header($url)
{
    header('Expires: 0');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s') . ' GMT');
    header('Pragma: no-cache');
    header("Location: $url");
    exit;
}

function wch_curl_post($url,$data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 200);
    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    ob_start();
    return curl_exec ($ch);
    ob_end_clean();
    curl_close ($ch);
    unset($ch);

}
