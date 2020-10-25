<?php
namespace app\admin\controller\location;
use app\admin\controller\BaseController;

class DistrictController extends BaseController
{
	//区域列表
	public function index(){
		
		$districtlist = model('Location')->with('city')->where('type',3)->paginate();
		// halt($districtlist->toArray());
		cookie("prevUrl", $this->request->url());

		$this->assign('districtlist', $districtlist);
		return view();
	}

	//新增修改区域
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			$data['type'] = 3;
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
				$district = model('Location')->find($id);
				$this->assign('district', $district);
			}
			$citylist = model('Location')->where('type',2)->select();
			$this->assign('citylist', $citylist);
			return view();
		}
	}
	//删除区域
	public function del(){
		$ids = input('param.id');
		$result = model('Location')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}
	//改变区域状态
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