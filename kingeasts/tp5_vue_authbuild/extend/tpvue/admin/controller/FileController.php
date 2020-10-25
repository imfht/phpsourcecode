<?php
// 文件控制器     
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\controller;



use tpvue\admin\library\Upload;

class FileController extends BaseController
{

    /**
     * 上传
     * @return [type] [description]
     */
    public function upload()
    {
        $upload = new Upload();
        $return = $upload->upload();
        return json($return);
    }
    /**
     *[uploadAvatar] 上传图像操作
     * @return [type] [description]
     */
    public function uploadAvatar($uuid=0)
    {
        $return = [
            'status' =>1,
            'path'   =>'/public/images/defalut.jpg',
            'msg'    =>'提示成功'
        ];
        return json($return);
    }
   
}
