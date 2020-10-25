<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:37:51+08:00

namespace app\first\controller;

use app\common\model\Qiniu;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
use think\facade\Cache;
use think\facade\Config;

class Uploadify
{
    /**
     * 上传头像
     * @param string $file
     * @param array $crop
     * @param int $quality
     * @return array|string
     */
    public function upload_head($filename='head', $crop=[], $quality=50)
    {
        $file = request()->file($filename);
        $path = config('app.UPLOAD.UPLOAD_PATH'). DIRECTORY_SEPARATOR .'head';
        $valid = config('app.UPLOAD.UPLOAD_IMAGE');

        if(!$file){
            return ['status'=>0,'message'=>lang('empty',[lang($filename)])];
        }
        $info = $file->validate($valid)->move($path);
        if ($info) {
            return  ['status'=>1,'path'=>substr($info->getPathName(),1)];
        } else {
            return ['status'=>0,'message'=>$file->getError()];
        }
    }

    public function uploads($file='file'){
        $bucket =  Config::get('qiniu.bucket');
        $upManager = new UploadManager();
        $auth = new Auth(Config::get('qiniu.accessKey'),Config::get('qiniu.secretKey'));
        $token = Cache::store(Config::get('qiniu.store'))
            ->get('qiniu_access_token');
        if(!$token){
            $token = $auth->uploadToken($bucket);
            Cache::store(Config::get('qiniu.store'))
                ->set('qiniu_access_token',$token,3600);
        }
        $config=Config::get('qiniu.validate');
        $file = request()->file($file);
        $info = $file->getInfo();
        $ext = explode('/',$info['type']);
        if(!$file->checkSize($config['size'])){
            return ['code'=>1,'msg'=>'上传大小超过5M'];
        }
        if(!$file->checkExt($config['ext'])){
            return ['code'=>1,'msg'=>'不允许的上传类型'];
        }
        list($ret, $error) = $upManager->putFile($token,md5(microtime(true)),$file->getInfo()['tmp_name']);
        if($error){
            return ['code'=>1,'msg'=>"上传失败:{$error}"];
        }
        $pic = new Qiniu();
        $path = Config::get('qiniu.domain').$ret['key'];
        $res = $pic->addNew($info['name'],$path,$info['size'],$info['type'],$ext[1],$out);
        if(!$res){
            return ['code'=>1,'msg'=>$out];
        }
        return ['code'=>0,'msg'=>'上传成功','data'=>['src'=>$path,'name'=>$info['name']]];
    }

    /**
     * 上传头像
     * @param string $file
     * @return \think\response\Json
     */
    public function webUploader($file='file')
    {
        $file = request()->file($file);
        $path = DIRECTORY_SEPARATOR .'uploads'. DIRECTORY_SEPARATOR .'uploadify'. DIRECTORY_SEPARATOR . 'auth';
        $config=[
            'size'=>1024*1024*20,
            'ext'=>'jpg,png,gif'
        ];
        $info = $file->validate($config)->move(ROOT_PATH . 'public'.$path);

        if ($info) {
            $fullPath =  $path.DIRECTORY_SEPARATOR.$info->getSaveName();
            return json([
                "jsonrpc" => "2.0",
                'result'=>[
                    'code'=>200,
                    'file'=>$fullPath,
                    'id'=>'id'
                ]
            ]);
        } else {
            return json([
                "jsonrpc" => "2.0",
                'error'=>[
                    'code'=>102,
                    'message'=>"Failed to open output stream.",
                    'id'=>'id'
                ]
            ]);
        }
    }

    /**
     * 裁切
     * @param $path
     * @param $crop
     * @param int $quality
     */
    protected function _image_worker($path, $crop, $quality=80)
    {
        $image = \think\Image::open($path);
        if ($crop) {
            $image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y'])
                ->save($path, null, $quality);
        } else {
            $image->save($path, null, $quality);
        }
    }

    /**
     * 上传七牛
     * @param string $bucket
     * @param string $file
     * @return array
     * @throws \Exception
     */
    public function toQiNiu($bucket='',$file='file'){
        $bucket = $bucket ? $bucket : Config::get('qiniu.bucket');
        $upManager = new UploadManager();
        $auth = new Auth(Config::get('qiniu.accessKey'),Config::get('qiniu.secretKey'));
        $token = Cache::store(Config::get('qiniu.store'))
            ->get('qiniu_access_token');
        if(!$token){
            $token = $auth->uploadToken($bucket);
            Cache::store(Config::get('qiniu.store'))
                ->set('qiniu_access_token',$token,3600);
        }
        $config=Config::get('qiniu.validate');
        $file = request()->file($file);
        $info = $file->getInfo();
        $ext = explode('/',$info['type']);
        if(!$file->checkSize($config['size'])){
            return ['status'=>0,'msg'=>'上传大小超过5M'];
        }
        if(!$file->checkExt($config['ext'])){
            return ['status'=>0,'msg'=>'不允许的上传类型'];
        }
        list($ret, $error) = $upManager->putFile($token,md5(microtime(true)),$file->getInfo()['tmp_name']);
        if($error){
            return ['status'=>0,'msg'=>"上传失败:{$error}"];
        }
        $pic = new Qiniu();
        $path = Config::get('qiniu.domain').$ret['key'];
        $res = $pic->addNew($info['name'],$path,$info['size'],$info['type'],$ext[1],$out);
        if(!$res){
            return ['status'=>1,'msg'=>$out];
        }
        return ['status'=>1,'msg'=>'上传成功','data'=>['pic_id'=>$out,'path'=>$path]];
    }
}
