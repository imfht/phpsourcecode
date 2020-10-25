<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;

/**
 * 附件模块
 * @author Lain
 *
 */
class AttachmentController extends AdminController {
	
	//初始化
	public function _initialize(){
		$action = array(
				//'permission'=>array('changePassword'),
				//'allow'=>array('index')
		);
		// B('Admin\\Behaviors\\Authenticate', '', $action);
	}
    
	//上传
	public function uploadJson(){
		$dir = I('get.dir');
		
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		
		// 设置附件上传类型
		switch ($dir){
			case 'image':
				$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', 'svg');
				break;
			case 'file':
				$upload->exts      =     array('txt');
				break;
			default:
				$this->ajaxReturn(array('error' => 1, 'message' => '参数错误'));
		}
		$upload->rootPath  =     C('UPLOAD_PATH'); // 设置附件上传根目录
		$upload->savePath  =     $dir.'/'; // 设置附件上传（子）目录
		// 上传文件
		$info   =   $upload->upload();
		$img_path = $upload->rootPath.$info['imgFile']['savepath'].$info['imgFile']['savename'];
		$img_url =  C('UPLOAD_URL').$info['imgFile']['savepath'].$info['imgFile']['savename'];
		if(!$info) {
			// 上传错误提示错误信息
			$this->ajaxReturn(array('error' => 1, 'message' => $upload->getError()));
		}else{
			// 上传成功
			$downloadedfile = array('viewhost'=> C('UPLOAD_URL'), 'filename'=>$info['imgFile']['name'], 'filepath'=>$img_path, 'filesize'=>$info['imgFile']['size'], 'fileext'=>$info['imgFile']['ext'], 'authcode' => $info['imgFile']['md5'], 'savepath'=>$info['imgFile']['savepath'], 'savename'=>$info['imgFile']['savename']);
			D('Attachment')->saveData($downloadedfile, $dir);

			// 水印
			$image = new \Lain\Phpcms\image(1);
			if(1) {
				$image->watermark($img_path, $img_path);
			}
			$this->ajaxReturn(json_encode(array('error' => 0, 'url' => $img_url)), 'EVAL');
		}
	}
	
	//新版文件管理
	public function fileManagerJson(){
		$root_path = '';
		$module = I('get.dir');
		if (!in_array($module, array('image', 'flash', 'media', 'file'))) {
			echo "Invalid Directory name.";
			exit;
		}
		$path = I('get.path');

		$map['userid'] = session('userid');
		$map['module'] = $module;
		if(!$path){
			$current_dir_path = '';
			//返回目录(image)
			//取出viewhost
			/* $arr_paths = D('Attachment')->where($map)->group('viewhost')->field('viewhost')->select();
			foreach ($arr_paths as $k=>$v){
				$file_detail = array();
				//文件夹的名字, 去掉当前目录
				$file_detail['filename'] = $v['viewhost'];
					
				$file_detail['has_file'] = true;
				$file_detail['is_dir'] = true;
				$file_detail['is_photo'] = false;
					
				$file_list[$k] = $file_detail;
			} */
			//取出savepath
			$arr_paths = D('Attachment')->where($map)->group('savepath')->field('savepath')->select();
			foreach ($arr_paths as $k=>$v){
				$file_detail = array();
				//文件夹的名字, 去掉当前目录
				$file_detail['filename'] = str_replace(array('image/', 'flash/', 'media/', 'file/', '/'), '', $v['savepath']);
				
				$file_detail['has_file'] = true;
				$file_detail['is_dir'] = true;
				$file_detail['is_photo'] = false;
				
				$file_list[$k] = $file_detail;
			}
		}else{
			$current_dir_path = $path;
			//返回当前用户可以操作的图片
			//映射
			$field = 'savename, savepath, uploadtime, filepath, filesize, fileext, isimage';
			$map['savepath'] = $module.'/'.$path;
			if(I('get.order')){
				$order_field = I('get.order');
				switch ($order_field){
					case 'NAME':
						$order = 'savename DESC';
						break;
					case 'TYPE':
						$order = 'fileext DESC';
						break;
					case 'SIZE':
						$order = 'filesize DESC';
						break;
					default:
						$order = 'uploadtime DESC';
				}
			}
			$arr_attachments = D('Attachment')->where($map)->order($order)->field($field)->select();
			foreach ($arr_attachments as $k => $v){
				$file_detail = array();
				$file_detail['datetime'] = date('Y-m-d H:i:s', $v['uploadtime']);
				$file_detail['filename'] = $v['savename'];
				
				$file_detail['filesize'] = $v['filesize'];
				$file_detail['filetype'] = $v['fileext'];
				$file_detail['has_file'] = false;
				$file_detail['is_dir'] = false;
				$file_detail['is_photo'] = $v['isimage'] ? true : false;
				
				$file_list[$k] = $file_detail;
			}
		}
		$result = array();
		$current_url = C('UPLOAD_URL').$module.'/'.$path;
		//相对于根目录的上一级目录
		$result['moveup_dir_path'] = '';
		//相对于根目录的当前目录
		$result['current_dir_path'] = $current_dir_path;
		//当前目录的URL
		$result['current_url'] = $current_url;
		//文件数
		$result['total_count'] = count($file_list);
		//文件列表数组
		$result['file_list'] = $file_list;
		$this->ajaxReturn($result);
	}
	
	//原版文件管理
	public function fileManagerJson1(){
		$php_path = './';
		$php_url = '/';
		
		//根目录路径，可以指定绝对路径，比如 /var/www/attached/
		$root_path = $php_path . 'Uploads/';
		//根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
		$root_url = $php_url . 'Uploads/';
		//图片扩展名
		$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
		
		//目录名
		$dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
		if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
			echo "Invalid Directory name.";
			exit;
		}
		if ($dir_name !== '') {
			$root_path .= $dir_name . "/";
			$root_url .= $dir_name . "/";
			if (!file_exists($root_path)) {
				mkdir($root_path);
			}
		}
		
		//根据path参数，设置各路径和URL
		if (empty($_GET['path'])) {
			$current_path = realpath($root_path) . '/';
			$current_url = $root_url;
			$current_dir_path = '';
			$moveup_dir_path = '';
		} else {
			$current_path = realpath($root_path) . '/' . $_GET['path'];
			$current_url = $root_url . $_GET['path'];
			$current_dir_path = $_GET['path'];
			$moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
		}
		//echo realpath($root_path);
		//排序形式，name or size or type
		$order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);
		
		//不允许使用..移动到上一级目录
		if (preg_match('/\.\./', $current_path)) {
			echo 'Access is not allowed.';
			exit;
		}
		//最后一个字符不是/
		if (!preg_match('/\/$/', $current_path)) {
			echo 'Parameter is not valid.';
			exit;
		}
		//目录不存在或不是目录
		if (!file_exists($current_path) || !is_dir($current_path)) {
			echo 'Directory does not exist.';
			exit;
		}
		
		//遍历目录取得文件信息
		$file_list = array();
		if ($handle = opendir($current_path)) {
			$i = 0;
			while (false !== ($filename = readdir($handle))) {
				if ($filename{0} == '.') continue;
				$file = $current_path . $filename;
				if (is_dir($file)) {
					$file_list[$i]['is_dir'] = true; //是否文件夹
					$file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
					$file_list[$i]['filesize'] = 0; //文件大小
					$file_list[$i]['is_photo'] = false; //是否图片
					$file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
				} else {
					$file_list[$i]['is_dir'] = false;
					$file_list[$i]['has_file'] = false;
					$file_list[$i]['filesize'] = filesize($file);
					$file_list[$i]['dir_path'] = '';
					$file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
					$file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
					$file_list[$i]['filetype'] = $file_ext;
				}
				$file_list[$i]['filename'] = $filename; //文件名，包含扩展名
				$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
				$i++;
			}
			closedir($handle);
		}
		
		//排序
		function cmp_func($a, $b) {
			global $order;
			if ($a['is_dir'] && !$b['is_dir']) {
				return -1;
			} else if (!$a['is_dir'] && $b['is_dir']) {
				return 1;
			} else {
				if ($order == 'size') {
					if ($a['filesize'] > $b['filesize']) {
						return 1;
					} else if ($a['filesize'] < $b['filesize']) {
						return -1;
					} else {
						return 0;
					}
				} else if ($order == 'type') {
					return strcmp($a['filetype'], $b['filetype']);
				} else {
					return strcmp($a['filename'], $b['filename']);
				}
			}
		}
		usort($file_list, 'cmp_func');
		
		$result = array();
		//相对于根目录的上一级目录
		$result['moveup_dir_path'] = $moveup_dir_path;
		//相对于根目录的当前目录
		$result['current_dir_path'] = $current_dir_path;
		//当前目录的URL
		$result['current_url'] = $current_url;
		//文件数
		$result['total_count'] = count($file_list);
		//文件列表数组
		$result['file_list'] = $file_list;

		$this->ajaxReturn($result);
		
	}
	
	//文章模块上传缩略图
	public function ajaxUpload(){
		$dir = I('get.dir');
		
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
		$upload->savePath  =     $dir.'/'; // 设置附件上传（子）目录
		// 上传文件
		$info   =   $upload->upload();
		$img_url = $upload->rootPath.$info['file']['savepath'].$info['file']['savename'];
		if(!$info) {// 上传错误提示错误信息
			$this->ajaxReturn(array('error' => 1, 'message' => $upload->getError()));
		}else{
			// 上传成功
			$downloadedfile = array('filename'=>$info['file']['name'], 'filepath'=>$img_url, 'filesize'=>$info['file']['size'], 'fileext'=>$info['file']['ext'], 'authcode' => $info['file']['md5'], 'savepath'=>$info['file']['savepath'], 'savename'=>$info['file']['savename']);
			D('Attachment')->saveData($downloadedfile);
			$this->ajaxReturn(array('statusCode' => 200, 'message' => "上传成功！", "filename" => $img_url));
		}
	}
	
	//非谷歌浏览器的图片粘贴功能
	public function ajax_pasteImage(){
		$uploader = new \Think\Upload\Driver\Local();
		$data_editor = $_POST['editor'];
		//仿禅道
		$dataLength = strlen($data_editor);
		if(ini_get('pcre.backtrack_limit') < $dataLength) ini_set('pcre.backtrack_limit', $dataLength);
		preg_match_all('/<img src="(data:image\/(\S+);base64,(\S+))".*\/>/U', $data_editor, $out);
		
		$rootPath = '/Uploads/';
		
		$save_path = 'image/'.date('Y-m-d').'/';

		foreach($out[3] as $key => $base64Image)
		{
		    //匹配成功
		    $extension = strtolower($out[2][$key]);
		    $image_name = uniqid().'.'. $extension;
		    $image_file = '.'.$rootPath.$save_path.$image_name;
		    
		    $imageData = base64_decode($base64Image);
		
		    if ($uploader->checkSavePath('.'.$rootPath.$save_path) && file_put_contents($image_file, $imageData)){
				// 上传成功
				$downloadedfile = array('viewhost'=>'/Uploads/', 'filename'=>$image_name, 'filepath'=>$image_file, 'filesize'=> strlen($imageData), 'fileext'=> $extension, 'authcode' => '', 'savepath'=>$save_path, 'savename'=>$image_name);
				D('Attachment')->saveData($downloadedfile);
			}else{
			    die();
			}
		
		    $data_editor = str_replace($out[1][$key], $rootPath.$save_path . $image_name, $data_editor);
		}
		
		$data = array('flag' => true, 'data' => $data_editor);
		$this->ajaxReturn($data);
	}

}