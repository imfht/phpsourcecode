<?php
namespace app\api\controller;

require_once env('VENDOR_PATH') . 'qiniu_php_sdk/autoload.php';

use app\common\model\ApiApp as ApiApps;
use app\common\model\ApiList as ApiLists;
use think\Image;
use expand\Str;
use expand\ApiReturn;

class Uploads extends Base {

    public $file_move_path;   //上传文件移动服务器位置
    public $file_back_path;   //上传文件返回文件地址
    public $up_type;   //上传类型
    public $root_path;   //根目录路径

    public function initialize(){
        parent::initialize();
		$this->up_type = 'image';   //上传文件类型
        $up = cache('DB_UP_CONFIG');
        if(!$up){
            $up = db('config')->where('type', 'eq', 'up')->select();
            $up_config = [];
            foreach ($up as $k => $v){
                $up_config[$v['k']] = $v['v'];
            }
            cache('DB_UP_CONFIG', $up_config);
            $up = cache('DB_UP_CONFIG');
        }
        $this->file_move_path = WEB_PATH.'/'.$up['upload_path'].'/'.$this->up_type;	//上传文件移动服务器位置
        $this->file_back_path = '/'.$up['upload_path'].'/'.$this->up_type;	//上传文件返回文件地址

        $this->root_path = WEB_PATH.'/'.$up['upload_path'].'/';	//根目录路径
    }

    public function index($hash) {
    	$apiInfo = cache('apiInfo_'.$hash);	//接口信息
		$data = cache('input_'.$hash);	//请求字段
		$header = request()->header();
		$up_config = cache('DB_UP_CONFIG');	//上传配置
		$auth = new \Qiniu\Auth($up_config['qiniu_AccessKey'], $up_config['qiniu_SecretKey']);
		$returnBody = '{"name": $(fname), "size": $(fsize), "w": $(imageInfo.width), "h": $(imageInfo.height), "hash": $(etag)}';
		$policy = array(
		    'returnBody' => $returnBody,
		    'mimeLimit'	=> 'image/*'
		);
		$expires = 60;	//Token有效期
		//生成 Token
		$token = $auth->uploadToken($up_config['qiniu_bucket'],null, $expires, $policy, true);
		// 构建 UploadManager 对象
		$uploadMgr = new \Qiniu\Storage\UploadManager();
        $file = request()->file('file');
        if( isset($data['image_data']) && !empty($data['image_data']) ){
			$imgurl = $this->cutimage($file,$data);
        }else{
			$imgurl = $this->uploads($file);
        }
		if( !strchr($imgurl,'uploads') ){
			return ApiReturn::r(0,null,$imgurl);
		}
		// 要上传文件的本地路径
		$filePath = WEB_PATH.$imgurl;
		// 上传到七牛后保存的文件名
		$new_imgurl = ltrim($imgurl,'/');
		$key = $new_imgurl;
//		调用 UploadManager 的 putFile 方法进行文件的上传。
		list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

		if ($err !== null) {
			$info['imgurl'] = request()->domain().$imgurl;	//上传到七牛不成功，返回服务器的地址
		} else {
			unlink(WEB_PATH.$imgurl);	//删除服务端的图片
		    $info['imgurl'] = $up_config['qiniu_yuming'].'/'.$ret['name'];
		}
		return ApiReturn::r(1,$info);
    }

    public function uploads($file)
    {
		$up_config = cache('DB_UP_CONFIG');   //获取数据库中的上传文件配置信息缓存
//      $file = request()->file('file');
        if ($file){
            $info = $file->validate(['size'=>$up_config['image_size'], 'ext' => $up_config[$this->up_type.'_format'] ])
            ->move($this->file_move_path);
            if($info){
                if ( $this->up_type == 'image' && $up_config['isprint'] == 1){   //上传图片，加水印
                    $file = Image::open( $this->file_move_path.'/'.$info->getSaveName() );   //打开上传的图片
                    //水印图片、水印位置、水印透明度 -> 保存同名图片覆盖
                    $file->water(WEB_PATH.$up_config['image_print'], $up_config['print_position'], $up_config['print_blur'])
                    ->save( $this->file_move_path.'/'.$info->getSaveName() );
                }
                $file_path = $this->file_back_path.'/'.$info->getSaveName();
                $file_path = $up_config['image_url'].str_replace('\\', '/', $file_path);
                return $file_path;
            }else{
                return $this->_alert($file->getError());
            }
        }else{
            return $this->_alert('请选择文件');
        }
    }

    public function _alert($msg)
    {
        header('Content-type: text/html; charset=UTF-8');
        return $msg;
        exit;
    }

    /**
     * @Title: cropper
     * @Description: todo(上传图片并裁剪[  ])
     * @author 戏中有你
     * @date 2018年1月17日
     * @throws
     */
    public function cutimage($file,$data) {
        if ($file){
            $image_data = json_decode(htmlspecialchars_decode($data['image_data']), true);
            $start_x = $image_data['x'];       //起始x
            $start_y = $image_data['y'];       //起始y
            $end_x   = $image_data['width'];   //结束x
            $end_y   = $image_data['height'];  //结束y
            $rotate  = $image_data['rotate'];  //旋转角度[有正负]
            $image = Image::open( $file );   //打开上传的图片
            $houzhui_arr = explode('.', $_FILES["file"]["name"]);
            $extension = end($houzhui_arr); //后缀
            $name = '/'.date('Ymd', time()).'/'.date('YmdHis', time())."_".rand(100000, 999999).".".$extension;
            $path = $this->file_move_path.$name;
            $back = str_replace('\\', '/', $this->file_back_path.$name);
            //生成目录
            $mulu = $this->file_move_path.'/'.date('Ymd', time());
			$mulus = $this->file_move_path;
            if ( !file_exists($mulu) ){
            	if( !file_exists($mulus) ){
            		mkdir($mulus);
            	}
                mkdir($mulu);
            }
            $image->rotate($rotate)->crop($end_x, $end_y, $start_x, $start_y, $data['width'], $data['height'])->save($path, null, 80); //图片质量:80
            if (file_exists($path)){    //检测图片是否保存成功
                return $back;
            }
        }
    }

    public function delimg($hash){	//删除图片
		$data = cache('input_'.$hash);	//请求字段
		delimg($data['imgurl']);
		return ApiReturn::r(1);
    }






}
