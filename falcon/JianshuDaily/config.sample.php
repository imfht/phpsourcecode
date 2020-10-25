<?php
/**
 *
 * Description: 配置文件,请复制并改名为config.php
 * Author: falcon
 * Date: 16/1/6
 * Time: 上午11:49
 *
 */
$config = array(
    'JS_KEEP_IMG'=>true,  // 是否需要保留文章图片,会增加生成的电子书大小
    'JS_IMG_WIDTH'=>200,  // 简书图片缩放宽度 px
    'JS_MAX_PAGE'=> 2, //简书采集最大页数:1~5,每页20篇文章
    'KD_SEND_ZIP'=>false, //todo: 是否发送经过zip压缩的电子书
    'KD_RECEIVER'=> array('falcon_chen@qq.com','falcon_chen_40@kindle.cn'),// 电子书接收邮箱,可以为kindle或普通邮箱
    'KD_SENDER'=>array( //stmp发信邮箱
        'from'=>'每日简书-cellmean.com',
        'host' => '',// smtp host
        'username' => '',// sender username
        'password' => '',// sender password
        'secure' => 'ssl',//是否ssl,不使用时请留空
        'smtp_port' => 465,//端口,ssl一般为465,非ssl为25
    ),
    'KINDLEGEN_PATH'=> "/usr/local/kindlegen_dir/kindlegen",//kindlegen所在路径
);
