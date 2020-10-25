<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\CommonController;
use Think\Controller;
/**
 * 上传控制器
 * @author jry <598821125@qq.com>
 */
class PublicUploadController extends CommonController{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
       // parent::_initialize();
    }

    /**
     * 上传
     * @author jry <598821125@qq.com>
     */
    public function upload(){
        exit(D('PublicUpload')->upload());
    }

    /**
     * 下载
     * @author jry <598821125@qq.com>
     */
    public function download($id){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $public_upload_object = D('PublicUpload');
        if(!$public_upload_object->download($id)){
            $this->error($public_upload_object->getError());
        }

    }

    /**
     * KindEditor编辑器下载远程图片
     * @author jry <598821125@qq.com>
     */
    public function downremoteimg(){
        exit(D('PublicUpload')->downremoteimg());
    }

    /**
     * KindEditor编辑器文件管理
     * @author jry <598821125@qq.com>
     */
    public function fileManager(){
        exit(D('PublicUpload')->fileManager());
    }
}
