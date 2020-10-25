<?php
namespace app\admin\controller\ads;
use app\admin\controller\BaseController;

class PositionController extends BaseController
{
	//广告位置
	public function index(){
		$positionlist = model('AdsPosition')->all();
		
		cookie("prevUrl", request()->url());

		$this->assign('positionlist', $positionlist);
		return view();
	}

    //新增修改广告位置
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			$data['status'] = input('?post.status') ? $data['status'] : 0;
			
			if(input('post.id')){
				$result = model('AdsPosition')->update($data);
			}else{
				$result = model('AdsPosition')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$position = model('AdsPosition')->find($id);
				$this->assign('position', $position);
			}
			return view();
		}
	}

	//开启关闭广告位置
	public function update(){
		$data = input('param.');
		
		model('Ads')->where('position_id',$data['id'])->update(['status' => $data['status']]);
		$result = model('AdsPosition')->where('id','in',$data['id'])->update(['status' => $data['status']]);

		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}


}
