<?php
/**
 * 图像识别
 *      菜品识别
 *      车辆识别
 *      logo商标识别
 *      动物识别
 *      植物识别
 */
header('content-type:text/html;charset=utf-8');

require_once '../config.php';
require_once 'common.func.php';

try {
    // 判断请求方式
    if (!isPost()) {
        throw new Exception('请求方式错误');
    }

    // 判断图片是否正确，如正确则上传图片
    $upload_img_url = uploadFile($_FILES['myFile'], '../Uploads', ['jpeg', 'jpg', 'png', 'bmp']);

    // 读取图片信息
    $image = file_get_contents($upload_img_url);

    $type = intval($_POST['type']);

    // 图像识别
    $res = ImageRecogn($image, $type);

    $result = $res['result'];

    if (empty($result)) {
        throw new Exception('识别错误');
    }

    echo json_encode(['status' => 1, 'data' => $result]);
    exit();
} catch (Exception $e) {
    echo json_encode(['data' => $e->getMessage(), 'status' => 0]);
    exit();
}
