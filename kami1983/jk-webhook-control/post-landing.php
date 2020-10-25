<?php
require_once 'lib.include.php';

//参数接收
$post_source=$_GET['s']; //来源站点

//获取配置信息
$conf_arr=  require_once 'conf/setting.inc.php';

$post_str= urldecode(file_get_contents('php://input'));



$match_arr=array();
if(!preg_match('/hook=(.*)/i', $post_str,$match_arr)){
    throw new ExceptionWebHookLog('Not seem like a hook msg.', '1604181559');
}

if('' == $match_arr[1]){
    throw new ExceptionWebHookLog('Not post content.', '1604181705');
}

CWebhookLog::AppendLog('RECIVE POST STR:'.date("Y-m-d H:i:s"), $match_arr[1]);

//将数据转为JSON 进行数据对比
$post_obj=  json_decode($match_arr[1]);
//循环配置文件
foreach($conf_arr as $conf_val){
    //提取有用的配置文件信息
    $password=$conf_val['password'];
    $repository_name=$conf_val['repository-name'];
    $order=$conf_val['__order'];
    
    //如果地址相等
    if($repository_name == $post_obj->push_data->repository->name){
        if($post_obj->password != $password){
            //但是密码不对则直接报错
            throw new ExceptionWebHookLog('Password err, request deny.', '1604181712');
        }
        //执行命令
        exec($order,$order_result);
        CWebhookLog::AppendLog('ORDER REQUEST:'.$order.' '.date("Y-m-d H:i:s"), print_r($order_result,true));
    }
}

CWebhookLog::AppendLog('REQUEST DONE:'.date("Y-m-d H:i:s"), '');


echo '<h1>Post landing</h1>';
echo '<p>At: '.date("Y-m-d H:i:s").'</p>';
echo '<p>Post source: '.$post_source.'</p>';
echo '<pre>';
echo ">>>\n{$post_str}\n<<<";
echo '</pre>';