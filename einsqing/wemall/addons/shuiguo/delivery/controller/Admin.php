<?php
namespace addons\shuiguo\delivery\controller;

class Admin extends Base
{
	//配置页面
    public function index()
    {
    	if (request()->isPost()){
    		$data = input('post.');

    		if(input('post.id')){
				$result = x_model('AddonDeliveryConfig')->update($data);
			}else{
				$result = x_model('AddonDeliveryConfig')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
    	}else{
    		$config = x_model('AddonDeliveryConfig')->find();

            cookie("prevUrl", request()->url());
            
	    	$this->assign('config', $config);
	    	return view('admin_index');
    	}
    }
}
