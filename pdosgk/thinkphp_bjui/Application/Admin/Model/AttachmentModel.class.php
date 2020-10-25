<?php 
/*
 * 附件模型
 */
namespace Admin\Model;
use Think\Model;
class AttachmentModel extends Model {
	public $imageexts = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
	/* protected $_map = array(
			'name' =>'filename',
			'size'  =>'filesize',
			'ext'  =>'fileext',
	);
	public function add_content($data){
		var_dump($data);exit;
	} */
	public function __construct(){
		parent::__construct();
		//$this->module = $module ? $module : 'content';
	}
	
	//下载内容中的图片, 并返回下载后的内容
	public function download($field, $value,$watermark = '0',$ext = 'gif|jpg|jpeg|bmp|png', $absurl = '', $basehref = ''){
		$dir = date('Y-m-d/');
		$upload_url = '/Uploads/';
		$uploadpath = $upload_url.$dir;
		
		$uploaddir = './Uploads/'.$dir;
		
		$string = stripslashes($value);
		//判断是否需要下载
		if(!preg_match_all("/(href|src)=([\"|']?)([^ \"'>]+\.($ext))\\2/i", $string, $matches)) return $value;
		//取出下载的图片
		$remotefileurls = array();
		foreach($matches[3] as $matche)
		{
			//如果是本地图片, 则跳过
			if(strpos($matche, '://') === false) continue;
			$remotefileurls[] = $matche;
		}
		unset($matches, $string);
		$remotefileurls = array_unique($remotefileurls);
		$oldpath = $newpath = array();
		//开始下载
		//import("Org.Net.Http");
		foreach($remotefileurls as $k=>$file) {
			//判断是否是本地图片
			if(strpos($file, '://') === false || strpos($file, $upload_url) !== false) continue;
			
			//获取文件扩展名
			$filename = fileext($file);
			//$file_name = basename($file);
			$filename = $this->getname($filename);
			$newfile = $uploaddir.$filename;
			\Org\Net\Http::curlDownload($file, $newfile);

			//var_dump($newfile);exit;
		}
		//替换下载后的地址
		
	}
	
	function getname($fileext){
		return date('Ymdhis').rand(100, 999).'.'.$fileext;
	}
	
	public function saveData($uploadedfile, $module = 'image'){
		$uploadedfile['module'] = $module;
		$uploadedfile['userid'] = session('userid');
		$uploadedfile['uploadtime'] = NOW_TIME;
		$uploadedfile['uploadip'] = get_client_ip(0, true);
		//文件名如果太长, 则随机生成一个
		$uploadedfile['filename'] = strlen($uploadedfile['filename'])>49 ? $this->getname($uploadedfile['fileext']) : $uploadedfile['filename'];
		$uploadedfile['isimage'] = in_array($uploadedfile['fileext'], $this->imageexts) ? 1 : 0;
		
		if($this->create($uploadedfile)){
			return $this->add($uploadedfile);
		}
		
	}
}
