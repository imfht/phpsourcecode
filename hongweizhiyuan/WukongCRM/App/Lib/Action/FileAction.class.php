<?php 

class FileAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('editor', 'add', 'delete','filedownload')
		);
		B('Authenticate', $action);
	}
	
	public function editor(){
		$m_config = M('config');
		if (isset($_FILES['imgFile']['size']) && $_FILES['imgFile']['size'] != null) {
			//如果有文件上传 上传附件
			import('@.ORG.UploadFile');
			//导入上传类
			$upload = new UploadFile();
			//设置上传文件大小
			$upload->maxSize = 20000000;
			//设置上传文件类型
			$defaultinfo = $m_config->where('name = "defaultinfo"')->find();
		    $value = unserialize($defaultinfo['value']);
			$upload->allowExts  = explode(',', $value['allow_file_type']);// 设置附件上传类型
			//设置附件上传目录
			$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
			if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
				$this->error(L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'));
			}
			$upload->savePath = $dirname;
			
			if(!$upload->upload()) {// 上传错误提示错误信息
				//alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
				echo $upload->getErrorMsg(); die();
			}else{// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
			}
		}
		if(is_array($info[0]) && !empty($info[0])){
			$a['error']=0;
			$a['url'] = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER["REQUEST_URI"]).'/'.$dirname . $info[0]['savename'];
			//$this->ajaxReturn($a,'JSON');
			echo json_encode($a);
		}else{
			$this->error('失败');
		};
	}

	public function add(){
		$m_config = M('config');
		if($_POST['submit']){
			if (array_sum($_FILES['file']['size'])) {
				//如果有文件上传 上传附件
				import('@.ORG.UploadFile');
				//导入上传类
				$upload = new UploadFile();
				//设置上传文件大小
				$upload->maxSize = 20000000;
				//设置附件上传目录
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				
				$defaultinfo = $m_config->where('name = "defaultinfo"')->find();
				$value = unserialize($defaultinfo['value']);
				$upload->allowExts  = explode(',', $value['allow_file_type']);// 设置附件上传类型
				
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					$this->error(L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'));
				}
				$upload->savePath = $dirname;
				
				if(!$upload->upload()) {// 上传错误提示错误信息
					alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
				}else{// 上传成功 获取上传文件信息
					$info =  $upload->getUploadFileInfo();
				}
			}

			$m_file = M('File');
			$r_file_module = M($_POST['r']);
			$module = $_POST['module'];
			$m_id = $_POST['id'];
			foreach($info as $key=>$value){
				$data['name'] = $value['name'];
				$data['file_path'] = $value['savepath'].$value['savename'];
				$data['role_id'] = $_POST['role_id'];
				$data['size'] = $value['size'];
				$data['create_date'] = time(); 
				if($file_id = $m_file->add($data)){
					$temp['file_id'] = $file_id;
					$temp[$module . '_id'] = $m_id;
					if(0 >= $r_file_module->add($temp)){
						alert('error', L('ADD_FAILURE_PARTS_ACCESSORIES'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('ADD_ATTACHMENTS_FAIL'), $_SERVER['HTTP_REFERER']);
				};
			}
			alert('success',L('ADD_ATTACHMENTS_SUCCESS'), $_SERVER['HTTP_REFERER']);
		}elseif($_GET['r'] && $_GET['module'] && $_GET['id']){
			$defaultinfo = $m_config->where('name = "defaultinfo"')->find();
			$value = unserialize($defaultinfo['value']);
			$this->allowExts  = $value['allow_file_type'];// 设置附件上传类型
			$this->r = $_GET['r'];
			$this->module = $_GET['module'];
			$this->id = $_GET['id'];
			$this->display();
		}
	} 
		
//	public function delete(){
//		$id = isset($_GET['id']) ? $_GET['id'] : 0;
//		if(0 < $id){
//			$m_file = M('File');
//			$m_file = $m_file->where('file_id = %d', $_GET['id'])->find();
//			if (is_array($m_file) && ($m_file['role_id'] == session('role_id'))) {
//				if($m_file->where('file_id = %d', $_GET['id'])->delete()){
//					alert('success', '操作成功！', $_SERVER['HTTP_REFERER']);
//				}
//			} else {
//				alert('error', '您无权删除此附件！', $_SERVER['HTTP_REFERER']);
//			}			
//		} else {
//			alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
//		}
//	}
	public function delete(){
		$file_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if (0 == $file_id){
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}else{
			if (isset($_GET['r']) && isset($_GET['id'])) {
				$m_r = M($_GET['r']);
				$m_file = M('file');
				$file = $m_file->where('file_id = %d', $_GET['id'])->find();
				
				if (is_array($file) && ($file['role_id'] == session('role_id'))){
					if ($m_r->where('file_id = %d',$_GET['id'])->delete()) {
						if ($m_file->where('file_id = %d',$_GET['id'])->delete()) {
							alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
						}else{
							alert('success',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
						}
					}else {
						alert('success',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('success',L('YOU_HAVE_NO_RIGHT_TO_DELETE_THE_ATTACHMENT'),$_SERVER['HTTP_REFERER']);
				}
			} elseif (empty($_GET['r']) && isset($_GET['id'])){
				$m_file = M('file');
				$file = $m_file->where('file_id = %d', $_GET['id'])->find();
				if (is_array($file) && ($file['role_id'] == session('role_id'))) {
					if($m_file->where('file_id = %d', $_GET['id'])->delete()){
						alert('success', L('OPERATION_IS_SUCCESSFUL'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('YOU_HAVE_NO_RIGHT_TO_DELETE_THE_ATTACHMENT'), $_SERVER['HTTP_REFERER']);
				}
			}
		}
	}

	public function filedownload(){
		$path = trim(urldecode($_GET['path']));
		$name = substr(trim(urldecode($_GET['name'])),0,-4);
		if($path && $name)	download($path,$name);
		else $this->error('非法操作！');
	}
	
	public function manager(){
		error_reporting(0);
		ini_set('display_errors',false); 
		import('@.ORG.Services_JSON');
		if($_GET['dir'] == 'file'){
			//根目录路径，可以指定绝对路径				
			if(is_dir(UPLOAD_PATH.'/Common_files/')){ 
				$root_path = UPLOAD_PATH.'/Common_files/';
			}elseif(is_dir(UPLOAD_PATH)){
				$root_path = UPLOAD_PATH;
			}else{
				$root_path = './';
			}
			
			//根目录URL，可以指定绝对路径				
			if(is_dir(UPLOAD_PATH.'/Common_files/')){ 
				$root_url = UPLOAD_PATH.'/Common_files/';
			}elseif(is_dir(UPLOAD_PATH)){
				$root_url = UPLOAD_PATH;
			}else{
				$root_url = './';
			}
			
			//图片扩展名
			$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp','txt','doc','docx','xsl','ppt','pdf','zip','rar');
		}else{
			//根目录路径，可以指定绝对路径
			if(is_dir(UPLOAD_PATH.'/Common_images/')){ 
				$root_path = UPLOAD_PATH.'/Common_images/';
			}elseif(is_dir(UPLOAD_PATH)){
				$root_path = UPLOAD_PATH;
			}else{
				$root_path = './';
			}
			
			//根目录URL，可以指定绝对路径		
			if(is_dir(UPLOAD_PATH.'/Common_images/')){ 
				$root_url = UPLOAD_PATH.'/Common_images/';
			}elseif(is_dir(UPLOAD_PATH)){
				$root_url = UPLOAD_PATH;
			}else{
				$root_url = './';
			}
			
			//图片扩展名
			$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
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
					$file_ext = strtolower(array_pop(explode('.', trim($file))));
					$file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
					$file_list[$i]['filetype'] = $file_ext;
				}
				$file_list[$i]['filename'] = $filename; //文件名，包含扩展名
				$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
				$i++;
			}
			closedir($handle);
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

		//输出JSON字符串
		header('Content-type: application/json; charset=UTF-8');
		$json = new Services_JSON();
		echo $json->encode($result);

	}
}