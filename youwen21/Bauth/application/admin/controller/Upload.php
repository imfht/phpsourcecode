<?php
namespace app\admin\controller;

use think\File;

class Upload extends Base
{
    private $_rule=[
        'size' => 4000000,
        'type' => 'image/gif,image/jpeg,image/bmp',
        'ext' => 'gif,jpg,jpeg,bmp,png,swf',
    ];

    public function index()
    {
        $fileArr = $this->request->file();
        $savePath = './Uploads/Admin/';
        foreach ($fileArr as $key => $fileObj) {
            if(!$fileObj->check($this->_rule)){
                $this->error($fileObj->getError());
            }
            $ret = $fileObj->move($savePath);
            if(false == $ret){
                $error = $fileObj->getError();
                return response(['code'=>600, 'errorFile'=>$error], 600, [], 'json');
            }else{
                $fileName = $savePath.$ret->getSaveName();
                $fileName = ltrim($fileName, '.');
                return response(['code'=>200, 'successFile'=>$fileName], 200, [], 'json');
            }
            break;
        }
    }

    public function multipleUpload()
    {
        $fileArr = $this->request->file();
        $successFiles = [];
        $errorFiles = [];
        $savePath = './Uploads/Admin/';
        foreach ($fileArr as $key => $fileObj) {
            $ret = $fileObj->move($savePath);
            if(false == $ret){
                $errorFiles[] = $fileObj->getError();
            }else{
                $successFiles[] = $savePath.$ret->getSaveName();
            }
        }
        if(!empty($errorFiles)){
            return response(['successFiles'=>$successFiles, 'errorFiles'=>$errorFiles], 600, [], 'json');
        }else{
            return response(['successFiles'=>$successFiles, 'errorFiles'=>$errorFiles], 200, [], 'json');
        }
    }

    // 上传图片 base64图片
    public function upload_base64()
    {
        $typeAllow = ['image/gif','image/jpeg','image/bmp'];

        // 获取APP客户端传来的base64图片内容
        $image = $this->request->post('image');
        // 对HTML5图片上传的支持  HTML5会给base64图片加前缀
        if(false !== strpos($image, 'data:')){
            $img = substr($image, strpos($image, ',')+1);
            $img = base64_decode($img);
        }else{
            $img = base64_decode($image);
        }
        try{
            $info = getimagesizefromstring($img);
            if(false == $info){
                throw new \Exception('bad image');
            }
            if(!in_array($info['mime'], $typeAllow)){
                throw new \Exception('Type not allowed');
            }
            $file = $this->_createFileName();
            $ret = file_put_contents($file, $img);
            if(!$ret)
                throw new \Exception('Write failed');
        }catch (\Exception $e){
            switch ($e->getMessage()) {
                case 'getimagesizefromstring(): Read error!':
                    $this->error( '不是有效的图片文件');
                    break;
                case 'bad image':
                    $this->error( '图片处理失败');
                    break;
                case 'Type not allowed':
                    $this->error( '不是允许的图片类型');
                    break;
                case 'Too big':
                    $this->error( '图片大小不合格');
                    break;
                case 'Write failed':
                    $this->error( '写入图片失败，可能是权限问题，请检查');
                    break;
                default:
                    $this->error( '上传错误');
                    break;
            }
        }
        // return ['code'=>0, 'successFile'=>ltrim($file, '.')];
        return $this->success($this->request->domain().ltrim($file, '.'), '');
    }

    private function _createFileName($ext = '.jpg', $savePath = './Uploads/')
    {
        $path = $savePath. date('Ymd');
        $this->_makePath($path);
        $name = $this->uid.'_'.uniqid();
        return $path.'/'.$name.$ext;
    }
    private function _makePath($path)
    {
        if(!is_dir($path)){
            if(!mkdir($path)){
                failReturn('9999', '创建目录失败');
            }
        }
        return true;
    }

}
