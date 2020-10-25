<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use think\facade\Env;
class UpFiles extends Common
{
    public function upload(){
        // 获取上传文件表单字段名
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $file = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下

        $info = $file->validate(['ext' => 'jpg,png,gif,jpeg'])->move('uploads');
        if($info){
            $result['code'] = 1;
            $result['info'] = '图片上传成功!';
            $path=str_replace('\\','/',$info->getSaveName());
            $result['url'] = '/uploads/'. $path;
            return $result;
        }else{
            // 上传失败获取错误信息

            $result['code'] =0;
            $result['info'] =  $file->getError();
            $result['url'] = '';
            return $result;
        }
    }
    public function file(){
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext' => 'zip,rar,pdf,swf,ppt,psd,ttf,txt,xls,doc,docx'])->move('uploads');
        if($info){
            $result['code'] = 0;
            $result['info'] = '文件上传成功!';
            $path=str_replace('\\','/',$info->getSaveName());

            $result['url'] = '/uploads/'. $path;
            $result['ext'] = $info->getExtension();
            $result['size'] = byte_format($info->getSize(),2);
            return $result;
        }else{
            // 上传失败获取错误信息
            $result['code'] =1;
            $result['info'] = '文件上传失败!';
            $result['url'] = '';
            return $result;
        }
    }
    public function pic(){
        // 获取上传文件表单字段名
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $file = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext' => 'jpg,png,gif,jpeg'])->move(Env::get('root_path') . 'public/uploads');
        if($info){
            $result['code'] = 1;
            $result['info'] = '图片上传成功!';
            $path=str_replace('\\','/',$info->getSaveName());
            $result['url'] = '/uploads/'. $path;
            return json_encode($result,true);
        }else{
            // 上传失败获取错误信息
            $result['code'] =0;
            $result['info'] = '图片上传失败!';
            $result['url'] = '';
            return json_encode($result,true);
        }
    }
    /**
     * 后台：wangEditor
     * @return \think\response\Json
     */
    public function editUpload(){
        // 获取上传文件表单字段名
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $file = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext' => 'jpg,png,gif,jpeg'])->move('uploads');
        if($info){
            $path=str_replace('\\','/',$info->getSaveName());
            return '/uploads/'. $path;
        }else{
            // 上传失败获取错误信息
            $result['code'] =1;
            $result['msg'] = '图片上传失败!';
            $result['data'] = '';
            return json_encode($result,true);
        }
    }
    //多图上传
    public function upImages(){
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $file = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext' => 'jpg,png,gif,jpeg'])->move(Env::get('root_path') . 'public/uploads');
        if($info){
            $result['code'] = 0;
            $result['msg'] = '图片上传成功!';
            $path=str_replace('\\','/',$info->getSaveName());
            $result["src"] = '/uploads/'. $path;
            return $result;
        }else{
            // 上传失败获取错误信息
            $result['code'] =1;
            $result['msg'] = '图片上传失败!';
            return $result;
        }
    }
    /**
     * 后台：NKeditor
     * @return \think\response\Json
     */
    public function editimg(){
        $allowExtesions = array(
            'image' => 'gif,jpg,jpeg,png,bmp',
            'flash' => 'swf,flv',
            'media' => 'swf,flv,mp3,wav,wma,wmv,mid,avi,mpg,asf,rm,rmvb',
            'file' => 'doc,docx,xls,xlsx,ppt,htm,html,txt,zip,rar,gz,bz2',
        );
        // 获取上传文件表单字段名
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $file = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>$allowExtesions[input('fileType')]])->move('./uploads');
        if($info){
            $path=str_replace('\\','/',$info->getSaveName());
            $url = '/uploads/'. $path;
            $result['code'] = '000';
            $result['message'] = '图片上传成功!';
            $result['item'] = ['url'=>$url];
            return json($result);
        }else{
            // 上传失败获取错误信息
            $result['code'] =001;
            $result['message'] = $file->getError();
            $result['url'] = '';
            return json($result);
        }
    }
}