<?php
require_once('./source/VerifyPic.class.php');

session_start();

$obj = new VerifyPic();

$oper = $_GET['oper'];

//获取图片文字及顺序
if ($oper == 'getdata') {
    $res = $obj->getWordsOrder();
    if ($res == -1) {
        $data = array('code'=>1000, 'msg'=>'必须先请求图片');
    }
    else {
        $data = array('code'=>0, 'data'=>$res, 'msg'=>'success');
    }
}
//验证用户点选位置
elseif ($oper == 'checkdot') {
    $dot_arr = explode(',', $_GET['dots']);
    $arr[0] = array('x'=>$dot_arr[0], 'y'=>$dot_arr[1]);
    $arr[1] = array('x'=>$dot_arr[2], 'y'=>$dot_arr[3]);
    $arr[2] = array('x'=>$dot_arr[4], 'y'=>$dot_arr[5]);
    $res = $obj->checkPositions($arr);
    if ($res) {
        $_SESSION['VerifyPicStatus'] = true;
        $data = array('code'=>0, 'msg'=>'success');
    }
    else {
        $data = array('code'=>1001, 'msg'=>'验证失败');
    }
}
//输出一张验证码图片
else {
    $obj->getImage();
    exit;
}

echo json_encode($data);

