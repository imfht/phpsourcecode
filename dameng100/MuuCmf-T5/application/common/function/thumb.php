<?php
use think\Db;
use think\Loader;
use think\Image;

/**
 * 单图上传组件
 * @param  [type] $name      [description]
 * @param  [type] $image_id [description]
 * @return [type]           [description]
 */
function single_image_upload($name, $image_id){

    $image_path = get_cover($image_id);
    $upload_picture = lang("_SELECT_PICTURES_");
    $delete_picture = lang("_DELETE_");
    $api = url('api/file/uploadPicture',array('session_id'=>session_id()));

    $html = <<<EOF
<div class="single-image-upload image-upload controls">
    <input class="attach" type="hidden" name="{$name}" value="{$image_id}"/>
    <div class="upload-img-box">
        <div class="upload-pre-item popup-gallery">
EOF;
    if(!empty($image_id)){
    $html .= <<<EOF
        <div class="each">
            <img src="{$image_path}">
            <div class="text-center opacity del_btn"></div>
            <div data-id="{$image_id}" class="text-center del_btn">{$delete_picture}</div>
        </div>
EOF;
    }
            
    $html .= <<<EOF
        </div>
    </div>
    <div id="upload_single_image_{$name}" class="">{$upload_picture}</div>
</div>

<script>
    $(function () {
        var uploader_{$name}= WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: true,
            // swf文件路径
            swf: 'Uploader.swf',
            // 文件接收服务端。
            server: "{$api}",
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: {id:'#upload_single_image_{$name}',multiple: false},
            // 只允许选择图片文件
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/jpg,image/jpeg,image/png'
            }
        });
        uploader_{$name}.on('fileQueued', function (file) {
            uploader_{$name}.upload();
            toast.showLoading();
        });
        /*上传成功**/
        uploader_{$name}.on('uploadSuccess', function (file, data) {
            if (data.code) {
                $("[name='{$name}']").val(data.data[0].id);
                $("[name='{$name}']").parent().find('.upload-pre-item').html(
                    '<div class="each">' +
                    '<img src="'+ data.data[0].path+'">' +
                    '<div class="text-center opacity del_btn"></div>' +
                    '<div data-id="'+data.data[0].id+'" class="text-center del_btn">{$delete_picture}</div>'+
                    '</div>'
                );
                //重启webuploader,可多次上传
                uploader_{$name}.reset();
            } else {
                updateAlert(data.msg);
                setTimeout(function () {
                    $('#top-alert').find('button').click();
                    $(that).removeClass('disabled').prop('disabled', false);
                }, 1500);
            }
        });
        //上传完成
        uploader_{$name}.on( 'uploadComplete', function( file ) {
            toast.hideLoading();
        });

        //移除图片
        $('.single-image-upload').on('click','.del_btn',function(){
            var id = $(this).data('id');
            admin_image.removeImage($(this),id);
        })

    })
</script>
EOF;
    return $html;
}

/**
 * 多图上传
 * @param  [type] $name [description]
 * @param  [type] $ids  [description]
 * @return [type]       [description]
 */
function multi_image_upload($name, $ids = '')
{
    $upload_picture = lang("_SELECT_PICTURES_");
    $delete_picture = lang("_DELETE_");
    $picture_exists = lang('_THE_PICTURE_ALREADY_EXISTS_WITH_SINGLE_');
    $limit_exceed = lang('_EXCEED_THE_PICTURE_LIMIT_WITH_SINGLE_');
    $api = url('api/file/uploadPicture',array('session_id'=>session_id()));

    $html = '';
    $html .= '
    <div class="multi-image-upload image-upload controls">
        <input class="attach" type="hidden" name="'.$name.'" value="'.$ids.'"/>
        <div class="upload-img-box">
            <div class="upload-pre-item popup-gallery">';
    if(!empty($ids)){
        $aIds = explode(',',$ids);
        foreach($aIds as $aId){
            $path = get_cover($aId);
            $html .= '
                <div class="each">
                    <img src="'.$path.'">
                    <div class="text-center opacity del_btn"></div>
                    <div data-id="'.$aId.'" class="text-center del_btn">'.$delete_picture.'</div>
                </div>
            ';
        }
    }
    
    $html .= '
            </div>
        </div>
        <div id="upload_multi_image_'.$name.'">'.$upload_picture.'</div>
    </div>
    ';       
    $html .= <<<EOF
    <script>
    $(function () {
        var id = "#upload_multi_image_{$name}";
        var limit = parseInt(6);
        var uploader_{$name}= WebUploader.create({
            // 选完文件后，是否自动上传。
            swf: 'Uploader.swf',
            // 文件接收服务端。
            server: "{$api}",
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素
            pick: {'id': id, 'multi': true},
            fileNumLimit: limit,
            // 只允许文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/image/jpg,image/jpeg,image/png'
            }
        });
        uploader_{$name}.on('fileQueued', function (file) {
            uploader_{$name}.upload();
            toast.showLoading();
        });
        uploader_{$name}.on('uploadFinished', function (file) {
            uploader_{$name}.reset();
        });
        /*上传成功**/
        uploader_{$name}.on('uploadSuccess', function (file, data) {
          if (data.code) {
            var ids = $("[name='{$name}']").val();
            ids = ids.split(',');
            if( ids.indexOf(data.data[0].id) == -1){
                var rids = admin_image.upAttachVal('add',data.data[0].id, $("[name='{$name}']"));
                if(rids.length>limit){
                    updateAlert({$limit_exceed});
                    return;
                }
                
                $("[name='{$name}']").parent().find('.upload-pre-item').append(
                    '<div class="each">'+
                    '<img src="'+ data.data[0].path+'">'+
                    '<div class="text-center opacity del_btn"></div>' +
                    '<div data-id="'+data.data[0].id+'" class="text-center del_btn">{$delete_picture}</div>'+
                    '</div>'
                );
            }else{
                updateAlert({$picture_exists});
            }
        } else {
            updateAlert(data.msg);
            setTimeout(function () {
                $('#top-alert').find('button').click();
                $(that).removeClass('disabled').prop('disabled', false);
            }, 1500);
        }
        });
        //上传完成
        uploader_{$name}.on( 'uploadComplete', function( file ) {
            toast.hideLoading();
        });

        //移除图片
        $('.multi-image-upload').on('click','.del_btn',function(){
            var id = $(this).data('id');
            admin_image.removeImage($(this),id);
        })

    })
    </script>
EOF;

    return $html;
}
/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 */
function get_cover($cover_id, $field = 'path')
{
    if (empty($cover_id)) {
        return false;
    }
    $picture = Db::name('Picture')->where(['id'=>$cover_id,'status' => 1])->find();
    $picture['path'] = get_pic_src($picture['path']);
    return empty($field) ? $picture : $picture[$field];
}

function pic($cover_id)
{
    return get_cover($cover_id, 'path');
}

/** 不兼容sae 只兼容本地 --駿濤
 * @param        $filename
 * @param int $width
 * @param string $height
 * @param int $type
 * @param bool $replace
 * @return mixed|string
 */
function getThumbImage($filename, $width = 100, $height = 'auto', $type = 0, $replace = false)
{
    $UPLOAD_URL = '';
    $UPLOAD_PATH = '';
    $filename = str_ireplace($UPLOAD_URL, '', $filename); //将URL转化为本地地址
    $info = pathinfo($filename);
    $oldFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $info['extension'];
    $thumbFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_' . $width . '_' . $height . '.' . $info['extension'];

    $oldFile = str_replace('\\', '/', $oldFile);
    $thumbFile = str_replace('\\', '/', $thumbFile);

    $filename = ltrim($filename, '/');
    $oldFile = ltrim($oldFile, '/');
    $thumbFile = ltrim($thumbFile, '/');

    if (!file_exists($UPLOAD_PATH . $oldFile)) {
        //原图不存在直接返回
        @unlink($UPLOAD_PATH . $thumbFile);
        $info['src'] = $oldFile;
        $info['width'] = intval($width);
        $info['height'] = intval($height);
        return $info;
    } elseif (file_exists($UPLOAD_PATH . $thumbFile) && !$replace) {
        //缩图已存在并且  replace替换为false
        $imageinfo = getimagesize($UPLOAD_PATH . $thumbFile);
        $info['src'] = $thumbFile;
        $info['width'] = intval($imageinfo[0]);
        $info['height'] = intval($imageinfo[1]);
        return $info;
    } else {
        //执行缩图操作
        $oldimageinfo = getimagesize($UPLOAD_PATH . $oldFile);
        $old_image_width = intval($oldimageinfo[0]);
        $old_image_height = intval($oldimageinfo[1]);
        if ($old_image_width <= $width && $old_image_height <= $height) {
            @unlink($UPLOAD_PATH . $thumbFile);
            @copy($UPLOAD_PATH . $oldFile, $UPLOAD_PATH . $thumbFile);
            $info['src'] = $thumbFile;
            $info['width'] = $old_image_width;
            $info['height'] = $old_image_height;
            return $info;
        } else {
            if ($height == "auto") $height = $old_image_height * $width / $old_image_width;
            if ($width == "auto") $width = $old_image_width * $width / $old_image_height;
            if (intval($height) == 0 || intval($width) == 0) {
                return 0;
            }

            $thumb = Image::open($UPLOAD_PATH . $filename);
            //默认裁切类型标识缩略图居中裁剪类型，先写死，后续版本增加后台设置
            $thumb->thumb($width, $height, Image::THUMB_CENTER);
            
            $res = $thumb->save($UPLOAD_PATH . $thumbFile);
            $info['src'] = $UPLOAD_PATH . $thumbFile;
            $info['width'] = $old_image_width;
            $info['height'] = $old_image_height;
            return $info;
        }
    }
}

/**通过ID获取到图片的缩略图
 * @param        $cover_id 图片的ID
 * @param int $width 需要取得的宽
 * @param string $height 需要取得的高
 * @param int $type 图片的类型，qiniu 七牛，local 本地, sae SAE
 * @param bool $replace 是否强制替换
 * @return string
 * @auth 大蒙
 */
function getThumbImageById($cover_id, $width = 100, $height = 'auto', $type = 0, $replace = false)
{
    //存在cover_id为空时，写入public/images路径的bug待修复
    $picture = cache('picture_' . $cover_id);
    if (empty($picture)) {
        $picture = Db::name('Picture')->where(['id'=>$cover_id,'status' => 1])->find();
        
        cache('picture_' . $cover_id, $picture);
    }
    if (empty($picture)) {
        $attach = getThumbImage('uploads/picture/nopic.png', $width, $height, $type, $replace);
        return get_pic_src($attach['src']);
    }
    if ($picture['type'] == 'local' || $picture['driver'] == 'local') {
        $attach = getThumbImage($picture['path'], $width, $height, $type, $replace);

        return get_pic_src($attach['src']);
    } else {
        $new_img = $picture['path'];
        $name = get_addon_class($picture['driver']);
        if (class_exists($name)) {
            $class = new $name();
            if (method_exists($class, 'thumb')) {
                $new_img = $class->thumb($picture['path'], $width, $height);
            }
        }

        return get_pic_src($new_img);
    }

}

/**简写函数，等同于getThumbImageById（）
 * @param $cover_id 图片id
 * @param int $width 宽度
 * @param string $height 高度
 * @param int $type 裁剪类型，0居中裁剪
 * @param bool $replace 裁剪
 * @return string
 */
function thumb($cover_id, $width = 100, $height = 'auto', $type = 0, $replace = false)
{
    return getThumbImageById($cover_id, $width, $height, $type, $replace);
}


/**获取第一张图
 * @param $str_img
 * @return mixed
 */
function get_pic($str_img)
{
    preg_match_all("/<img.*\>/isU", $str_img, $ereg); //正则表达式把图片的整个都获取出来了
    $img = $ereg[0][0]; //图片
    $p = "#src=('|\")(.*)('|\")#isU"; //正则表达式
    preg_match_all($p, $img, $img1);
    $img_path = $img1[2][0]; //获取第一张图片路径
    return $img_path;
}


/**
 * get_pic_src   渲染图片链接
 * @param $path
 * @return mixed
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function get_pic_src($path)
{
    //不存在http://
    $not_http_remote = (strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote = (strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return getRootUrl() . str_replace('//', '/', $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}

/**
 * 补全渲染图片路径http部分
 *
 * @param      <type>  $path   The path
 *
 * @return     <type>  The remote source.
 */
function getRemoteSrc($path)
{
    //不存在http://
    $not_http_remote = (strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote = (strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return getRootUrl().$path;
    } else {
        //远端url
        return $path;
    }
}

/**获取网站的根Url
 * @return string
 */
function getRootUrl()
{
    return get_http_https().$_SERVER['SERVER_NAME'].'/';
}
