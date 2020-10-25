<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\asset\controller;
use app\common\controller\Base;
use think\Request;
class Upload extends Base {
private $path;
private $ext;
private $returnpath;
public function _initialize(){
     $this->path = ROOT_PATH . 'public' . DS . 'uploads/'.$dir;
     $this->ext = trim(getset('allowed_upload_types'),",");
     $this->returnpath='/uploads/'.$dir;
}
public function uploads($dir="weupload"){
    // 获取表单上传文件
    $file = $this->request->file('file');
    if (empty($file)) {
        $this->error('请选择上传文件');
    }
    // 移动到框架应用根目录/public/uploads/ 目录下
    // $info = $file->validate(['ext' => 'jpg,png'])->move($this->path,true,false);
    $info = $file->validate(['ext' => $this->ext])->move($this->path,true,false);
    if ($info) {
        return json(['status'=>1,'msg'=>'success','file'=>str_replace("\\", "/", $this->returnpath.$info->getSaveName())]);
    } else {
        // 上传失败获取错误信息3
        return json(['status'=>0,'msg'=>$this->returnpath.$info->getSaveName(),'file'=>""]);
    }

  }

function saveImage($info, $data, $length, $bucket) {
    //$key = md5(time().$info['name']);
    $key = substr($info['name'], 0, strlen(strrchr($info['name'], '.')) * -1);
    if ($info['type'] == 'image/gif') {
        $key = $key . '.gif';
    } else if (in_array($info['type'], array('image/jpeg', 'image/pjpeg'))) {
        $key = $key . '.jpg';
    } else {
        $key = $key . '.png';
    }
    $object = $key;
    $upload_file_options = array('content' => $data, 'length' => $length);
 // print_r($upload_file_options);
    $oss_sdk_service = new ALIOSS();

    $upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket, $object, $upload_file_options);
    if ($upload_file_by_content->body) {
        return array("code" => -1, "content" => $upload_file_by_content->body);
    } else {
        return array("code" => 1, "key" => $key);
    }
}
 
  
 
  
 

}


