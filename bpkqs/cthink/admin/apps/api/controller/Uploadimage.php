<?php
namespace app\api\controller;
use think\Controller;

/**
 * 上传图片接口
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class UploadImage extends controller{	
	
	/**
	 * 自定义文件路径
	 */
	protected $timepath = '';
	
	/**
	 * 图片保存路径
	 */
	protected $savepath = '';
	
	/**
	 * 保存文件的名称
	 */
	protected $savename = '';
	
	/**
	 * 文件对象
	 */
	protected $file = null;
	
	/**
	 * 验证上传图片的信息
	 */
	protected $validate = [];
	
	/**
	 * 文件信息
	 */
	protected $imginfo = [];
	
	/**
	 * 得到文件的扩展名
	 */
	protected $ext = '';
	
	/**
	 * 得到文件的尺寸
	 */
	protected $imagesize = [];
	
	/**
	 * 针对文件进行md5
	 */
	protected $md5file = '';
	
	/**
	 * 对文件hash
	 */
	protected $hashfile = '';
	
	/**
	 * 初始化,配置和设置上传信息
	 */
	protected function _initialize(){
		$this->timepath = date('Y/m-d',time());
		
		$this->savepath = ROOT_PATH . 'public' . DS . 'uploads/'.$this->timepath;
		
		$this->file = request()->file('Filedata');
		
		$this->imginfo = $this->file->getInfo();
		
		$name = explode('.',$this->imginfo['name']);
		
		$this->ext = $name[count($name) -1];
		
		$this->savename = md5(time().rand_string(10)).'.'.$this->ext;
		
		$this->imagesize = getimagesize($this->imginfo['tmp_name']);
		
		$this->md5file = md5_file($this->imginfo['tmp_name']);
		
		$this->hashfile = hash_file('sha1',$this->imginfo['tmp_name']);
		
		$this->validate = ['size'=>409600,'ext'=>'jpg,jpeg,png,gif'];
	}
	
	/**
	 * 上传图片接口
	 */
	public function upload(){
		$attach = model('Attach');
		$result = $attach->getAttachinfo($this->md5file);
		if($result){
			return json_encode(['code'=>1,'msg'=>$result]);
		}else{
			$info = $this->file->validate($this->validate)->move($this->savepath,$this->savename);
			if($info){
				list($width,$height) = $this->imagesize;
				$data = [
					'app_name'	=> 'public',
					'ctime'		=> time(),
					'name'		=> $this->imginfo['name'],
					'type'		=> $this->imginfo['type'],
					'size'		=> $this->imginfo['size'],
					'extension'	=> $this->ext,
					'md5'		=> $this->md5file,
					'hash'		=> $this->hashfile,
					'save_path'	=> $this->timepath,
					'save_name'	=> $this->savename,
					'width'		=> $width,
					'height'	=> $height,
					'mime'		=> $this->imagesize['mime'],
				];
				$getid = $attach->add($data);
				$data['attach_id'] = $getid;
				return json_encode(['code'=>1,'msg'=>$data]);
			}else{
				return json_encode(['code'=>0,'msg'=>$this->file->getError()]);
			}
		}
	}
	
	/**
	 * uenter编辑器上传图片
	 */
	public function editor(){
		$attach = model('Attach');
		$result = $attach->getAttachinfo($this->md5file);
		if($result){
			return json_encode(['state'=>'SUCCESS','url'=>config('url_domain').'/public/uploads/'.$result['save_path'].'/'.$result['save_name'],'title'=>'','original'=>'']);
		}else{
			$info = $this->file->validate($this->validate)->move($this->savepath,$this->savename);
			if($info){
				list($width,$height) = $this->imagesize;
				$data = [
					'app_name'	=> 'public',
					'ctime'		=> time(),
					'name'		=> $this->imginfo['name'],
					'type'		=> $this->imginfo['type'],
					'size'		=> $this->imginfo['size'],
					'extension'	=> $this->ext,
					'md5'		=> $this->md5file,
					'hash'		=> $this->hashfile,
					'save_path'	=> $this->timepath,
					'save_name'	=> $this->savename,
					'width'		=> $width,
					'height'	=> $height,
					'mime'		=> $this->imagesize['mime'],
				];
				$getid = $attach->add($data);
				$data['attach_id'] = $getid;
				return json_encode(['state'=>'SUCCESS','url'=>config('url_domain').'/public/uploads/'.$data['save_path'].'/'.$data['save_name'],'title'=>'','original'=>'']);
			}else{
				return json_encode(['state'=>'error','title'=>$this->file->getError()]);
			}
		}
	}
	
}
