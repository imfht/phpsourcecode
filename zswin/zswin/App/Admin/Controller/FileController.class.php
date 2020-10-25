<?php

namespace Admin\Controller;
use Think\Upload;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends CommonController {

    /* 文件上传 */
    public function upload(){
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
            $return['data'] = think_encrypt(json_encode($info['download']));
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
    //keditor编辑器上传图片处理
	public function ke_upimg(){
		/* 返回标准数据 */
		$return  = array('error' => 0, 'info' => '上传成功', 'data' => '');
		$img = $this->editorupload();
		/* 记录附件信息 */
		if($img['status']){
			
			$return['url'] = $img['fullpath'];
			unset($return['info'], $return['data']);
		} else {
			$return['error'] = 1;
			$return['message']   = $info['info'];
		}
        $this->ajaxReturn($return);
		/* 返回JSON数据 */
		//exit(json_encode($return));
	}
    //keditor编辑器上传图片处理1
	public function editorupload(){
		
		/* 上传配置 */
		$setting = C('EDITOR_UPLOAD');
        $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
        $root_path = realpath(C('EDITOR_UPLOAD.rootPath')). DIRECTORY_SEPARATOR;
	if ($dir_name !== '') {
	$root_path .= $dir_name . "/";
	
	if (!file_exists($root_path)) {
		mkdir($root_path);
	}
}	
		$setting['rootPath']=$setting['rootPath'].$dir_name . "/";
		/* 调用文件上传组件上传文件 */
		$this->uploader = new Upload($setting, 'Local');
		$info   = $this->uploader->upload($_FILES);
		if($info){
			$url = $setting['rootPath'].$info[imgFile]['savepath'].$info[imgFile]['savename'];
			$url = str_replace('./', '/', $url);
			$info['fullpath'] = __ROOT__.$url;
			$info['status'] = 1;
		}else{
			$info['status'] = 0;
            $info['info']   = $this->uploader->getError();
			
		}
		
		return $info;
	}
 public function editorfilemanager(){
 	
$root_path = realpath(C('EDITOR_UPLOAD.rootPath')). DIRECTORY_SEPARATOR;
$root_url =C('EDITOR_UPLOAD.rootPath');

//图片扩展名
$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

//目录名
$dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);

echo $dir_name;
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
    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture(){
        //TODO: 用户登录检测

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器

        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['Picture'], $return);
            //$return['data'] =$info;
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }
           
        /* 返回JSON数据 */
        $this->ajaxReturn($return);
 
    }
}
