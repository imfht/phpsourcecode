<?php
namespace app\admin\controller\shop;
use app\admin\controller\BaseController;

class FeedbackController extends BaseController
{
    //反馈列表
    public function index(){
        $backList = model('Feedback')->with('user')->order('id desc')->paginate();

		cookie("prevUrl", request()->url());

		$this->assign('backList', $backList);
        return view();
    }
}