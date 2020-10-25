<?php
    class UploadAction extends Action {
        // 文件上传
        public function upload_tp() {
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 3145728;// 设置附件上传大小
            $upload->allowExts  = array('txt','rar','zip','jpg','jpeg','gif','png','swf','wmv','avi','wma','mp3','mid');// 设置附件上传类型
            $upload->savePath =  '../Uploads/';// 设置附件上传目录
           
            if(!$upload->upload()) {// 上传错误提示错误信息
                //echo $upload->getErrorMsg();
                echo 'error';
            }else{// 上传成功
                $info = $upload->getUploadFileInfo();
                echo __ROOT__.'/Uploads/'.$info[0]['savename'];
            }
        }
    ////////////////////////////////////////////////////////////////////////
       public function upload_ke_json(){
               
                $php_path = '../Uploads/Kindeditor/';
                $php_url = __ROOT__.'/Uploads/Kindeditor/';
                
                import('ORG.Net.UploadFile');
                $upload = new UploadFile();
                $upload->allowExts = array('gif', 'jpg', 'jpeg', 'png', 'bmp','swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb','doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz'); //允许上传类型
                
                $upload->savePath = $php_path;
                $upload->upload();
                
                //取得最后一次错误信息
                $uploaderror = $upload->getErrorMsg();
                //取成功返回信息
                $jieguo = $upload->getUploadFileInfo();
               
                //错误信息空和有返回成功信息则返回URL
                if($uploaderror=='' && $jieguo){
                    $this->ajaxReturn (array('error' => 0, 'url' => $php_url.$jieguo[0]['savename']));
                    exit ();
                }else{
                    $this->ajaxReturn (array('error' => 1, 'message' => $uploaderror));
                    exit ();
                }
       }
           
            //编辑器文件浏览器
       public function upload_ke_manager(){
                
                $root_path = '../Uploads/Kindeditor/';
                $root_url = __ROOT__.'/Uploads/Kindeditor/';
                
                $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
                /*
                //目录名
                $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
                if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
                    echo "无效的目录名";
                    exit;
                }
                
                if ($dir_name !== '') {
                    $root_path .= $dir_name . "/";
                    $root_url .= $dir_name . "/";
                    if (!file_exists($root_path)) {
                        mkdir($root_path);
                    }
                }*/
               
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
                echo realpath($root_path);
                //排序形式，name or size or type
                $order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);
               
                //不允许使用..移动到上一级目录
                if (preg_match('/\.\./', $current_path)) {
                    echo '没有权限访问';
                    exit;
                }
                //最后一个字符不是/
                if (!preg_match('/\/$/', $current_path)) {
                    echo '无效的参数';
                    exit;
                }
                //目录不存在或不是目录
                if (!file_exists($current_path) || !is_dir($current_path)) {
                    echo '文件夹不存在';
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
                
                $this->ajaxReturn ( $result );
        }
           
    }