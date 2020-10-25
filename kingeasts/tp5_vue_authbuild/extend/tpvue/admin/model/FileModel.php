<?php
// 附件模型 
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\model;

class FileModel extends BaseModel
{

    protected $auto  = ['update_time'];
    protected $insert = ['status' => 1,'create_time']; 

    protected function setUidAttr($value)
    {
        return is_login();
    }

    // protected function setCreateTimeAttr($value)
    // {
    //     return time();
    // }
    // protected function setUpdateTimeAttr($value)
    // {
    //     return time();
    // }

    /**
     * 检测当前上传的文件是否已经存在
     * @param  array   $file 文件上传数组
     * @return boolean       文件信息， false - 不存在该文件
     */
    public function isFile($file){
        if(empty($file['md5'])){
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
		$map = array('md5' => $file['md5'],'sha1'=>$file['sha1'],);
        return $this->field(true)->where($map)->find();
    }

    
}
