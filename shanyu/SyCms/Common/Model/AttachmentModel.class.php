<?php
namespace Common\Model;
use Think\Model;
use Think\Upload;

class AttachmentModel extends Model{
	private $image_ext=array('gif','jpg','png','jpeg');
	private $upload_type='';
	private $allow_ext='';
	private $allow_size='';

	protected function _initialize(){
		$this->upload_type=C('FILE_UPLOAD_TYPE');
		$this->allow_ext=C('UPLOAD_ALLOW');
		$this->allow_size=C('UPLOAD_MAXSIZE')*1024;//UPLOAD_MAXSIZE单位为KB
	}

    public function uploadOne($config=array()){
    	$path = './Uploads/';
        if(!is_dir($path)){
            mkdir($path, 0755, true);
        }elseif(!is_writeable($path)){
            $this->error='备份目录不存在或不可写，请检查后重试！';
            return false;
        }

		$file_data=reset($_FILES);

		//增加自定义条件
		$config['maxSize']=$this->allow_size;
		$config['exts']=$this->allow_ext;
		
    	//上传服务器
		$Upload = new Upload($config);
		$file = $Upload->uploadOne($file_data);
		if(!$file){
			$this->error = $Upload->getError();
			return false;
		}
		
		//使用第三方上传空间时不做管理直接返回
		if($this->upload_type != 'Local'){
			$data['title']=str_replace('.'.$file['ext'],'',$file['name']);
			if(isset($file['url'])){
				$data['url']=$file['url'];
			}else{
				$domain=C('UPLOAD_TYPE_CONFIG.domain');
				$data['url']=$domain.'/Uploads/'.$file['savepath'].$file['savename'];
			}
			$data['ext']=$file['ext'];
			$data['size']=$file['size'];
			return $data;
		}

		//图片处理(缩放|水印)
		if(in_array( $file['ext'], $this->image_ext )){
			$file['type']=1;//图片类型
			if($config['thumb'] || ( C('UPLOAD_WATER') && $config['water']) ){
				$file=$this->doImage($file,$config);
			}
		}

		$data=$this->createData($file);
		//添加数据库
		if($config['file_id']){
			//单文件上传时直接替换原有数据
			$this->where("id={$config['file_id']}")->save($data);
			$data['id']=$config['file_id'];
		}else{
			$file_id=$this->add($data);
			$data['id']=$file_id;
		}

		$data['url']='/Uploads/'.$data['path'].'/'.$data['name'].'.'.$data['ext'];
		//缓存新增附件信息
		$this->addCache($data);

		return $data;
    }

    //图片处理(缩放|水印)
    private function doImage($file=array(),$config=array()){
    	$file_path='Uploads/'.$file['savepath'].$file['savename'];

    	$Image = new \Think\Image();
		$Image->open($file_path);
		//指定缩放尺寸
		if($config['thumb']){
			if($config['height'] && $config['width']){
				$file_height=$config['height'];
				$file_width=$config['width'];
			}else{
				$file_height=C('UPLOAD_THUMB_HEIGHT');
				$file_width=C('UPLOAD_THUMB_WIDTH');
			}
			$Image->thumb($file_width,$file_height,\Think\Image::IMAGE_THUMB_FIXED);
			$file['type']=2;//图片缩略图类型
		}
		//添加水印
		if(C('UPLOAD_WATER') && $config['water']){
			$water=C('UPLOAD_WATER_PATH');
			if(is_file($water)){
				$Image->water($water,9);
			}
		}

		$Image->save($file_path);
		$file['size']=filesize($file_path);
		return $file;
    }
    //创建数据库信息
    private function createData($file){
    	$upload_id=defined('UID') ? UID : 0 ;
    	$title=str_replace('.'.$file['ext'],'',$file['name']);
    	$name=str_replace('.'.$file['ext'],'',$file['savename']);
    	$path=trim($file['savepath'],'/');
    	$type=isset($file['type']) ? $file['type'] : 0;
    	
    	$result=array(
    		'title'=>$title,
    		'name'=>$name,
    		'path'=>$path,
    		'size'=>$file['size'],
    		'ext'=>$file['ext'],
    		'md5'=>$file['md5'],
    		'type'=>$type,
    		'upload_id'=>$upload_id,
    		'upload_ip'=>get_client_ip(),
    		'upload_date'=>date('Y-m-d H:i:s'),
    	);
    	return $result;
    }
    //缓存新增附件信息
	private function addCache($data){
		$cache=session('attachment');
		//判断缓存是否存在,存在附加数组
		if(!empty($cache)){
			$cache[$data['id']]=$data['url'];
			session('attachment',$cache);
		}else{
			session('attachment',array($data['id']=>$data['url']));
		}
		return true;
	}

	//增加信息关联附件处理
    public function addFile($data=array(),$table_id=0,$table_name=''){
    	if(empty($data)) return false;
    	if($this->upload_type!= 'Local') return true;

    	$files=array();
    	//取出POST数据中的附件PATH
		foreach ($data as $v) {
			if(preg_match('/\/Uploads\//', $v)){
				preg_match_all('/\/Uploads\/[\d|-]+\/[\w]+\.[\w]{1,4}/', $v, $file);
				if(empty($file[0])) continue;
				$files=array_merge($files,$file[0]);
			}
		}
		if(empty($files)) return false;

		$cache=session('attachment');
		$file_id=array();
		//对比缓存中存储的附件PATH取出附件ID
		foreach ($files as $v) {
			$file_id[]=array_search($v,$cache);
		}
		if(empty($file_id)) return false;

		//更新数据库文件资料
		$this->updateData($file_id,$table_id,$table_name);

		//清空缓存
		session('attachment',null);

		return true;
    }

	//编辑信息后关联附件处理
    public function editFile($data=array(),$table_id=0,$table_name=''){
    	if(empty($data)) return false;
    	if($this->upload_type!= 'Local') return true;
    	
    	$files=array();
    	//取出POST数据中的附件PATH
		foreach ($data as $v) {
			if(preg_match('/\/Uploads\//', $v)){
				preg_match_all('/\/Uploads\/[\d|-]+\/[\w]+\.[\w]{1,4}/', $v, $file);
				if(empty($file[0])) continue;
				$files=array_merge($files,$file[0]);
			}
		}

		//合并缓存中的附件和数据库的附件
		$cache=session('attachment');
		$_data_file=$this
			->where("table_id={$table_id} AND table_name='{$table_name}'")
			->field('id,path,name,ext')
			->select();
		$data_file=array();
		foreach ($_data_file as $v) {
			$data_file[$v['id']]='/Uploads/'.$v['path'].'/'.$v['name'].'.'.$v['ext'];
		}

		$add_id=$del_id=array();
		//对比缓存中存储的附件PATH取出附件ID
		foreach ($files as $v) {
			if($_add_id=array_search($v,$cache)){
				$add_id[]=$_add_id;
			}
			if($_del_id=array_search($v, $data_file)){
				unset($data_file[$_del_id]);
			}
		}
		$del_id=array_keys($data_file);

		//更新数据库文件资料
		if(!empty($add_id)) $this->updateData($add_id,$table_id,$table_name);
		if(!empty($del_id)) $this->updateData($del_id);
		
		//清空缓存
		session('attachment',null);

		return true;
    }
    private function updateData($file_id=array(),$table_id=0,$table_name=''){
		if(count($file_id)==1){
			$where="id = ".$file_id[0];
		}else{
			$in_str=implode(',', $file_id);
			$where="id IN ({$in_str})";
		}

		$data=array('table_id'=>$table_id,'table_name'=>$table_name);
		$result=$this
			->where($where)
			->save($data);
		return $result;
    }

    //删除信息后关联附件处理
    public function delFile($id=0,$table_name=''){

		$where['table_id']=$id;
		$where['table_name']=$table_name;
		$file=$this
			->field('id,path,name,ext')
			->where($where)
			->select();
		if(!empty($file)){
	        foreach ($file as $v) {
	            $_file_path='Uploads/'.$v['path'].'/'.$v['name'].'.'.$v['ext'];
	            unlink($_file_path);
	        }
	        $this->where($where)->delete();
		}
		return true;
    }

	/**
	 * 下载指定文件
	 * @param  number  $root 文件存储根目录
	 * @param  integer $id   文件ID
	 * @param  string   $args     回调函数参数
	 * @return boolean       false-下载失败，否则输出下载文件
	 */
	public function download($root, $id, $callback = null, $args = null){
		/* 获取下载文件信息 */
		$file = $this->find($id);
		if(!$file){
			$this->error = '不存在该文件！';
			return false;
		}

		/* 下载文件 */
		switch ($file['location']) {
			case 0: //下载本地文件
				$file['rootpath'] = $root;
				return $this->downLocalFile($file, $callback, $args);
			case 1: //TODO: 下载远程FTP文件
				break;
			default:
				$this->error = '不支持的文件存储类型！';
				return false;

		}

	}
	/**
	 * 下载本地文件
	 * @param  array    $file     文件信息数组
	 * @param  callable $callback 下载回调函数，一般用于增加下载次数
	 * @param  string   $args     回调函数参数
	 * @return boolean            下载失败返回false
	 */
	private function downLocalFile($file, $callback = null, $args = null){
		if(is_file($file['rootpath'].$file['savepath'].$file['savename'])){
			/* 调用回调函数新增下载数 */
			is_callable($callback) && call_user_func($callback, $args);

			/* 执行下载 */ //TODO: 大文件断点续传
			header("Content-Description: File Transfer");
			header('Content-type: ' . $file['type']);
			header('Content-Length:' . $file['size']);
			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
				header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
			}
			readfile($file['rootpath'].$file['savepath'].$file['savename']);
			exit;
		} else {
			$this->error = '文件已被删除！';
			return false;
		}
	}


}

/*
微风上传文件说明资料

$_FILES 值
	array(
		'file1' => 
			array (
			'name' => string '08.jpg',
			'type' => string 'image/jpeg',
			'tmp_name' => string 'D:\Wamp Server\tmp\php532A.tmp',
			'error' => int 0,
			'size' => int 186130,
		),
	);

uploadOne Local返回的信息
	array
	(
	    'name' => 005.jpg
	    'type' => image/jpeg
	    'size' => 186010
	    'key' => 0
	    'ext' => jpg
	    'md5' => 760e2b83df813b2e02376aabe411d70d
	    'sha1' => 9ee82f9f92890d7e08c730f86d07d71e56401eee
	    'savename' => 20140818125054.jpg
	    'savepath' => 20140818/
	)
uploadOne Upyun返回的信息
Array
(
    [name] => 其他的啊.jpg
    [type] => image/jpeg
    [size] => 70854
    [key] => 0
    [ext] => jpg
    [md5] => 723f1e42b27447b4f13fdc81d8f0157f
    [sha1] => 62e3e45b1d15d2d4d1ba9bdb75407b3a77bbeb8f
    [savename] => 55be131e3e42b.jpg
    [savepath] => 2015-08-02/
)

upload 返回的信息
	array 
	(
			'file1' => array
			(
			'name' => string '08.jpg', 
			'type' => string 'image/jpeg', 
			'size' => int 186130,
			'key' => string 'file1', //数据表单NAME
			'ext' => string 'jpg', 
			'md5' => string '4d7ce71c8e3e1ec11f6d177e800ec94c', 
			'sha1' => string '7352b2498105ed38d7719dd66c358a57d03e4b79', 
			'savename' => string '20140817115728.jpg', 
			'savepath' => string '20140817/', 
		),
	);

ueditor需要返回的信息
	array(
	     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
	     "url" => "",            //返回的地址
	     "title" => "",          //新文件名
	     "original" => "",       //原始文件名
	     "type" => ""            //文件类型
	     "size" => "",           //文件大小
	)

*/
	//仅适用于有file_id传值的形式
    // public function addFile($table_id=0,$table_name=''){
    // 	$file_id=I('post.file_id','');
    // 	array_filter($file_id);
    	
    // 	if(!empty($file_id)){
    // 		if(count($file_id)==1){
    // 			$where="id = ".$file_id[0];
    // 		}else{
    // 			$in_str=implode(',', $file_id);
    // 			$where="id IN {$in_str}";
    // 		}
    // 		$data=array('table_id'=>$table_id,'table_name'=>$table_name);
    // 		M('File')->where($where)->save($data);
    // 		return true;
    // 	}
    // 	return false;
    // }
