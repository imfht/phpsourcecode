<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Addons\EditorForAdmin\Controller;
use Home\Controller\AddonsController;
use Think\Upload;
use Admin\Model\PictureModel;

class UploadController extends AddonsController{

	public $uploader = null;

	/* 上传图片 */
	public function upload(){
		session('upload_error', null);
		/* 上传配置 */
		$setting = C('EDITOR_UPLOAD');

		/* 调用文件上传组件上传文件 */
		$this->uploader = new Upload($setting, 'Local');
		$info   = $this->uploader->upload($_FILES);
		if($info){
			$url = C('EDITOR_UPLOAD.rootPath').$info['imgFile']['savepath'].$info['imgFile']['savename'];
			$url = str_replace('./', '/', $url);
			/* 已经存在文件记录 */
			if(isset($info['id']) && is_numeric($info['id'])){
			}else {
			    /* 记录文件信息 */
			    $PictureM = new PictureModel();
			    $value = $info['imgFile'];
			    $value['path']=  substr($setting['rootPath'], 1).$info['imgFile']['savepath'].$info['imgFile']['savename'];	//在模板里的url路径
			    if($PictureM->create($value) && ($id =$PictureM->add())){
			    } else {
			        //TODO: 文件上传成功，但是记录文件信息失败，需记录日志
			        unset($info);
			    }
			}
			$info['fullpath'] = __ROOT__.$url;
		}
		session('upload_error', $this->uploader->getError());
		return $info;
	}

	//keditor编辑器上传图片处理
	public function ke_upimg(){
		/* 返回标准数据 */
		$return  = array('error' => 0, 'info' => '上传成功', 'data' => '');
		$img = $this->upload();
		/* 记录附件信息 */
		if($img){
			$return['url'] = $img['fullpath'];
			unset($return['info'], $return['data']);
		} else {
			$return['error'] = 1;
			$return['message']   = session('upload_error');
		}

		/* 返回JSON数据 */
		exit(json_encode($return));
	}

	//ueditor编辑器上传图片处理
	public function ue_upimg(){

		$img = $this->upload();
		$return = array();
		$return['url'] = $img['fullpath'];
		$title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
		$return['title'] = $title;
		$return['original'] = $img['imgFile']['name'];
		$return['state'] = ($img)? 'SUCCESS' : session('upload_error');
		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}

}
