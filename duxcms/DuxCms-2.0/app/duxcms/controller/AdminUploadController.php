<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 文件上传
 */
class AdminUploadController extends AdminController
{
    /**
     * 文件上传
     */
    public function upload()
    {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        $file = target('admin/File');
        $info = $file->uploadData();
        if ($info)
        {
            $return['data'] = $info;
        }
        else
        {
            $return['status'] = 0;
            $return['info'] = $file->getError();
        }
        $this->ajaxReturn($return);
    }

    /**
     * 编辑器上传
     */
    public function editor()
    {
        //编辑器无法动态获取参数，使用默认配置
        $file = target('admin/File');
        $info = $file->uploadData();
        if ($info){
            $return = array(
                'error' => 0,
                'url' => $info['url'],
                'info' => $info,
                );
        }else{
            $return = array(
                'error' => 1,
                'message' => $file->getError(),
                );
        }
        $this->ajaxReturn($return);
    }
}

