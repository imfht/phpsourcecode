<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:37:51+08:00

namespace app\admin\controller;

class Uploadify{
    /**
     * 上传头像
     * @param string $filename
     * @param array $crop
     * @param int $quality
     * @return array
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


    /**
     * 上传头像
     * @param string $file
     * @return \think\response\Json
     */
    public function webUploader($file='file'){
        $file = request()->file($file);
        $path = DIRECTORY_SEPARATOR .'uploads'. DIRECTORY_SEPARATOR .'uploadify'.
                DIRECTORY_SEPARATOR . 'auth';
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
     * @param string $name
     * @param int $w
     * @param int $h
     * @param int $output
     * @return array
     */
    public function upload($name="image",$w=0,$h=0,$output=0){
        $file = request()->file($name);
        $path = config('app.UPLOAD.UPLOAD_PATH'). DIRECTORY_SEPARATOR ."{$name}s";
        $valid = config('app.UPLOAD.UPLOAD_IMAGE');
        if(!$file){
            return ['status'=>0,'message'=>lang('empty',[lang($name)])];
        }
        $info = $file->validate($valid)->move($path);
        if ($info) {
            $path = $info->getPathName();
            if($w && $h) {
                $this->_image_thumb($path,$w,$h);
            }
            if($output){
                echo  request()->Domain().substr($path,1);
            }else{
                return  ['status'=>1,'path'=> request()->Domain().substr($path,1)];
            }
        } else {
            return ['status'=>0,'message'=>$file->getError()];
        }
    }

    /**
     * 删除文件
     * @param string $path
     * @return array
     */
    public function delete($path=''){
        if(!$path){
            return ['status'=>0,'msg'=>'图片地址不能为空'];
        }
        $_path = '.'.str_replace(request()->Domain(),'',$path);
        if(!unlink($_path)){
            return ['status'=>0,'msg'=>'删除失败'];
        }
        return  ['status'=>1,'msg'=>'删除成功','path'=>$_path];
    }

    /**
     * 裁切
     * @param $path
     * @param int $w
     * @param int $h
     * @param int $quality
     */
    protected function _image_thumb($path,$w=220,$h=80, $type=null,$quality=80)
    {
        $image = \think\Image::open($path);
        $image->thumb($w, $h,\think\Image::THUMB_SCALING)
            ->save($path,$type,$quality);
    }
}
