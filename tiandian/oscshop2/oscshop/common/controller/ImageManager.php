<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 * 
 * 多用户图片管理器(只显示自己目录下的图片)
 * 
 * 图片只能一张张上传
 */
namespace osc\common\controller;
class ImageManager extends Base{
	
	//用户图片目录
	private $user_image_dir;
	//图片保存路径
	private $upload_url;	
	
	protected function _initialize(){
		parent::_initialize();		
	}
	
	protected function init($uid){			
		
		$this->user_image_dir=$uid;
		
		$this->upload_url=DIR_IMAGE . 'images/'.$this->user_image_dir;
		
		if (!is_dir($this->upload_url)) {
			
			mkdir($this->upload_url, 0777);
			chmod($this->upload_url, 0777);
			fopen($this->upload_url.'/index.html','wb');
		}
	}
	
	
	
	//图片和文件夹展示
	public function index(){
		
		$data=input('param.');			
		
		if (isset($data['directory'])) {
			$directory = $data['directory'];
		} else {
			$directory = '';
		}		
		//新建文件夹
		$data['folder_url']=url('file_manager/folder',['directory'=>$directory]);
		//图片上传
		$data['upload_url']=url('file_manager/upload',['directory'=>$directory]);
		//删除
		$data['delete_url']=url('file_manager/delete');
		//搜索
		$data['search_url']=url('file_manager/index',['directory'=>$directory]);
		
		$file_data=$this->get_file_data($data); 
		
		$data['images'] = $file_data['images'];
		
		$data['pagination'] = $file_data['pagination'];

		$data['parent'] = $this->get_parent_url($data); 		

		$data['refresh'] =$this->get_refresh_url($data);		
		
		$this->assign('data',$data);	
		
		$this->assign('heading_title','图片管理器');	
			
		exit($this->fetch());	
	}
	
	public function get_dir($d){
		$dir='';
		foreach ($d as $k => $v) {					
				$dir.=$v.'/';					
		}
		$dir = substr($dir,0,strlen($dir)-1); 
		return $dir;
	}
	
	//图片上传
	public function upload(){
		
		$json = [];
		
		$data=input('param.');

		// Make sure we have the correct directory
		if (isset($data['directory'])) {
			
			$d=explode('-', $data['directory']);
			$dir='';
			if(count($d)>1){
				$dir = $this->get_dir($d); 
			}else{
				$dir=$data['directory'];
			}			
			
			$directory = rtrim($this->upload_url.'/' . str_replace(array('../', '..\\', '..'), '',$dir), '/');

		} else {
			$directory = $this->upload_url;
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = '不是一个文件夹';
		}

		$files=$_FILES;
		
		if (!$json) {
			if (!empty($files['file']['name']) && is_file($files['file']['tmp_name'])) {
				// Sanitize the filename
				$filename = basename(html_entity_decode($files['file']['name'], ENT_QUOTES, 'UTF-8'));

				// Validate the filename length
				if ((mb_strlen($filename) < 3) || (mb_strlen($filename) > 255)) {
					$json['error'] = '文件名长度错误,必须大于3小于255';
				}
			
				if(!preg_match('/^[A-Za-z0-9\-\_.]+$/', $filename)){
					$json['error'] = '文件名,只允许包含字母、数字、下划线、破折号';
				}
				
				// Allowed file extension types
				$allowed = array(
					'jpg',
					'jpeg',
					'gif',
					'png'
				);

				if (!in_array(mb_strtolower(mb_substr(strrchr($filename,'.'),1,mb_strlen(strrchr($filename, '.')))), $allowed)) {
					$json['error'] = '文件类型错误';
				}

				// Allowed file mime types
				$allowed = array(
					'image/jpeg',
					'image/pjpeg',
					'image/png',
					'image/x-png',
					'image/gif'
				);

				if (!in_array($files['file']['type'], $allowed)) {
					$json['error'] = '文件类型错误';
				}

				// Return any upload error
				if ($files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = '上传失败'. $files['file']['error'];
				}
			} else {
				$json['error'] = '上传失败';
			}
		}

		if (!$json) {
			move_uploaded_file($files['file']['tmp_name'], $directory . '/' . $filename);

			$json['success'] = '上传成功';
		}
		return $json;

	}
	//新建文件夹
	public function folder(){
		
		$json =[];
		
		$data=input('param.');
		
		// Make sure we have the correct directory
		if (isset($data['directory'])) {
			
			$d=explode('-', $data['directory']);
			$dir='';
			if(count($d)>1){				
				
				$dir = $this->get_dir($d); 
				
			}else{
				$dir=$data['directory'];
			}			
			
			$directory = rtrim($this->upload_url.'/' . str_replace(array('../', '..\\', '..'), '',$dir), '/');

		} else {
			$directory = $this->upload_url;
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = '文件夹不存在';
		}

		if (!$json) {
			
			$post=input('post.');

			// Sanitize the folder name
			$folder = str_replace(array('../', '..\\', '..'), '', basename(html_entity_decode($post['folder'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((mb_strlen($folder) < 1) || (mb_strlen($folder) > 128)) {
				$json['error'] = '文件夹长度必须大于0小于128';
			}
		
			if( $folder==$this->user_image_dir){
					$json['error'] = '非法名称';
			}
		
			if(!preg_match('/^[A-Za-z0-9]+$/', $folder)){
					$json['error'] = '文件夹名称,只允许包含字母、数字';
			}
			
			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = '文件夹已经存在';
			}
		}

		if (!$json) {
			mkdir($directory . '/' . $folder, 0777);
			chmod($directory . '/' . $folder, 0777);
			fopen($directory . '/' . $folder.'/index.html','wb');
			$json['success'] = '创建成功';
		}
		
		return $json;

	}
	//删除图片和文件夹
	public function delete(){
		
		$json =[];
		
		$post=input('post.');
		
		if (isset($post['path'])) {
			$paths = $post['path'];
		} else {
			$json['error'] = '非法操作';
			
			return $json;			
		}

		// Loop through each path to run validations
		foreach ($paths as $path) {
			$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

			// Check path exsists
			if ($path == DIR_IMAGE . 'images'||$path == DIR_IMAGE.'images/'.$this->user_image_dir) {
				$json['error'] = '非法操作';

				break;
			}
		}

		if (!$json) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = array();

					// Make path into an array
					$path = array($path . '*');

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							@unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							@rmdir($file);
						}
					}
				}
			}

			$json['success'] ='删除成功';
		}

		return $json;
	}
	//取得图片文件，文件夹数据，分页数据
	public function get_file_data($data){
		
		if (isset($data['filter_name'])) {
			$filter_name = rtrim(str_replace(array('../', '..\\', '..', '*'), '', $data['filter_name']), '/');
		} else {
			$filter_name = null;
		}
		
		//验证是否有这个文件夹
		if (isset($data['directory'])) {
			
			$d=explode('-', $data['directory']);
			$dir='';
			if(count($d)>1){				
				
				$dir = $this->get_dir($d); 
			}else{
				$dir=$data['directory'];
			}			
			
			$directory = rtrim($this->upload_url.'/' . str_replace(array('../', '..\\', '..'), '',$dir), '/');
			
		} else {
			$directory = $this->upload_url;
		}
		if (isset($data['page'])) {
			$page = $data['page'];
		} else {
			$page = 1;
		}		
		
		$images_and_directory =[];
		
		//取得文件夹
		$directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);
	
		if (!$directories) {
			$directories = array();
		}

		//取得文件
		$files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

		if (!$files) {
			$files = array();
		}

		//合并文件夹和文件
		$images = array_merge($directories, $files);

		//计算文件和文件夹数量
		$image_total = count($images);

		// Split the array based on current page number and max number of items per page of 10
		$images = array_splice($images, ($page - 1) * 16, 16);
				
		if(empty($images)){
			return ['images'=>null,'pagination'=>null];die;
		}		
		
		foreach ($images as $image) {
			$name = str_split(basename($image), 14);

			if (is_dir($image)) {
				$url =[];
				
				$path=mb_substr($image, mb_strlen(DIR_IMAGE),mb_strlen($image));
				
				$p=explode('/', $path);
				
				if(count($p)>1){
					
					$d='';					
					foreach ($p as $k => $v) {						
						if($v!='images'&&$v!=$this->user_image_dir){							
								$d.=$v.'-';												
						}
					}
					$d = substr($d,0,strlen($d)-1); 
					$url['directory']=$d;
					
				}else{
					$url['directory']=end($p);
				}
				
				if (isset($data['target'])) {
					$url['target']=$data['target'];
				}

				if (isset($data['thumb'])) {
					$url['thumb']=$data['thumb'];
				}			
	
				$images_and_directory['images'][] = array(
				
					'thumb' => '',
					'name'  => implode(' ', $name),
					'type'  => 'directory',
					'path'  => $path,
					'href'=>url('file_manager/index',$url)
				);
				
				
			} elseif (is_file($image)) {

				$images_and_directory['images'][] = array(
					'thumb' => resize(mb_substr($image, mb_strlen(DIR_IMAGE),mb_strlen($image)), 100, 100),
					'name'  => implode(' ', $name),
					'type'  => 'image',
					'path'  => mb_substr($image, mb_strlen(DIR_IMAGE),mb_strlen($image)),
				);
			}
		}
		
		//分页用链接
		$url = [];
		
		if (isset($data['directory'])) {
			$url['directory']=$data['directory'];
		}

		if (isset($data['filter_name'])) {
			$url['filter_name']=html_entity_decode($data['filter_name'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($data['target'])) {
			$url['target']=$data['target'];
		}

		if (isset($data['thumb'])) {
			$url['thumb']=$data['thumb'];
		}
		
		$pagination = new \oscshop\Pagination();
		$pagination->total = $image_total;
		$pagination->page = $page;
		$pagination->limit = 16;
		
		$pagination->url =url('file_manager/index',$url);
		
		$images_and_directory['pagination'] = $pagination->render();
		
		
		return $images_and_directory;
		
	}

	//取得上一级的链接
	public function get_parent_url($data){
		
		$url = [];

		if (isset($data['directory'])) {
			
			$d=explode('-', $data['directory']);			
			
			$dir='';
			if(count($d)>1){
				
				for ($i=0; $i <(count($d)-1); $i++) {
					 
					if($i<(count($d)-2)){
						$dir.=$d[$i].'-';
					}else{
						$dir.=$d[$i];
					}
				}				
				
				$url['directory']=$dir;
			}			
			
		}

		if (isset($data['target'])) {
			$url['target']=$data['target'];
		}

		if (isset($data['thumb'])) {
			$url['thumb'] =$data['thumb'];
		}	
				
		return url('file_manager/index',$url);
	}
	
	//取得刷新的链接(重新加载当前页面的数据)
	public function get_refresh_url($data){
		
		$url = [];

		if (isset($data['directory'])) {			
			$url['directory']=$data['directory'];			
		}

		if (isset($data['target'])) {
			$url['target']=$data['target'];
		}

		if (isset($data['thumb'])) {
			$url['thumb'] =$data['thumb'];
		}
		
				
		return url('file_manager/index',$url);
	}
	
	//用于ckeditor图片上传
	function ckupload(){
		$dir = ROOT_PATH . '/public/uploads/images/ckeditor/'.date('Ymd',time());
		if (!is_dir($dir)) {			
			mkdir($dir, 0777);
			chmod($dir, 0777);
			fopen($dir.'/index.html','wb');
		}
		$files=$_FILES;
		$json = [];

		if (!empty($files['upload']['name']) && is_file($files['upload']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(html_entity_decode($files['upload']['name'], ENT_QUOTES, 'UTF-8'));
			
			// Allowed file extension types
			$allowed = array(
				'jpg',
				'jpeg',
				'gif',
				'png'
			);

			if (!in_array(mb_strtolower(mb_substr(strrchr($filename, '.'), 1,mb_strlen(strrchr($filename, '.')))), $allowed)) {
				$json['error'] = '文件类型错误';
			}
			
			$ext=mb_strtolower(mb_substr(strrchr($filename, '.'), 1,mb_strlen(strrchr($filename, '.'))));
			
			// Allowed file mime types
			$allowed = array(
				'image/jpeg',
				'image/pjpeg',
				'image/png',
				'image/x-png',
				'image/gif'
			);

			if (!in_array($files['upload']['type'], $allowed)) {
				$json['error'] = '文件类型错误';
			}

			// Return any upload error
			if ($files['upload']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = '上传失败'. $files['upload']['error'];
			}
		} else {
			$json['error'] = '上传失败';
		}
		

		if (!$json) {			
			$ckNum=input('param.CKEditorFuncNum');
			//文件名
			$filename=build_order_no().'.'.$ext;
			//缩略图源文件路径
			$thumb_path='images/ckeditor/'.date('Ymd',time()).'/'.$filename;
			//源文件保存路径
		 	$savepath=request()->domain().'/public/uploads/images/ckeditor/'.date('Ymd',time()).'/'.$filename;
			//保存源文件
			move_uploaded_file($files['upload']['tmp_name'], $dir . '/' . $filename);		
			//生成缩略图
			$thumb=request()->domain().'/'.resize($thumb_path,config('ck_image_width'));			
			//删除原图
			@unlink(DIR_IMAGE.'images/ckeditor/'.date('Ymd',time()).'/'.$filename);			
       		//下面的输出，会自动的将上传成功的文件路径，返回给编辑器。
        	echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$ckNum.",'$thumb','');</script>";
		}else{
			echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$ckNum.", '/', '上传失败," . $json['error'] . "！');</script>";
		}
		
	}
}
?>