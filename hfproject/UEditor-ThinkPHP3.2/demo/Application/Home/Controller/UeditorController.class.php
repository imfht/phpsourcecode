<?php
/**
 * Ueditor 编辑器 控制器
 * @Author: Alvin<syxuwen@gmail.com>
 * @Date: 2016-08-24
 * 
 * ueditor和后台通信的功能较多，这里列举一下编辑器和后台通信的方法：
 1.上传图片	uploadimage
 2.拖放图片上传、粘贴板图片上传	 uploadimage
 3.word文档图片转存	uploadimage
 4.截图工具上传	uploadimage
 5.上传涂鸦	uploadscrawl
 6.上传视频	uploadvideo
 7.上传附件	uploadvideo
 8.在线图片、文件管理 listimage listfile
 9.粘贴转存远程图片	catchimage
 */
namespace Home\Controller;

use Think\Upload;

class UeditorController extends CommonController
{
    private $uploadconfig = array();
    private $editorconfig = array();
    
    //初始化
	public function _initialize(){
		
		parent::_initialize();
		
		//编辑器配置
		$this->editorconfig = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(CONF_PATH."ueditorconfig.json")), true);
	
		//初始化上传文件基本配置
		$this->uploadconfig = array(
			'rootPath'  =>  './Upload/', // 设置上传根目录
			"subName" => array('date', 'Y/m/d'),
		);
	}
    
    /**
     * 路由
     */
    public function index(){
    	
    	$act = I('request.action');
    	$this->$act();
    }
    
    /**
     * 配置
     */
    public function config(){
    	
    	$this->ajaxReturn($this->editorconfig);
    }
    
    /**
     * 上传图片
     */
    public function uploadimage(){
    	
    	$this->uploadconfig['savePath'] = 'images/';
    	$this->uploadconfig['maxSize'] = $this->editorconfig['imageMaxSize'];//大小上限
    	$this->uploadconfig['exts'] = $this->formatAllowFiles($this->editorconfig['imageAllowFiles']);
		$this->commonupload();
    }
    
    /**
     * 涂鸦
     */
    public function uploadscrawl(){
    	
        $base64Data = $_POST[$this->editorconfig['scrawlFieldName']];
        $img = base64_decode($base64Data);
        
        $config = array(
            "savePath" => $this->uploadconfig['rootPath'].'scrawl/' . date('Y') . '/' . date('m') . '/'. date('d') . '/', //保存路径
            "maxSize" => $this->editorconfig['scrawlMaxSize'],
        );

        //大小验证
        $uriSize = strlen($img); //得到图片大小
        $allowSize = 1024 * $config['maxSize'];
        if ($uriSize > $allowSize) {
        	$returndata['state'] = "超出大小限制";
        }

        //写入文件
        $tmpName = $config['savePath'] . rand(1, 10000) . time() . uniqid().'.png';
        \Think\Storage::connect();
        if(\Think\Storage::put($tmpName,$img)){
        	$return_data['url'] = __ROOT__ . '/' . $tmpName;
        }else{
        	$returndata['state'] = "文件保存失败";
        }
        
        if($returndata['state']){
        	$this->ajaxReturn($returndata);
        }
        
        $return_data['state'] = 'SUCCESS';
        $return_data['title'] =  "scrawl.jpg";
        $return_data['original'] =  "scrawl.jpg";
        $this->ajaxReturn($return_data);

    }

    /**
     * 视频上传
     */
    public function uploadvideo(){
    	
    	$this->uploadconfig['savePath'] = 'video/';
    	$this->uploadconfig['maxSize'] = $this->editorconfig['videoMaxSize'];//大小上限
    	$this->uploadconfig['exts'] = $this->formatAllowFiles($this->editorconfig['videoAllowFiles']);
    	
    	$this->commonupload();
    }
    
    /**
     * 附件上传
     */
    public function uploadfile(){
    	
    	$this->uploadconfig['savePath'] = 'file/';
    	$this->uploadconfig['maxSize'] = $this->editorconfig['fileMaxSize'];//大小上限
    	$this->uploadconfig['exts'] = $this->formatAllowFiles($this->editorconfig['fileAllowFiles']);
    	
    	$this->commonupload();
    }
    
    /**
     * 文件上传公共方法
     */
    private function commonupload(){
    	
    	$upload = new Upload($this->uploadconfig);
    	$info = $upload->upload();
    	
    	if ($info) {
    		$state = "SUCCESS";
    	} else {
    		$state = "ERROR" . $upload->getError();
    	}
    	
    	$return_data['url'] = __ROOT__.$this->uploadconfig['rootPath'].$info['upfile']['savepath'].$info['upfile']['savename'];
    	$return_data['title'] = $info['upfile']['name'];
    	$return_data['original'] = $info['upfile']['name'];
    	$return_data['state'] = $state;
    	$this->ajaxReturn($return_data);
    }
    
    /**
     * 在线图片管理
     */
    public function listimage(){
    	
    	$allowFiles = $this->editorconfig['imageManagerAllowFiles'];
    	$listSize = $this->editorconfig['imageManagerListSize'];
    	$basepath = __ROOT__.$this->uploadconfig['rootPath'];
    	$path = $basepath.$this->editorconfig['imageManagerListPath'];
        $this->listcommon($allowFiles,$listSize,$path);
    }

    /**
     * 在线文件管理
     */
    public function listfile(){
    	
        $allowFiles = $this->editorconfig['fileManagerAllowFiles'];
        $listSize = $this->editorconfig['fileManagerListSize'];
        $basepath = __ROOT__.$this->uploadconfig['rootPath'];
        $path = $basepath.$this->editorconfig['fileManagerListPath'];
        $this->listcommon($allowFiles,$listSize,$path);
    }
    
    /**
     * 文件list公共方法
     * @param string $allowFiles
     * @param integer $listSize
     * @param string $path
     */
    private function listcommon($allowFiles,$listSize,$path){
    	
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;
        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            $this->ajaxReturn(array(
                    "state" => "no match file",
                    "list" => array(),
                    "start" => $start,
                    "total" => count($files)
            ));
        }
        
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}
        /* 返回数据 */
        $result = array(
                "state" => "SUCCESS",
                "list" => $list,
                "start" => $start,
                "total" => count($files)
        );

        $this->ajaxReturn($result);
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function getfiles($path, $allowFiles, &$files = array()){
    	
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }
    
    
    /**
     * 粘贴转存远程图片
     */
    public function catchimage(){
    	
    	\Think\Storage::connect();
    	$config = array(
    			"pathFormat" => $this->editorconfig['catcherPathFormat'],
    			"maxSize" => $this->editorconfig['catcherMaxSize'],
    			"allowFiles" => $this->editorconfig['catcherAllowFiles'],
    			"oriName" => "remote.png"
    	);
    	$fieldName = $this->editorconfig['catcherFieldName'];
    	

    	$list = array();
    	if (isset($_POST[$fieldName])) {
    		$source = $_POST[$fieldName];
    	} else {
    		$source = $_GET[$fieldName];
    	}
    	
    	foreach ($source as $imgUrl) {
    	
    		$imgUrl = htmlspecialchars($imgUrl);
    		$imgUrl = str_replace("&amp;", "&", $imgUrl);
    	
    		//http开头验证
    		if (strpos($imgUrl, "http") !== 0) {
    			$this->ajaxReturn(array('state'=>'不是http链接'));
    		}
    	
    		$heads = get_headers($imgUrl);
    		//格式验证(扩展名验证和Content-Type验证)
    		$fileType = strtolower(strrchr($imgUrl, '.'));
    		if (!in_array($fileType, $config['allowFiles']) || stristr($heads['Content-Type'], "image")) {
    			$this->ajaxReturn(array("state"=>"错误文件格式"));
    		}
    	
    		//打开输出缓冲区并获取远程图片
    		ob_start();
    		$context = stream_context_create(
    			array('http' => array(
    					'follow_location' => false // don't follow redirects
    			))
    		);
    	
    		readfile($imgUrl, false, $context);
    		$img = ob_get_contents();
    		ob_end_clean();
    	
    		//大小验证
    		if(strlen($img)>$config['maxSize']){
    			$data['states'] = '超出大小限制';
    			$this->ajaxReturn($data);
    		}
    	
    	
    		$imgname = uniqid().'.png';
    		$filename = $this->uploadconfig['rootPath'].'catchimage/'.date('Y/m/d').'/'.$imgname;
    		preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);
    		$oriName = $m ? $m[1]:"";
    		if(\Think\Storage::put($filename,$img)){
    			array_push($list, array(
    					"state" => 'SUCCESS',
    					"url" => __ROOT__.$filename,
    					"size" => strlen($img),
    					"title" => $imgname,
    					"original" => $oriName,
    					"source" => htmlspecialchars($imgUrl)
    			));
    		}else{
    			array_push($list,array('state'=>'文件写入失败'));
    		}
    	}
    	
    	/* 返回抓取数据 */
    	$this->ajaxReturn(array(
    			'state'=> count($list) ? 'SUCCESS':'ERROR',
    			'list'=> $list
    	));
    	
    }
    
    /**
     * ueditor 扩展名配置转换为数组
     * @param string $exts
     * @return string array
     */
    private function formatAllowFiles($exts){
    	foreach ($exts as $v){
    		$data[] = ltrim($v,'.');
    	}
    	return $data;
    }
    

}