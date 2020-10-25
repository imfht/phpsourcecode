<?php

/**
 * 上传文件
 * @param array $fileInfo 图片信息
 * @param string $path 路径
 * @param array $allowExt 可允许扩展名
 * @param int $maxSize 最大文件空间
 * @param bool $flag 是否检测真实图片类型
 * @return string 返回文件地址
 * @throws Exception
 */
function uploadFile($fileInfo, $path = 'Uploads', $allowExt = ['jpeg', 'jpg', 'gif', 'png', 'bmp'], $maxSize = 1024 * 1024 * 2,  $flag = true)
{
    if (!is_array($allowExt)) {
        throw new Exception('参数错误');
    }

    // 上传文件的错误号(无错误则为0)
    $error = $fileInfo['error'];

    if ($error > 0) {
        // 匹配错误信息
        switch ($error) {
            case 1:
                $mes = '上传文件超过PHP配置文件中upload_max_filesize选项的值';
                break;
            case 2:
                $mes = '超过了表单MAX_FILE_SIZE限制的大小';
                break;
            case 3:
                $mes = '文件部分被上传';
                break;
            case 4:
                $mes = '没有选择上传文件';
                break;
            case 6:
                $mes = '没有找到临时目录';
                break;
            case 7:
                $mes = '文件写入失败';
                break;
            case 8:
                $mes = '上传文件被PHP扩展程序中断';
                break;
            default:
                $mes = '';
                break;
        }
        throw new Exception($mes);
    }

    // 判断上传文件的大小
    if ($fileInfo['size'] > $maxSize) {
        throw new Exception('上传文件过大');
    }

    // 上传文件名
    $filename = $fileInfo['name'];

    // 文件的扩展名
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    // 判断文件类型
    if (!in_array($ext, $allowExt)) {
        throw new Exception('非法文件类型');
    }

    // 上传文件的临时路径
    $tmp_name = $fileInfo['tmp_name'];

    // 判断文件是否通过HTTP_POST方式上传来的
    if (!is_uploaded_file($tmp_name)) {
        throw new Exception('文件不是通过HTTP_POST方式上传来的');
    }

    // 检测是否为真实图片
    if ($flag) {
        if (!getimagesize($tmp_name)) {
            throw new Exception('文件不是真实图片类型');
        }
    }

    // 判断目录是否存在，如果不存在
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        chmod($path, 0777);
    }

    // 上传的文件名,确保文件名唯一，防止重名产生覆盖
    $destination = $path . '/' . md5(uniqid(microtime(true), true)) . '.' . $ext;

    // 将服务器上的临时文件移动到指定的目录下
    if (move_uploaded_file($tmp_name, $destination)) {
        return $destination;
    } else {
        throw new Exception('文件上传失败');
    }
}

/**
 * 判断请求类型是否为post类型
 * @return bool
 */
function isPost()
{
    return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
}

/**
 * 文字识别
 * @param $image
 * @param int $type
 * @param array $options 配置
 * @return array
 * @throws Exception
 */
function wordsOcr($image, $type = 1, $options = [])
{
    require_once '../aip_sdk/AipOcr.php';

    $client = new AipOcr(APPID, APIKEY, SECRETKEY);

    $types = range(1, 10);
    if (!in_array($type, $types)) {
        throw new Exception('请求错误');
    }

    switch ($type) {
        // 调用通用文字识别, 图片参数为本地图片
        case 1:
            $res = $client->basicGeneral($image, $options);
            break;
        // 调用通用文字识别（高精度版）
        case 2:
            $res = $client->basicAccurate($image, $options);
            break;
        // 网络图片文字识别
        case 3:
            $res = $client->webImage($image, $options);
            break;
        // 身份证识别
        case 4:
            $id_card_side = $options['id_card_side'];
            $res = $client->idcard($image, $id_card_side, $options);
            break;
        // 银行卡识别
        case 5:
            $res = $client->bankcard($image);
            break;
        // 驾驶证识别
        case 6:
            $res = $client->drivingLicense($image, $options);
            break;
        // 行驶证识别
        case 7:
            $res = $client->vehicleLicense($image, $options);
            break;
        // 车牌识别
        case 8:
            $res = $client->licensePlate($image);
            break;
        // 营业执照识别
        case 9:
            $res = $client->businessLicense($image);
            break;
        // 通用票据识别
        case 10:
            $res = $client->receipt($image, $options);
            break;
        default:
            $res = [];
            break;
    }

    return $res;
}

/**
 * 返回格式的处理
 * @param $type
 * @param $res
 * @return array|string
 */
function handleResult($type, $res)
{
    $result = [];
    switch ($type) {
        case 1:
        case 2:
        case 3:
            $words = '';
            foreach ($res['words_result'] as $v) {
                $words .= $v['words'] . "<br />";
            }
            $result['data'] = $words;
            $result['status'] = 1;
            break;
        case 4:
        case 6:
        case 7:
        case 9:
        case 10:
            foreach ($res['words_result'] as $k=>$v) {
                $result['data'][$k] = $v['words'];
            }
            $result['status'] = 1;
            break;
        case 5:
            $result['data'] = $res['result'];
            $result['status'] = 1;
            break;
        case 8:
            $result['data'] = $res['words_result'];
            $result['status'] = 1;
            break;
        default:
            $result = [];
            break;
    }
    return $result;
}

/**
 * 图像识别
 * @param $image
 * @param int $type
 * @return array
 * @throws Exception
 */
function ImageRecogn($image, $type = 1)
{
    require_once '../aip_sdk/AipImageClassify.php';

    $client = new AipImageClassify(APPID, APIKEY, SECRETKEY);

    $types = range(1, 5);
    if (!in_array($type, $types)) {
        throw new Exception('请求错误');
    }

    switch ($type) {
        // 调用菜品识别
        case 1:
            $res = $client->dishDetect($image);
            break;
        // 调用车辆识别
        case 2:
            $res = $client->carDetect($image);
            break;
        // 调用logo商标识别
        case 3:
            $res = $client->logoSearch($image);
            break;
        // 调用动物识别
        case 4:
            $res = $client->animalDetect($image);
            break;
        // 调用植物识别
        case 5:
            $res = $client->plantDetect($image);
            break;
        default:
            $res = [];
            break;
    }

    return $res;
}