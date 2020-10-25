<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @公共图片上传控制器
 */
namespace Home\Controller;
use Common\Controller\FrontendController;

class ImagesController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		// 访问者控制
		if (!$this->visitor && in_array ( ACTION_NAME, array (
				'add',
				'delete'	
		) )) {
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = $this->visitor['userid'];
		}
		$this->_mod = D('Common/Images');		
	}
	public function add() {
		$typeid  = $this->_post('typeid','intval','0');
		$file = $_FILES ['file'];
		$type = $this->_post('type','trim');
		$userid = $this->userid;
		// 上传
		if (! empty ( $file['name'] )) {
			$data_dir = date ( 'Y/md/H' );
			/*$result = savelocalfile($file, $type . '/' . $data_dir,
					array (
					'width'=>C('ik_simg.width').','.C('ik_mimg.width').','.C('ik_bimg.width'),
					'height'=>C('ik_simg.height').','.C('ik_mimg.height').','.C('ik_bimg.height')
					),
					array('jpg','jpeg','png','gif'));*/
		    
			$result = \Common\Util\Upload::saveLocalFile(
				$type . '/' . $data_dir.'/', 
				array(
					'width'=>C('ik_simg.width').','.C('ik_mimg.width').','.C('ik_bimg.width'),
					'height'=>C('ik_simg.height').','.C('ik_mimg.height').','.C('ik_bimg.height')
				));
							
			if ($result ['error']) {	
				$arrJson = array('r'=>1, 'html'=> $result ['error']);
				echo json_encode($arrJson);
			} else {
				
				$name = $result ['savename'];
				$path = $result ['savepath'];
				$size = $result ['size'];
				$title = $result ['name'];
				$photoid = $this->_mod->addImage($name,$path,$size,$title,$type,$typeid,$userid);
				//浏览该$photoid下的照片
				$arrPhoto = $this->_mod->getImageById($photoid);
				$arrJson = array(
						'id'=> $photoid,
						'layout'=> $arrPhoto['align'],
						'title'=>'',
						'seq_id'=> $arrPhoto['seqid'],
						'small_photo_url'=> $arrPhoto['simg'],
						'ajaxurl' => U('home/images/delete'),
				);
				echo json_encode($arrJson);
			}
		}else{
			$arrJson = array('r'=>1, 'html'=> '请选择图片再上传！');
			return $arrJson;
		}
	}
	// 删除图片
	public function delete(){
		$id = $this->_post('id','intval');
		$seqid = $this->_post('seq_id','intval');
		if($id>0){
			$this->_mod->delImage($id);
			$arrJson = array('r'=>0, 'html'=> '删除成功');
			echo json_encode($arrJson);
		}
	}

}