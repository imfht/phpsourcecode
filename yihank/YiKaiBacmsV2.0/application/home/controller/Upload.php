<?php
namespace app\home\controller;
use think\Controller;

/**
 * Class AdminPlug 后台插件类
 * hongkai.wang 20161203  QQ：529988248
 */
class Upload extends Controller{
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
        $data=model('admin/File')->uploadImg($file,'home');
        if ($data['url']){
            $msg['status'] =200;
            $msg['url'] = $data['url'];
        }else{
            $msg['status'] =0;
        }
        return json_encode($msg);
    }
    /**
     * 上传图片
     * @return
     * status  上传状态 1成功0失败
     * msg     返回上传消息
     * file_id 上传图片文件id
     * url     上传图片返回路径
     */
    public function uploadHead(){
        $user_id=session('home_user.user_id');
        if (empty($user_id)){
            return ajaxReturn(0,'您未登录');
        }
        foreach ($_FILES as $key=>$val){
            $file=$key;
            break;
        }
        $data=model('admin/File')->uploadImg($file,'home');
        if ($data['url']){
            $_POST['head_url']=$data['url'];
            $status=model('User')->edit();
            if ($status>0){
                $msg['status'] =200;
                $msg['url'] = $data['url'];
                $msg['msg'] = '上传成功';
            }else{
                $msg['status'] =0;
                $msg['msg'] = '上传失败';
            }
        }else{
            $msg['status'] =0;
            $msg['msg'] = '上传失败';
        }
        return json_encode($msg);
    }
}