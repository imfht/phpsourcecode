<?php
namespace app\admin\controller\wx;
use app\admin\controller\BaseController;

class RobotController extends BaseController
{

	//图灵机器人
	public function index(){
		if(request()->ispost()){
			$data = input('post.');
			intval($data['id'])?model('robot')->update($data):model('robot')->save($data);
			$this->success("保存成功", cookie("prevUrl"));
		}
		cookie("prevUrl", request()->url());
		$robot = model('robot')->find();
        $this->assign('robot', $robot);
		return view();
	}
}