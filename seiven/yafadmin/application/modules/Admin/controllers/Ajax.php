<?php
class AjaxController extends AdminController {
    function actlistAction(){
        $controller = $this->request->getQuery('controller');
        if($controller){
            $controllerList = SystemRights::getControllers();
            if(isset($controllerList[$controller])) $this->displayAjax(true, '', $controllerList[$controller]);
        }
        $this->displayAjax(true);
    }
    /**
     * find uploader
     */
    public function fineUploadAction(){
        set_time_limit(0);
        // Include the upload handler class
        $uploader = new fineUploader();
        
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = explode(',', $this->config->upload->ext); // all files types allowed by default
        $fineUploaderDir = $this->config->upload->dirname;
        $fineUploaderDir = rtrim($fineUploaderDir, '/') . '/';
        // Specify max file size in bytes.
        $uploader->sizeLimit = null;
        
        // Specify the input name set in the javascript.
        $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
                                         
        // If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = $fineUploaderDir . DIRECTORY_SEPARATOR . "tmp/chunks";
        
        $method = $_SERVER["REQUEST_METHOD"];
        if($method == "POST"){
            header("Content-Type: text/plain");
            // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
            // For example: /myserver/handlers/endpoint.php?done
            if(isset($_GET["done"])){
                $result = $uploader->combineChunks($fineUploaderDir . 'tmp');
            }else{
                // Handles upload requests
                // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
                $result = $uploader->handleUpload($fineUploaderDir . 'tmp');
                // To return a name used for uploaded file you can use the following line.
                $result["uploadName"] = $uploader->getUploadName();
                // 获取文件后缀
                $result["uploadExt"] = strtolower(end(explode('.', $result["uploadName"])));
            }
            echo json_encode($result);
        }else if($method == "DELETE"){
            // for delete file requests
            $result = $uploader->handleDelete($fineUploaderDir . 'tmp');
            echo json_encode($result);
        }else{
            echo json_encode(array(
                'error'=> "Server error. no method" 
            ));
        }
        die();
    }
    /**
     * kindEditor upload
     */
    public function uploadKindAction(){
        $php_path = $this->config->upload->dirname;
        $php_url = $this->config->upload->staticurl;
        // 文件保存目录路径
        $save_path = rtrim($php_path, '/') . '/';
        // 文件保存目录URL
        $save_url = rtrim($php_url, '/') . '/';
        // 定义允许上传的文件扩展名
        $ext_arr = array();
        if ($this->config->upload->dir->image) $ext_arr['image'] = explode(',', $this->config->upload->dir->image);
        if ($this->config->upload->dir->flash) $ext_arr['flash'] = explode(',', $this->config->upload->dir->flash);
        if ($this->config->upload->dir->media) $ext_arr['media'] = explode(',', $this->config->upload->dir->media);
        if ($this->config->upload->dir->file) $ext_arr['file'] = explode(',', $this->config->upload->dir->file);
        // 最大文件大小
        $max_size = $this->config->upload->maxsize;
        
        // PHP上传失败
        if(!empty($_FILES['imgFile']['error'])){
            switch($_FILES['imgFile']['error']){
                case '1' :
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2' :
                    $error = '超过表单允许的大小。';
                    break;
                case '3' :
                    $error = '图片只有部分被上传。';
                    break;
                case '4' :
                    $error = '请选择图片。';
                    break;
                case '6' :
                    $error = '找不到临时目录。';
                    break;
                case '7' :
                    $error = '写文件到硬盘出错。';
                    break;
                case '8' :
                    $error = 'File upload stopped by extension。';
                    break;
                case '999' :
                default :
                    $error = '未知错误。';
            }
            die(json_encode(array(
                'error'=> 1,
                'message'=> $error 
            )));
        }
        // 默认新定义目录
        if(isset($_GET['dir2'])){
            $dir_name = empty($_GET['dir2']) ? 'image' : trim($_GET['dir2']);
        }else{
            $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        }
        
        // 有上传文件时
        if(empty($_FILES) === false){
            // 原文件名
            $file_name = $_FILES['imgFile']['name'];
            // 服务器上临时文件名
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            // 文件大小
            $file_size = $_FILES['imgFile']['size'];
            // 检查文件名
            if(!$file_name){
                $error = '请选择文件。';
            }elseif(is_dir($save_path) === false){
                // 检查目录
                $error = '上传目录不存在。';
            }elseif(is_writable($save_path) === false){
                // 检查目录写权限
                $error = '上传目录没有写权限。';
            }elseif(is_uploaded_file($tmp_name) === false){
                // 检查是否已上传
                
                $error = '上传失败(upload fail)';
            }elseif($file_size > $max_size){
                // 检查文件大小
                $error = '上传文件大小超过限制。' . $file_size;
            }elseif(empty($ext_arr[$dir_name])){
                // 检查目录名
                $error = '目录名不正确。';
            }
            if($error) die(json_encode(array(
                'error'=> 1,
                'message'=> $error 
            )));
            // 获得文件扩展名
            $temp_arr = explode('.', $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            // 检查扩展名
            if(in_array($file_ext, $ext_arr[$dir_name]) === false){
                die(json_encode(array(
                    'error'=> 1,
                    'message'=> "上传文件扩展名是不允许的扩展名。" 
                )));
            }
            // 创建文件夹
            if($dir_name !== ''){
                $save_path .= $dir_name . "/";
                $save_url .= $dir_name . "/";
                if(!file_exists($save_path)){
                    mkdir($save_path);
                }
            }
            $ymd = date('Ymd');
            $save_path .= $ymd . "/";
            $save_url .= $ymd . "/";
            if(!file_exists($save_path)){
                mkdir($save_path);
            }
            // 新文件名
            $new_file_name = date('YmdHis') . '_' . rand(10000, 99999) . '.' . $file_ext;
            // 移动文件
            $file_path = $save_path . $new_file_name;
            if(move_uploaded_file($tmp_name, $file_path) === false){
                die(json_encode(array(
                    'error'=> 1,
                    'message'=> "上传失败(move fail)" 
                )));
            }
            chmod($file_path, 0644);
            $file_url = $save_url . $new_file_name;
            
            header('Content-type: text/html; charset=UTF-8');
            die(json_encode(array(
                'error'=> 0,
                'url'=> $file_url 
            )));
        }
    }
    // kindEditor 文件空间
    public function fileManagerKindAction(){
        $php_path = $this->config->upload->dirname;
        $php_url = $this->config->upload->staticurl;
        
        // 根目录路径，可以指定绝对路径，比如 /var/www/attached/
        $root_path = rtrim($php_path, '/') . '/';
        // 根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
        $root_url = rtrim($php_url, '/') . '/';
        // 图片扩展名
        $ext_arr = array();
        if ($this->config->upload->dir->image) $ext_arr['image'] = explode(',', $this->config->upload->dir->image);
        if ($this->config->upload->dir->flash) $ext_arr['flash'] = explode(',', $this->config->upload->dir->flash);
        if ($this->config->upload->dir->media) $ext_arr['media'] = explode(',', $this->config->upload->dir->media);
        if ($this->config->upload->dir->file) $ext_arr['file'] = explode(',', $this->config->upload->dir->file);
        
        // 目录名
        $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
        if(!in_array($dir_name, array(
            '',
            'image',
            'flash',
            'media',
            'file' 
        ))){
            echo "上传目录不正确";
            exit();
        }
        if($dir_name !== ''){
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";
            if(!file_exists($root_path)){
                mkdir($root_path);
            }
        }
        
        // 根据path参数，设置各路径和URL
        if(empty($_GET['path'])){
            $current_path = realpath($root_path);
            $current_path = rtrim($current_path, '/') . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        }else{
            $current_path = realpath($root_path) . '/' . urldecode($_GET['path']);
            $current_url = $root_url . $_GET['path'];
            $current_dir_path = $_GET['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        // echo realpath($root_path);
        // 排序形式，name or size or type
        $order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);
        
        // 不允许使用..移动到上一级目录
        if(preg_match('/\.\./', $current_path)){
            echo 'Access is not allowed.';
            exit();
        }
        // 最后一个字符不是/
        if(!preg_match('/\/$/', $current_path)){
            echo 'Parameter is not valid.';
            exit();
        }
        // 目录不存在或不是目录
        if(!file_exists($current_path) || !is_dir($current_path)){
            echo 'Directory does not exist.';
            exit();
        }
        
        // 遍历目录取得文件信息
        $file_list = array();
        if($handle = opendir($current_path)){
            $i = 0;
            while( false !== ($filename = readdir($handle)) ){
                if($filename{0} == '.') continue;
                $file = $current_path . $filename;
                if(is_dir($file)){
                    $file_list[$i]['is_dir'] = true; // 是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); // 文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; // 文件大小
                    $file_list[$i]['is_photo'] = false; // 是否图片
                    $file_list[$i]['filetype'] = ''; // 文件类别，用扩展名判断
                }else{
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; // 文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); // 文件最后修改时间
                $i++;
            }
            closedir($handle);
        }
        
        // usort($file_list, 'cmp_func');
        
        $result = array();
        // 相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        // 相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        // 当前目录的URL
        $result['current_url'] = str_replace('%2F', '/', $current_url);
        // 文件数
        $result['total_count'] = count($file_list);
        // 文件列表数组
        $result['file_list'] = $file_list;
        
        // 输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
}