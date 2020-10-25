<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/12/16
 * Time: 6:59 PM
 */
include_once 'Upload.php';
trait FileUpload
{

    /**
     * 根据保存路径对图片进行保存
     * @param $path 路径
     * @param int $size 图片文件上传大小
     * @return array|bool 保存成功返回文件保存的路径，失败返回false
     */
    public function imgUpload($path,$size=1235145728){
        $result=array();
        $upload=new Upload();
        $upload->maxSize=$size;//设置附件大小
//        $upload->exts=array('jpg','gif','png','jpeg');//允许上传的图片格式
        $upload->exts=array('jpg','gif','png','jpeg','bmp','swf','doc','xls','ppt','mid','wav','zip','rar','pdf','chm','rm','txt','docx','xlsx');
        $upload->savePath=trim($path,"/")."/";//设置上传目录
        $info=$upload->upload($_FILES);
        if(!$info)return false;
        foreach($info as $file){
            $result[$file['key']]='/uploads/'.$file['savepath'].$file['savename'];//文件路径
        }
        return $result;
    }
    /**
     * 根据保存路径对图片进行保存
     * @param $path 路径
     * @param int $size 图片文件上传大小
     * @return array|bool 保存成功返回文件保存的路径，失败返回false
     */
    public function picUpload($path,$size=1235145728){
        $result=array();
        $upload=new Upload();
        $upload->maxSize=$size;//设置附件大小
        $upload->exts=array('jpg','gif','png','jpeg');//允许上传的图片格式
        $upload->savePath=trim($path,"/")."/";//设置上传目录
        $info=$upload->upload($_FILES);
        if(!$info)return false;
        foreach($info as $file){
            $result[$file['key']]='/uploads/'.$file['savepath'].$file['savename'];//文件路径
        }
        return $result;

    }
    public function staticUpload($path,$size=1235145728){
        $result=array();
        $upload=new Upload();
        $upload->maxSize=$size;//设置附件大小
        $upload->exts=array('jpg','gif','png','jpeg');//允许上传的图片格式
        $upload->savePath=trim($path,"/")."/";//设置上传目录
        $info=$upload->upload($_FILES);
        if(!$info)return false;
        foreach($info as $file){
            $result[$file['key']]='/uploads/'.$file['savepath'].$file['savename'];//文件路径
        }
        return $result;
    }
}