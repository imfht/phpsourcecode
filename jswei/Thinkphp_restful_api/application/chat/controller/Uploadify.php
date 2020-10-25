<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:37:51+08:00

namespace app\chat\controller;

use app\common\model\Qiniu;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
use think\facade\Cache;
use think\facade\Config;

class Uploadify{

    /**
     * 上传到七牛
     * @param string $file
     * @return \think\response\Json
     * @throws \Exception
     */
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
            return json(['code'=>1,'msg'=>'上传大小超过5M']);
        }
        if(!$file->checkExt($config['ext'])){
            return json(['code'=>1,'msg'=>'不允许的上传类型']);
        }
        list($ret, $error) = $upManager->putFile($token,md5(microtime(true)),$file->getInfo()['tmp_name']);
        if($error){
            return json(['code'=>1,'msg'=>"上传失败:{$error}"]);
        }
        $pic = new Qiniu();
        $path = Config::get('qiniu.domain').$ret['key'];
        $res = $pic->addNew($info['name'],$path,$info['size'],$info['type'],$ext[1],$out);
        if(!$res){
            return json(['code'=>1,'msg'=>$out]);
        }
        return json(['code'=>0,'msg'=>'上传成功','data'=>['src'=>$path,'name'=>$info['name']]]);
    }
}
