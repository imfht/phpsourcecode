<?php 
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Org\Util\String;

/**
 * 管理问卷签名图片
 */
class SignimageController extends CommonController
{
	function _initialize()
	{
		parent::_initialize();

    	$this->bcItemPush('问卷管理', U('Questionnaire/index'));
    	$this->bcItemPush('问题管理', U('Questions/index', array('questionnaire_id'=>I('questionnaire_id/d'))));		
	}

	/* 罗列签名图片 */
    public function index()
    {
        $this->bcItemPush('签名图管理');

        $questionnaire_id = I('get.questionnaire_id/d');
        $this->assign('questionnaire_id', $questionnaire_id);

        $signimages = M('Signimages');
        $this->assign('signimages', $signimages->field(true)->where("questionnaire_id=$questionnaire_id")->select());
        $this->assign('totalNum', $signimages->count());
        $this->assign('activeNum', $signimages->where("status='on'")->count());

        $this->display();
    }

    /* 上传图片 */
    public function upload()
    {
        $config = array(
        	'maxSize'	=>	3145728,
        	'exts'		=>	array('jpg', 'jpeg', 'png', 'gif', 'bmp'),
        	'rootPath'	=>	'./Public/',
        	'savePath'	=>	'Signimages/',
        	'autoSub'	=>	false,
        );

        $upload = new \Think\Upload($config);
        $info = $upload->upload();

        $info OR $this->error( "图片上传错误, 请重试, 详细信息：{$upload->getError()}" ); //上传失败

        /* 上传成功, 上传文件信息入库 */
        foreach ($info as $file) {
        	$dataAll[] = array(
        		'path'				=>	"/Public/{$config['savePath']}{$file['savename']}",
        		'questionnaire_id'	=>	I('get.questionnaire_id/d'),
        	);
        }

        $Signimages = M('Signimages');
        $state = $Signimages->addAll($dataAll);

        if( $state===false ){
	        $this->error("图片添加失败, 请重试, 详细信息：{$Signimages->getDbError()}");
        }else{
	        $this->success('图片上传成功');
        }
    }

    /* 删除图片 */
    public function delete()
    {
        if( is_array(I('id')) ){ //批量删除
            $ids = I('id', array(), 'intval');
        }else{ //单个删除
            $ids[] = I('id/d');
        }

        $Signimages = M('Signimages');

        foreach ($ids as $id) {
	    	/* 删除文件 */
	    	$path = $Signimages->getFieldById($id, 'path');
	    	$abPath = ".$path";
	    	unlink($abPath) OR $this->error('图片文件删除失败');

        	/* 删除表记录 */
	        $state = $Signimages->delete($id);
	    	$state===false && $this->error("图片表记录删除失败, 详细信息: {$Signimages->getDbError()}");
        }

 		$this->success("图片删除成功");
    }

    /* 更新图片启用状态 */
    public function updateStatus()
    {
    	$statusList = I('post.status');
    	$Signimages = M('Signimages');

    	foreach ($statusList as $id => $status) {
    		$state = $Signimages->where("id=$id")->setField('status', $status);
    		$state===false && $this->error("图片状态更新出错, 详细信息：$Signimages->getDbError()");
    	}

    	$this->success('图片状态更新成功');
    }

}
?>