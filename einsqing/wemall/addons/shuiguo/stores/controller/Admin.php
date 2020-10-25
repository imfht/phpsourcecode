<?php
namespace addons\shuiguo\stores\controller;

class Admin extends Base
{
	//门店列表
    public function index()
    {
		$storesList = x_model('AddonStores')->order('id desc')->paginate();

		cookie("prevUrl", request()->url());

    	$this->assign('storesList', $storesList);
    	return view('admin_index');
    }

     //新增修改门店
    public function add()
    {
    	if (request()->isPost()){
    		$data = input('post.');

    		if(input('post.id')){
				$result = x_model('AddonStores')->update($data);
			}else{
				$result = x_model('AddonStores')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
    	}else{
    		$id = input('param.id');
			if($id){
				$stores = x_model('AddonStores')->find($id);
				$this->assign('stores', $stores);
			}
	    	return view('admin_add');
    	}
    }

    //更新门店状态
    public function update()
    {
    	$data = input('param.');

		$result = x_model('AddonStores')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
    }



}
