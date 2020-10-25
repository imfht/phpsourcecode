<?php
namespace app\admin\controller\shop\product;
use app\admin\controller\BaseController;

class BookingController extends BaseController
{
	//缺货列表
	public function index(){
		$bookinglist = model('Product')->with('file,skus')->order('rank', 'desc')->paginate();

		cookie("prevUrl", request()->url());
		
		$this->assign('bookinglist', $bookinglist);
		return view();
	}

}