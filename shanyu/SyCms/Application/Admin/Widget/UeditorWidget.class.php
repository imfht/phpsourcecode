<?php
namespace Admin\Widget;
use Think\Controller;

class UeditorWidget extends Controller {
    public function editor($name='',$value=''){
    	$this->assign('ueditor_name',$name);
		$this->assign('ueditor_value',$value);
    	$this->display(MODULE_PATH.'Widget/Tpl/Ueditor/editor.html');
    }

	public function server(){
		//检测访问
		if(empty($_GET['action'])) return false;

		//准备配置
		$config=$this->config;
		$action = $_GET['action'];
		$param=array(
			'water'=>intval($_GET['water']),
		);
		
		//事件处理
		switch ($action) {
		    case 'config':
		        $result =  json_encode($config);
		        break;
		    /* 上传图片 */
		    case 'uploadimage':
		    /* 上传涂鸦 */
		    case 'uploadscrawl':
		    /* 上传视频 */
		    case 'uploadvideo':
		    /* 上传文件 */
		    case 'uploadfile':

		    	if($file=D('Common/Attachment')->uploadOne($param)){
		    		$file_data=array(
		    			"state" => "SUCCESS",
		    			"url" => $file['url'],
		    			"title" => $file['title'],
		    			"original" => $file['title'],
		    			"type" => $file['ext'],
		    			"size" => $file['size'],
		    		);
		    		$result=json_encode($file_data);
		    	}
		        break;

		    /* 列出图片 */
		    case 'listimage':
		    	$config['imageManagerListPath'] = __ROOT__.'/Uploads/';
		        $result = include("Public/Js/Ueditor/php/action_list.php");
		        break;
		    /* 列出文件 */
		    case 'listfile':
		    	$config['fileManagerListPath'] = __ROOT__.'/Uploads/file/';
		        $result = include("Public/Js/Ueditor/php/action_list.php");
		        break;

		    /* 抓取远程文件 */
		    case 'catchimage':
		    	$config['fileManagerListPath']=__ROOT__.'/Uploads/catcher/{yyyy}{mm}{dd}/{time}{rand:6}';
		        $result = include("Public/Js/Ueditor/php/action_crawler.php");
		        break;

		    default:
		        $result = json_encode(array(
		            'state'=> '请求地址出错'
		        ));
		        break;
		}

		/* 输出结果 */
		if (isset($_GET["callback"])) {
		    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
		        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
		    } else {
		        echo json_encode(array(
		            'state'=> 'callback参数不合法'
		        ));
		    }
		} else {
		    echo $result;
		}
	}

	protected $config = array
	(
	    'imageActionName' => 'uploadimage',
	    'imageFieldName' => 'upfile',
	    'imageMaxSize' => 2048000,
	    'imageAllowFiles' => array('.png', '.jpg', '.jpeg', '.gif', '.bmp'),
	    'imageCompressEnable' => true,
	    'imageCompressBorder' => 1600,
	    'imageInsertAlign' => 'none',
	    'imageUrlPrefix' => '',
	    'imagePathFormat' => 'Upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',

	    'scrawlActionName' => 'uploadscrawl',
	    'scrawlFieldName' => 'upfile',
	    'scrawlPathFormat' => 'Upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',
	    'scrawlMaxSize' => 2048000,
	    'scrawlUrlPrefix' => '',
	    'scrawlInsertAlign' => 'none',

	    'snapscreenActionName' => 'uploadimage',
	    'snapscreenPathFormat' => 'Upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',
	    'snapscreenUrlPrefix' => '',
	    'snapscreenInsertAlign' => 'none',

	    'catcherLocalDomain' => array('127.0.0.1', 'localhost', 'img.baidu.com'),
	    'catcherActionName' => 'catchimage',
	    'catcherFieldName' => 'source',
	    'catcherPathFormat' => 'Upload/catcher/{yyyy}{mm}{dd}/{time}{rand:6}',
	    'catcherUrlPrefix' => '',
	    'catcherMaxSize' => 2048000,
	    'catcherAllowFiles' => array('.png', '.jpg', '.jpeg', '.gif', '.bmp'),

	    'videoActionName' => 'uploadvideo',
	    'videoFieldName' => 'upfile',
	    'videoPathFormat' => 'Upload/video/{yyyy}{mm}{dd}/{time}{rand:6}',
	    'videoUrlPrefix' => '',
	    'videoMaxSize' => 102400000,
	    'videoAllowFiles' => array('.flv', '.swf', '.mkv', '.avi', '.rm', '.rmvb', '.mpeg', '.mpg', '.ogg', '.ogv', '.mov', '.wmv', '.mp4', '.webm', '.mp3', '.wav', '.mid'),

	    'fileActionName' => 'uploadfile',
	    'fileFieldName' => 'upfile',
	    'filePathFormat' => 'Upload/file/{yyyy}{mm}{dd}/{time}{rand:6}',
	    'fileUrlPrefix' => '',
	    'fileMaxSize' => 51200000,
	    'fileAllowFiles' => array('.png','.jpg','.jpeg','.gif','.bmp','.flv','.swf','.mkv','.avi','.rm','.rmvb','.mpeg','.mpg','.ogg','.ogv','.mov','.wmv','.mp4','.webm','.mp3','.wav','.mid','.rar','.zip','.tar','.gz','.7z','.bz2','.cab','.iso','.doc','.docx','.xls','.xlsx','.ppt','.pptx','.pdf','.txt','.md','.xml'),

	    'imageManagerActionName' => 'listimage',
	    'imageManagerListPath' => 'Upload/image/',
	    'imageManagerListSize' => 20,
	    'imageManagerUrlPrefix' => '',
	    'imageManagerInsertAlign' => 'none',
	    'imageManagerAllowFiles' => array('.png', '.jpg', '.jpeg', '.gif', '.bmp'),

	    'fileManagerActionName' => 'listfile',
	    'fileManagerListPath' => 'Upload/file/',
	    'fileManagerUrlPrefix' => '',
	    'fileManagerListSize' => 20,
	    'fileManagerAllowFiles' => array('.png','.jpg','.jpeg','.gif','.bmp','.flv','.swf','.mkv','.avi','.rm','.rmvb','.mpeg','.mpg','.ogg','.ogv','.mov','.wmv','.mp4','.webm','.mp3','.wav','.mid','.rar','.zip','.tar','.gz','.7z','.bz2','.cab','.iso','.doc','.docx','.xls','.xlsx','.ppt','.pptx','.pdf','.txt','.md','.xml'),
	);


}