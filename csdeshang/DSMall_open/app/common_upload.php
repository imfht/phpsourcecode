<?php

/*
 * 常规上传图片公共处理
 */

function ds_upload_pic($upload_path, $file_name, $save_name = '') {
    $file_object = request()->file($file_name);
    $upload_path = BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $upload_path;


    $file_config = array(
        'disks' => array(
            'local' => array(
                'root' => $upload_path
            )
        )
    );
    config($file_config, 'filesystem');
    try {
        validate(['image' => 'fileSize:' . ALLOW_IMG_SIZE . '|fileExt:' . ALLOW_IMG_EXT])
                ->check(['image' => $file_object]);
        if ($save_name) {
            $temp= explode('.', $save_name);
            if(count($temp)<2){
                $save_name.='.png';
            }
            $file_name = \think\facade\Filesystem::putFileAs('', $file_object, $save_name);
        } else {
            $file_name = \think\facade\Filesystem::putFile('', $file_object, 'uniqid');
        }
        return ds_callback(true, '', array('file_name' => $file_name));
    } catch (\Exception $e) {
        return ds_callback(false, $e->getMessage());
    }
}

/*
 * 公共生成缩略图
 * @param string $upload_path 上传文件路径
 * @param string $file_name 上传设置的文件名称
 * @param array $thumb_width 设置的图片宽度
 * @param array $thumb_height 设置的图片高度
 * @param array $thumb_ext 为空表示为不生成多余的图片，直接按照比例生成覆盖
 * @return string
 */

function ds_create_thumb($upload_path, $file_name, $thumb_width, $thumb_height, $thumb_ext = '') {
    if (!file_exists($upload_path . '/' . $file_name)) {
        return;
    }

    $thumb_width = explode(',', $thumb_width);
    $thumb_height = explode(',', $thumb_height);

    if (empty($thumb_ext)) {
        //为空则覆盖原有图片
        $image = \think\Image::open($upload_path . '/' . $file_name);
        $image->thumb($thumb_width[0], $thumb_height[0], \think\Image::THUMB_CENTER)->save($upload_path . '/' . $file_name);
    } else {
        $common_images_ext = explode(',', COMMON_IMAGES_EXT);
        $thumb_ext = explode(',', $thumb_ext);

        $ifthumb = FALSE;
        if ((count($thumb_width) == count($thumb_height)) && (count($thumb_width) == count($thumb_ext))) {
            $ifthumb = TRUE;
        }
        if ($ifthumb) {
            for ($i = 0; $i < count($thumb_width); $i++) {
                if (in_array($thumb_ext[$i], $common_images_ext)) {
                    $image = \think\Image::open($upload_path . '/' . $file_name);
                    $image->thumb($thumb_width[$i], $thumb_width[$i], \think\Image::THUMB_CENTER)->save($upload_path . '/' . str_ireplace('.', $thumb_ext[$i] . '.', $file_name));
                }
            }
        }
    }
}

/*
 * 公共删除图片
 */

function ds_unlink($upload_path, $file_name) {
    $common_images_ext = explode(',', COMMON_IMAGES_EXT);
    foreach ($common_images_ext as $ext) {
        $thumb_file = str_ireplace('.', $ext . '.', $file_name);
        @unlink($upload_path . DIRECTORY_SEPARATOR . $thumb_file);
    }
    @unlink($upload_path . DIRECTORY_SEPARATOR . $file_name);
}

/**
 * 只针对于相册图片上传的图片进行处理
 * upload_path  文件保存路径
 * file_name  上传文件的value值
 * save_name  文件保存名称
 */
function upload_albumpic($upload_path, $file_name = 'file', $save_name) {
    //判断是否上传图片
    if (!empty($_FILES[$file_name]['name'])) {
        $upload_type = config('ds_config.upload_type');
        //远程保存
        if ($upload_type == 'alioss') {
            $accessId = config('ds_config.alioss_accessid');
            $accessSecret = config('ds_config.alioss_accesssecret');
            $bucket = config('ds_config.alioss_bucket');
            $endpoint = config('ds_config.alioss_endpoint');
            $aliendpoint_type = config('ds_config.aliendpoint_type') == '1' ? true : false;
            if (!strpos($save_name, '.')) {
                $save_name .= '.' . pathinfo($_FILES[$file_name]['name'], PATHINFO_EXTENSION);
            }
            $object = $upload_path . '/' . 'alioss_' . $save_name;
            $filePath = $_FILES[$file_name]['tmp_name'];
            require_once root_path() . 'vendor/aliyuncs/oss-sdk-php/autoload.php';
            $OssClient = new \OSS\OssClient($accessId, $accessSecret, $endpoint, $aliendpoint_type);
            try {
                $fileinfo = $OssClient->uploadFile($bucket, $object, $filePath);
                return array('code' => '10000', 'message' => '', 'result' => $fileinfo['info']['url']);
            } catch (OssException $e) {
                return array('code' => '10001', 'message' => $e->getMessage(), 'result' => '');
            }
        } elseif ($upload_type == 'local') {
            //本地图片保存
            $file_object = request()->file($file_name);
            $upload_path = BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $upload_path;
            $file_config = array(
                'disks' => array(
                    'local' => array(
                        'root' => $upload_path
                    )
                )
            );
            config($file_config, 'filesystem');
            try {
                $temp= explode('.', $save_name);
                if(count($temp)<2){
                    $save_name.='.png';
                }
                validate(['image' => 'fileSize:' . ALLOW_IMG_SIZE . '|fileExt:' . ALLOW_IMG_EXT])
                        ->check(['image' => $file_object]);
                $file_name = \think\facade\Filesystem::putFileAs('', $file_object, $save_name);
                $img_path = $upload_path . '/' . $file_name;
                create_albumpic_thumb($upload_path, $file_name);
                return array('code' => '10000', 'message' => '', 'result' => $img_path);
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $data['code'] = '10001';
                $data['message'] = $error;
                $data['result'] = $_FILES[$file_name]['name'];
                return $data;
            }
        }
        //预留文件类型检测
    } else {
        return array('code' => '10001', 'message' => '', 'result' => '');
    }
}

/*
 * 生成相册图片的缩略图
 * upload_path  文件路径
 * file_name  文件名称
 */

function create_albumpic_thumb($upload_path, $file_name) {
    if (!file_exists($upload_path . '/' . $file_name)) {
        return;
    }
    $ifthumb = FALSE;
    if (defined('GOODS_IMAGES_WIDTH') && defined('GOODS_IMAGES_HEIGHT') && defined('GOODS_IMAGES_EXT')) {
        $thumb_width = explode(',', GOODS_IMAGES_WIDTH);
        $thumb_height = explode(',', GOODS_IMAGES_HEIGHT);
        $thumb_ext = explode(',', GOODS_IMAGES_EXT);
        if (count($thumb_width) == count($thumb_height) && count($thumb_width) == count($thumb_ext)) {
            $ifthumb = TRUE;
        }
    }
    if ($ifthumb) {
        for ($i = 0; $i < count($thumb_width); $i++) {
            $image = \think\Image::open($upload_path . '/' . $file_name);
            $image->thumb($thumb_width[$i], $thumb_height[$i], \think\Image::THUMB_CENTER)->save($upload_path . '/' . str_ireplace('.', $thumb_ext[$i] . '.', $file_name));
        }
    }
}

/* * 删除商品图文件
 * pic_list  要删除的文件
 * */

function del_albumpic($pic_list) {
    if (!empty($pic_list) && is_array($pic_list)) {
        $count = '0';
        foreach ($pic_list as $val) {
            $upload_type = explode('_', $val['apic_cover']);
            if ($upload_type['0'] == 'alioss') {
                $count++;
            }
        }
        $image_ext = explode(',', GOODS_IMAGES_EXT);

        foreach ($pic_list as $v) {
            $upload_type = explode('_', $v['apic_cover']);
            //外网存储图片
            if (in_array($upload_type['0'], array('alioss', 'cos'))) {
                if ($upload_type['0'] == 'alioss') {
                    if ($count > 1) {
                        $object[] = ATTACH_GOODS . '/' . $v['store_id'] . '/' . $v['apic_cover'];
                    } else {
                        $object = ATTACH_GOODS . '/' . $v['store_id'] . '/' . $v['apic_cover'];
                    }
                }
            } else {
                $upload_path = BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_GOODS . DIRECTORY_SEPARATOR . $v['store_id'];
                foreach ($image_ext as $ext) {
                    $file = str_ireplace('.', $ext . '.', $v['apic_cover']);
                    @unlink($upload_path . DIRECTORY_SEPARATOR . $file);
                }
                @unlink($upload_path . DIRECTORY_SEPARATOR . $v['apic_cover']);
            }
        }
        $upload_type = config('ds_config.upload_type');
        if ($upload_type == 'alioss') {
            //外网存储图片删除
            $accessId = config('ds_config.alioss_accessid');
            $accessSecret = config('ds_config.alioss_accesssecret');
            $bucket = config('ds_config.alioss_bucket');
            $endpoint = config('ds_config.alioss_endpoint');
            $aliendpoint_type = config('ds_config.aliendpoint_type') == '1' ? true : false;
            require_once root_path() . 'vendor/aliyuncs/oss-sdk-php/autoload.php';
            $OssClient = new \OSS\OssClient($accessId, $accessSecret, $endpoint, $aliendpoint_type);
            try {
                if (is_array($object)) {
                    $OssClient->deleteObjects($bucket, $object);
                } else {
                    $OssClient->deleteObject($bucket, $object);
                }
                return array('code' => '10000', 'message' => '', 'result' => '');
            } catch (OssException $e) {
                return array('code' => '10001', 'message' => $e->getMessage(), 'result' => '');
            }
        }
        return array('code' => '10001', 'message' => '', 'result' => '');
    }
}
