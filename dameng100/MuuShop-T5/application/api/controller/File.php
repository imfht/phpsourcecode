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
        $files = request()->file();
        if (empty($files)) {
            $return['code'] = 0;
            $return['msg'] = 'No file upload or server upload limit exceeded';
            return json($return);
        }

        $arr = model('api/Upload')->upload($files,'picture');

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
        $files = request()->file();

        if (empty($files)) {
            $return['code'] = 0;
            $return['msg'] = 'No file upload or server upload limit exceeded';
            return json($return);
        }

        $arr = model('api/Upload')->upload($files,'file');

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
    public function uploadAvatar(){

        $aUid = is_login();

        /* 调用文件上传组件上传文件 */
        $file = request()->file('file');

        if (empty($file)) {
            $return['code'] = 0;
            $return['msg'] = 'No file upload or server upload limit exceeded';
            return json($return);
        }
        $return = [];
        //获取上传驱动
        $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        //构建返回数据
        $data['driver'] = $driver;
        $data['uid'] = $aUid;
        if($driver == 'local'){
            $info = $file
            ->validate(['size'=>2*1024*1024,'ext'=>'jpg,png,gif'])
            ->rule('uniqid')
            ->move(ROOT_PATH . 'public' . DS . 'uploads'  . DS . 'avatar' . DS . $aUid);

            if($info){
                // 成功上传后 获取上传信息
                $data['path'] = DS . 'uploads'  . DS . 'avatar' . DS . $aUid . DS . $info->getSaveName();
                $return['code'] = 1;
                $return['msg'] = 'Upload successful';
                $return['data'] = $data;
            }else{
                $return['code'] = 0;
                $return['msg'] = $file->getError();
            }
        }else{
            //获取驱动配置
            $uploadConfig = get_upload_config($driver);
            //文件本地路径
            $filePath = $file->getRealPath();
        }
        
        //返回
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
