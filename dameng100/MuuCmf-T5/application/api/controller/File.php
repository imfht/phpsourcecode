<?php
namespace app\api\controller;

use think\Controller;
use think\Db;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */

class File extends Controller
{
    /* 图片上传 */
    public function uploadPicture()
    {   
        //自定义目录名，暂只支持云存储
        $dirname = input('dirname','','text');
        $files = request()->file();
        if (empty($files)) {
            $return['code'] = 0;
            $return['msg'] = 'No file upload or server upload limit exceeded';
            return json($return);
        }

        $arr = model('api/Upload')->upload($files,'picture',$dirname);

        if(is_array($arr)){
            $return['code'] = 1;
            $return['msg'] = 'Upload successful';
            $return['data'] = $arr;
        }else{
            $return['code'] = 1;
            $return['msg'] = model('api/Upload')->getError();
        }
        return json($return);
    }

    /* 文件上传 */
    public function uploadFile()
    {   
        //自定义目录名，暂只支持云存储
        $dirname = input('dirname','','text');
        $files = request()->file();

        if (empty($files)) {
            $return['code'] = 0;
            $return['msg'] = 'No file upload or server upload limit exceeded';
            return json($return);
        }

        $arr = model('api/Upload')->upload($files,'file',$dirname);

        if(is_array($arr)){
            $return['code'] = 1;
            $return['msg'] = 'Upload successful';
            $return['data'] = $arr;
        }else{
            $return['code'] = 1;
            $return['msg'] = model('api/Upload')->getError();
        }
        return json($return);
    }
    /**
     * 用户头像上传
     * @return [type] [description]
     */
    public function uploadAvatar()
    {
        $uid = $aUid = input('uid',0,'intval');
        //无uid时尝试获取
        if($uid == 0){
            $aUid = $uid || is_login();
        }
        
        if($aUid <= 0){
            $return['code'] = 0;
            $return['msg'] = 'Uid Error';
            return json($return);
        }
        /* 调用文件上传组件上传文件 */
        $files = request()->file();
        
        if (empty($files)) {
            $return['code'] = 0;
            $return['msg'] = 'No Avatar Image upload or server upload limit exceeded';
            return json($return);
        }
        
        $arr = model('api/Upload')->upload($files,'avatar','avatar',$aUid);

        if(is_array($arr)){
            $return['code'] = 1;
            $return['msg'] = 'Upload successful';
            $return['data'] = $arr;
        }else{
            $return['code'] = 1;
            $return['msg'] = model('api/Upload')->getError();
        }

        return json($return);
    }
    /**
     * [ueditor 编辑器方法]
     * @return [type] [description]
     */
    public function ueditor(){

        $action = $this->request->param('action');
        switch($action){
            case 'config':
                $result = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(ROOT_PATH.'public/static/common/lib/ueditor/php/config.json')), true);
                break;

            case 'uploadimage':
                $files = request()->file();
                if (empty($files)) {
                    $return['code'] = 0;
                    $return['msg'] = 'No file upload or server upload limit exceeded';
                    return json($return);
                }

                $arr = model('api/Upload')->upload($files,'picture');

                $result['state'] ='SUCCESS';
                $result['url'] = $arr[0]['path'];
                
                break;
            case 'uploadscrawl':
                $files = input('upfile');
                if (empty($files)) {
                    $return['code'] = 0;
                    $return['msg'] = 'No file upload or server upload limit exceeded';
                    return json($return);
                }

                $arr = model('api/Upload')->upload($files,'base64');

                $result['state'] ='SUCCESS';
                $result['url'] = $arr['path'];

                break;

            case 'uploadfile':

                $files = request()->file();

                if (empty($files)) {
                    $return['code'] = 0;
                    $return['msg'] = 'No file upload or server upload limit exceeded';
                    return json($return);
                }

                $arr = model('api/Upload')->upload($files,'file');

                if(is_array($arr)){
                    $result['state'] ='SUCCESS';
                    $result['url'] = $arr[0]['savepath'];
                    $result['original'] = $arr[0]['name'];
                }else{
                    $result['state'] = 'error';
                    $result['msg'] = model('api/Upload')->getError();
                }
                return json($result);

                break;
            default:
                break;
        }
        return json($result);
    }

}
