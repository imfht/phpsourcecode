<?php
namespace Admin\Controller;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends AdminController {

    /* 文件上传 */
    public function uploadFiles(){
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
        /* 调用文件上传组件上传文件 */
        $File = D('File');
        $file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
        $info = $File->upload(
            $_FILES,
            C('DOWNLOAD_UPLOAD'),
            C('DOWNLOAD_UPLOAD_DRIVER'),
            C("UPLOAD_{$file_driver}_CONFIG")
        );

        /* 记录附件信息 */
        if($info){
            $return['data'] = $info['download']['path'];
            $return['info'] = $info['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }

    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture(){

		/* 图片上传相关配置 */
		$config = array(
			'mimes'    => '', //允许上传的文件MiMe类型
			'maxSize'  => 8*1024*1024, //上传的文件大小限制 (0-不做限制)
			'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
			'autoSub'  => true, //自动子目录保存文件
			'subName'  => '', //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
			'rootPath' => './', //保存根路径
			'savePath' => '', //保存路径
			'saveName' => time().'_'.mt_rand(), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
			'saveExt'  => '', //文件保存后缀，空则使用原后缀
			'replace'  => false, //存在同名是否覆盖
			'hash'     => true, //是否生成hash编码
			'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
		);

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('File');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
            $_FILES,
            $config,
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG"),
			C("UPLOAD_TYPE_CONFIG")['oss_upload_url']
        ); //TODO:上传到远程服务器

        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }
}
