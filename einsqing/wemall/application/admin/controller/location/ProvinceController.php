<?php
namespace app\admin\controller\location;
use app\admin\controller\BaseController;

class ProvinceController extends BaseController
{
	//省份列表
	public function index(){
		
		$provincelist = model('Location')->with('country')->where('type',1)->paginate();
		// halt($provincelist->toArray());
		cookie("prevUrl", $this->request->url());

		$this->assign('provincelist', $provincelist);
		return view();
	}

	//新增修改省份
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			$data['type'] = 1;
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
				$province = model('Location')->find($id);
				$this->assign('province', $province);
			}
			$countrylist = model('Location')->where('type',0)->select();
			$this->assign('countrylist', $countrylist);
			return view();
		}
	}
	//删除省份
	public function del(){
		$ids = input('param.id');
		$result = model('Location')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}
	//改变省份状态
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