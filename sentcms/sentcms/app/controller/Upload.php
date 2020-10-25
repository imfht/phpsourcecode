<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller;

use think\facade\Session;
use think\facade\Filesystem;
use app\model\Attach;

class Upload extends Base {

	// 使用内置PHP模板引擎渲染模板输出
	protected $tpl_config = [
		'view_dir_name' => 'view',
		'tpl_replace_string' => [
			'__static__' => '/static',
			'__img__' => '/static/admin/images',
			'__css__' => '/static/admin/css',
			'__js__' => '/static/admin/js',
			'__plugins__' => '/static/plugins',
			'__public__' => '/static/admin',
		],
	];

	public $data = ['data' => [], 'code' => 0, 'msg' => ''];

	protected function initialize() {
	}

	public function index(){
		$param = $this->request->get();

		if (!isset($param['name'])) {
			return $this->error('非法操作');
		}
		$this->data = [
			'from' => $this->request->param('from'),
			'param' => $param,
			'require' => [
				'jsname'     => 'upload',
				'actionname' => 'index'
			]
		];
		return $this->fetch();
	}

	public function server(){
		$param = $this->request->get();
		$map = [];
		if (!isset($param['name'])) {
			return $this->error('非法操作');
		}
		$pageConfig = [
			'list_rows' => $this->request->param('list_rows', 20),
			'page' => $this->request->param('page', 1),
			'query' => $this->request->param()
		];
		if($param['type'] == 'file'){
			$map[] = ['type', '<>', 'image'];
		}else{
			$map[] = ['type', '=', 'image'];
		}
		$list = Attach::where($map)->paginate($pageConfig);

		$this->data = [
			'from' => $this->request->param('from'),
			'param' => $param,
			'list'  => $list,
			'page'  => $list->render(),
			'require' => [
				'jsname'     => 'upload',
				'actionname' => 'server'
			]
		];
		return $this->fetch();
	}

	public function upload(){
		$type = $this->request->param('type');
		$upload_type = (false !== strpos($type, "image")) ? "image" : 'file';
		$config      = $this->$upload_type();
		// 获取表单上传文件 例如上传了001.jpg
		$file =  $this->request->file('file');
		try {
			validate(['file'=>'filesize:10240|fileExt:jpg|image:200,200,jpg'])
				->check([$file]);
			$data['code'] = 1;
			$data['info']   = $this->save($this->request, $upload_type);
		} catch (think\exception\ValidateException $e) {
			$data['code'] = 0;
			$data['info']   = $e->getMessage();
		}
		return json($data);
	}

	protected function image(){
		return [];
	}

	protected function file(){
		return [];
	}

	public function editor(){
		$fileType = $this->request->get('fileType', 'image', 'trim');
		$file = request()->file('imgFile');
		$data['data']['url'] = '/uploads/' . Filesystem::disk('public')->putFile($fileType, $file, 'md5');
		$data['code'] = "000";
		return json($data);
	}

	public function filemanage(){
		$pageConfig = [
			'list_rows' => $this->request->param('list_rows', 20),
			'page' => $this->request->param('page', 1),
			'query' => $this->request->param()
		];
		$map[] = ['type', '=', 'image'];
		$data = Attach::where($map)->paginate($pageConfig)->each(function($item, $key){
			$item['thumbURL'] = $item['url'];
			$item['oriURL'] = $item['url'];
			return $item;
		})->toArray();

		$data['code'] = "000";
		return $data;
	}

	public function ueditor(){
		$data = new \com\Ueditor(Session::get('userInfo.uid'));
		echo $data->output();
	}

	public function delete(){
		$id = $this->request->param('id', 0);
		if(!$id){
			$data = [
				'status' => false
			];
		}else{
			$data = [
				'status' => true
			];
		}
		return json($data);
	}

	protected function save($request, $upload_type){
		$data = [];
		$file= $request->file('file');
		$data['type']        = $upload_type;
		$data['mime']        = $request->param('type');
		$data['size']        = $file->getSize(); //文件大小，单位字节
		$data['md5']         = md5_file($file->getPathname());
		$data['sha1']        = sha1_file($file->getPathname());
		$data['savepath']    = str_replace("\\", "/", Filesystem::disk('public')->putFile($upload_type, $file, 'md5'));
		$data['ext']         = pathinfo($data['savepath'], PATHINFO_EXTENSION); //文件扩展名
		$data['location']    = "/uploads/";
		$data['url'] = $data['location'] . $data['savepath'];
		$data['real_url'] = $request->domain() . $data['url'];
		$data['create_time'] = time();
		$data['savename']    = $request->param('name', $data['savepath']);
		$data['name']        = $request->param('name', $data['savepath']);
		$attach = Attach::create($data);
		$data['id'] = $attach->id;
		return $data;
	}
}