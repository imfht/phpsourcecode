<?php
namespace app\admin\controller\config;
use app\admin\controller\BaseController;

class ShopController extends BaseController
{
	//商城设置
	public function index(){
		if (request()->isPost()){
			$data = input('post.');
			$data['debug'] = input('?post.debug') ? $data['debug'] : 0;
			$data['status'] = input('?post.status') ? $data['status'] : 0;
			$data['shop_update'] = input('?post.shop_update') ? $data['shop_update'] : 0;
			$result = model('Config')->update($data);
			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$config = model('Config')->with('logo')->find();
			cookie("prevUrl", "/admin/config/shop/index");
			// halt($config->toArray());
			$this->assign('config', $config);
			return view();
		}
	}
}