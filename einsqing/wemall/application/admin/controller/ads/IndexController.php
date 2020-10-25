<?php
namespace app\admin\controller\ads;
use app\admin\controller\BaseController;

class IndexController extends BaseController
{
	//广告列表
	public function index(){
		$map = array();
		$search = '?';
		if(input('param.position_id') != '' && input('param.position_id') != '-10'){
            $map['position_id']  = input('param.position_id');
            $search .= 'position_id='.input('param.position_id').'&';
        }

		$adslist = model('Ads')->with('file,position')->where($map)->order('rank', 'desc')->paginate();
		$page = str_replace("?",$search,$adslist->render());
        $this->assign("page", $page);

		cookie("prevUrl", request()->url());

		$positionlist = model('AdsPosition')->all();
		$this->assign('positionlist', $positionlist);
		$this->assign('adslist', $adslist);
		$this->assign('condition', input('param.'));
		return view();
	}

	//新增修改广告
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			$data['status'] = input('?post.status') ? $data['status'] : 0;
			
			if(input('post.id')){
				$result = model('Ads')->update($data);
			}else{
				$result = model('Ads')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$ads = model('Ads')->with('file')->find($id);
				$this->assign('ads', $ads);
			}

			$positionlist = model('AdsPosition')->all();
			$this->assign('positionlist', $positionlist);
			return view();
		}
	}

	//改变状态
	public function update(){
		$data = input('param.');
		$result = model('Ads')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

	//删除广告
	public function del(){
		$ids = input('param.id');
		
		$result = model('Ads')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}

}