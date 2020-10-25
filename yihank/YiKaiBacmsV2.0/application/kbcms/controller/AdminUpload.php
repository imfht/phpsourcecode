<?php
namespace app\kbcms\controller;
use think\Controller;

/**
 * Class AdminPlug 后台插件类
 * hongkai.wang 20161203  QQ：529988248
 */
class AdminUpload extends Controller{
    /**
     * 上传图片
     * @return
     * status  上传状态 1成功0失败
     * msg     返回上传消息
     * file_id 上传图片文件id
     * url     上传图片返回路径
     */
    public function upload(){
        foreach ($_FILES as $key=>$val){
            $file=$key;
            break;
        }
        $data=model('admin/File')->uploadImg($file);
        if ($data['url']){
            $msg['status'] =200;
            $msg['url'] = $data['url'];
        }else{
            $msg['status'] =0;
        }
        return json_encode($msg);
    }
}