<?php
namespace plugins\weixin\admin;

use app\common\controller\AdminBase;

class Cert extends AdminBase
{
	
	
	public function config()
    {
		
		$path = ROOT_PATH."data{$webdb['web_dir']}/olpay/weixin_cert_{$webdb['mymd5']}/";
		$cert_file = array('apiclient_cert.pem','apiclient_key.pem','rootca.pem');
		
		if(IS_POST){
			
			$this->post_cert_file('file1');
			$this->post_cert_file('file2');
			$this->post_cert_file('file3');
			
			//if ( $model->save_data($data['postdb']) ) {
                $this->success('更新成功');
           // } else {
                $this->error('更新失败');
           // }
		}
		
		
		$files = '';
		foreach($cert_file AS $value){
			if(is_file($path.$value)){
				$files[] = $value;
			}
		}
		if($files){
			$msg = "你已上传“".implode('，',$files)."”证书！";
		}else{
			$msg = "你还没有上传任何证书！";
		}
		
		return $this->pfetch('config',['msg'=>$msg]);
	}
	
	public function post_cert_file($file){
		global $webdb,$path,$cert_file;
		
		
		
		if(!is_dir($path)){
			makepath($path);
		}
		
		$name = $_FILES[$file][name];
		
		if( !in_array($name, $cert_file) ){
			$this->error("你只能上传这三种文件名的证书“apiclient_cert.pem,apiclient_key.pem,rootca.pem”");
		}
		
		$upfile = $_FILES[$file][tmp_name];
		$newfile = $path.$name;
		
		if(!move_uploaded_file($upfile,$newfile)){

		}elseif(!@copy($upfile,$newfile)){

		}else{
			$this->error('空间有问题，文件上传失败！');
		}
		return true;
		
	}	

}
