<?php
namespace app\admin\controller\location;
use app\admin\controller\BaseController;

class CityController extends BaseController
{
	//城市列表
	public function index(){
		
		$citylist = model('Location')->with('province')->where('type',2)->paginate();
		// halt($citylist->toArray());
		cookie("prevUrl", $this->request->url());

		$this->assign('citylist', $citylist);
		return view();
	}

	//新增修改城市
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			$data['type'] = 2;
			if($data['id']){
				$result = model('Location')->update($data);
			}else{
				$result = model('Location')->create($data);
			}
			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$city = model('Location')->find($id);
				$this->assign('city', $city);
			}
			$provincelist = model('Location')->where('type',1)->select();
			$this->assign('provincelist', $provincelist);
			return view();
		}
	}
	//删除城市
	public function del(){
		$ids = input('param.id');
		$result = model('Location')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}
	//改变城市状态
	public function update(){
		$data = input('param.');
		$result = model('Location')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}






}