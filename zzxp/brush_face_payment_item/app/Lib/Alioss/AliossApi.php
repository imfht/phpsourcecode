<?php
namespace App\Lib\Alioss;

use OSS\OssClient;
use OSS\Core\OssException;
use App\Lib\Alioss\Common;


class AliossApi
{
    protected $bucket;
    protected $ossClient;

    public function __construct()
    {
        $this->bucket = Common::getBucketName();
        $this->ossClient = Common::getOssClient();
    }

    function uploadStaticFile($file, $name = null)
    {
        $data['msg'] = '';
//        var_dump($file); die;
        if(!empty($name)){
            $file_path = 'static/'.$name;
        }else{
            $file_path = $file;
        }
        if (is_null($this->ossClient)) exit(1);
        try {
            $result = $this->ossClient->uploadFile($this->bucket, $file_path, $file);
            $data['img_url'] = $result['oss-request-url'];
        } catch (OssException $e) {
            $data['msg'] = $e->getMessage();
        }
        unlink($file);
        return $data;
    }

    /**
     * @param string $file 路径文件
     * @param string null $name 文件名字
     * @return string 异常消息|文件链接
     */
    function uploadFile($file, $name = null,$file_path = '')
    {
        $data['msg'] = '';
//        var_dump($file); die;
        if(empty($file_path)) {
            $file_path = 'uploads/' . date("Ym") . '/' . date("d") . '/';
           empty($name) ? $file_path .= time() : $file_path .= $name;
            //给文件名加个随机，防止出现重复的情况
            $file_path .= rand().'.jpg';
        }else{
           empty($name) ? $file_path .= time() : $file_path .= $name;
            $file_path .= '.jpg';
        }
        //$file_path .= '.jpg';
        if (is_null($this->ossClient)) exit(1);
        try {
            $result = $this->ossClient->uploadFile($this->bucket, $file_path, $file);
            $data['img_url'] = $result['oss-request-url'];
        } catch (OssException $e) {
            $data['msg'] = $e->getMessage();
        }
        unlink($file);
        return $data;
    }

    function uploadCarFile($file, $name = null)
    {
        $data['msg'] = '';
        $file_path = 'public/' . 'wx' . '/' . 'rent_car' . '/';
        empty($name) ? $file_path .= time() : $file_path .= $name;
        $file_path .= '.jpg';
        if (is_null($this->ossClient)) exit(1);
        try {
            $result = $this->ossClient->uploadFile($this->bucket, $file_path, $file);
            $data['img_url'] = $result['oss-request-url'];
        } catch (OssException $e) {
            $data['msg'] = $e->getMessage();
        }
        unlink($file);
        return $data;
    }

    /**
     * @param string $file 路径文件
     * @return mixed
     */
    function deleteObject($file)
    {
        $data['msg'] = '';
        try {
            $this->ossClient->deleteObject($this->bucket, $file);
        } catch (OssException $e) {
            $data['msg'] = $e->getMessage();
        }
        $this->ossClient->doesObjectExist($this->bucket, $file) ? $data['status'] = false : $data['status'] = true;
        return $data;
    }


}