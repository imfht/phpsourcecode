<?php
/**
 * 文字识别
 *      通用文字识别
 *      通用文字识别（高精度）
 *      网络图片文字识别
 *      身份证识别
 *      银行卡识别
 *      驾驶证识别
 *      行驶证识别
 *      车牌识别
 *      营业执照识别
 *      通用票据识别
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

    // 加上配置：开启检测图像朝向
    $options["detect_direction"] = "true";

    $type = intval($_POST['type']);

    // 如果是身份证识别则判断参数
    if ($type === 4) {
        $id_card_side = $_POST['id_card_side'];
        if (empty($id_card_side) || !in_array($id_card_side, ['front', 'back'])) {
            throw new Exception('参数错误');
        }
        $options['id_card_side'] = $id_card_side;
    }

    // 文字识别
    $res = wordsOcr($image, $type, $options);

    // 返回格式的处理
    $result = handleResult($type, $res);

    if (empty($result)) {
        throw new Exception('识别错误');
    }

    echo json_encode($result);
    exit();
} catch (Exception $e) {
    echo json_encode(['data' => $e->getMessage(), 'status' => 0]);
    exit();
}
