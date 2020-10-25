<?php

use WxSDK\WxApp;
use WxSDK\core\model\tpl\DataItem;
use WxSDK\core\model\tpl\TplDataArray;
use WxSDK\core\model\tpl\TplModel;
use WxSDK\core\module\TplKit;

include_once '../Loader.php';; // 自动加载类
$accessToken = new WxApp();

//新增模版



//获取模版列表
// $ret = TplKit::getList($accessToken);
// exit(json_encode($ret));

//发送消息
// $data = [];
// $data['userName'] = array(
//     'value'=>'麻言',   
// );

$temp[] = new DataItem('userName', "麻言");
$temp[] = new DataItem('loginNum', '6','#ff0000');
$data = new TplDataArray(...$temp);
$model = new TplModel('oUIjzjqtAVuPY2sIT96Ouy2V8Ejs'
    , '2tOxVH-tBRYDe5cLE-JNKwaiTyLp39-RblldlaLAKg8', $data);
$ret = TplKit::sendMsg($accessToken, $model);
echo json_encode($ret);