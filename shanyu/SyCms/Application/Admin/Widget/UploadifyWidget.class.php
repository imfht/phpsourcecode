<?php
namespace Admin\Widget;
use Think\Controller;

class UploadifyWidget extends Controller {
	public function img($name='',$value='',$config=''){
		if(empty($name)) return false;

		$this->assign('name',$name);
		$this->assign('value',$value);
    	
		//单文件上传直接替换
		if(!empty($value)){
			$file=$this->getFileByValue($value);
			if(!empty($config)){
				$config=array_merge($file,$config);
			}else{
				$config=$file;
			}
		}
		$this->assign('config',$config);

		$this->display(MODULE_PATH.'Widget/Tpl/Uploadify/img.html');
	}

    public function file($name='',$value='',$config=''){
    	if(empty($name)) return false;
    	
		$this->assign('name',$name);
		$this->assign('value',$value);

		//单文件上传直接替换
		if(!empty($value)){
			$file=$this->getFileByValue($value);
			if(!empty($config)){
				$config=array_merge($file,$config);
			}else{
				$config=$file;
			}
		}
		$this->assign('config',$config);

		$this->display(MODULE_PATH.'Widget/Tpl/Uploadify/file.html');
    }	

	private function getFileByValue($value){
		$file=array();

		//获取附件的数据库数据
		$conf=pathinfo($value);
		$map=array(
			'name'=>$conf['filename'],
			'dirname'=>trim($conf['dirname'],'/Uploads/'),
			'ext'=>$conf['extension'],
		);
		$file=M('Attachment')
			->field('id as file_id,path as file_path,name as file_name,ext as file_ext')
			->where($map)
			->find();

		return $file;
	}

    public function images($name='',$value='',$config=''){

		$this->assign('config',$config);
		$this->assign('name',$name);
		$this->assign('value',$value);

		$this->display(MODULE_PATH.'Widget/Tpl/Uploadify/images.html');
    }

	public function server(){
		//替换附件
		$config=array();
		$post=I('post.','');
		//单个图片时直接进行替换
		if($post['file_id']){
		    $config = array(
		    	'file_id'       =>  $post['file_id'],
		        'subName'       =>  $post['file_path'], //保存路径
		        'saveName'      =>  $post['file_name'], //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		        'saveExt'       =>  $post['file_ext'], //文件保存后缀，空则使用原后缀
		        'replace'       =>  true, //存在同名是否覆盖
		    );
		}
		//缩略图
		if($post['thumb']){
			$config['thumb']=true;
			$config['width']=intval($post['width']);
			$config['height']=intval($post['height']);
		}
		//水印
		if($post['water']){
			$config['water']=true;
		}

		$file = D('Common/Attachment') -> uploadOne($config);
		if(!$file){ echo D('Common/Attachment')->getError(); exit;}

		$result=array(
			'id'=>$file['id'],
			'title'=>$file['title'],
			'path'=>'/Uploads/'.$file['path'].'/'.$file['name'].'.'.$file['ext'],
		);
		$this->ajaxReturn($result);
	}



}