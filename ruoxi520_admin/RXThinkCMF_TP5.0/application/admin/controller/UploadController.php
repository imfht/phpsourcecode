<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 文件上传-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-12
 */
namespace app\admin\controller;
class UploadController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 上传图片
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function uploadImg()
    {
        
        // 设置上传约束
        $config = [
            'size' => 10000000,
            'ext'  => 'jpg,gif,png,bmp,jpeg,JPG'
        ];
        $file = $this->request->file('file');
        $upload_path = UPLOAD_TEMP_PATH;
        $info = $file->validate($config)->move($upload_path);
        if($info) {
            //上传成功
            $file_path = IMG_URL . "/temp/" . $info->getSaveName();
            return message("上传成功",true,$file_path);
            
        }else {
            //上传失败
            $error = $file->getError();
            return message($error,false);
        }
        
    }
    
}