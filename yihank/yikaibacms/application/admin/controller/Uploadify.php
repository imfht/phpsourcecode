<?php
namespace app\admin\controller;

class Uploadify extends Admin{

    /**
     * 上传图片 后台专用
     * @access  public
     * @null int 一次上传图片张图
     * @elementid string 上传成功后返回路径插入指定ID元素内
     * @path  string 指定上传保存文件夹,默认存在Public/upload/temp/目录
     * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
     */
    public function upload(){
        $func = input('func');
        $path = input('path','temp');
        $info = array(
        	'num'=> input('num'),
            'title' => '',       	
        	'upload' =>url('Admin/Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images','session_id'=>session_id())),
            'size' => '4M',
            'type' =>'jpg,png,gif,jpeg',
            'input' => input('input'),
            'func' => empty($func) ? 'undefined' : $func,
        );
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    /*
              删除上传的图片
     */
    public function delupload(){
        $action=isset($_GET['action']) ? $_GET['action'] : null;
        $filename= isset($_GET['filename']) ? $_GET['filename'] : null;
        $filename= str_replace('../','',$filename);
        $filename= trim($filename,'.');
        $filename= trim($filename,'/');
        if($action=='del' && !empty($filename)){
            $size = getimagesize($filename);
            $filetype = explode('/',$size['mime']);
            if($filetype[0]!='image'){
                return false;
                exit;
            }
            unlink($filename);
            exit;
        }
    }

}