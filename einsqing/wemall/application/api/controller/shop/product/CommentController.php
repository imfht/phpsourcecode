<?php
namespace app\api\controller\shop\product;
use app\api\controller\BaseController;

class CommentController extends BaseController
{
	//获取商品评论列表
	public function index(){
		$product_id = input('param.id');

        if(input('param.page')){
        	$data['comment'] = model('ProductComment')->with('user.avater,product')->where('product_id',$product_id)->order('id', 'desc')->paginate();
        }else{
        	$data['comment'] = model('ProductComment')->with('user.avater,product')->where('product_id',$product_id)->order('id', 'desc')->select();
        }
		return json(['data' => $data, 'msg' => '商品评论列表', 'code' => 1]);
	}

    // 新增评论
    public function add(){
        $user_id = $this->get_user_id();
        $data = input('param.');
        $star = input('?param.star') ? $data['star'] : 0;
        
        $orderDetail = model('OrderDetail')->where('order_id',$data['id'])->select();

        $addAll = array();
        foreach ($orderDetail as $key => $value) {
            $data1 = array();
            $data1 ["product_id"] = $value["product_id"];
            $data1 ["user_id"] = $user_id;
            $data1 ["name"] = $data['name'];
            $data1 ["star"] = $star;
            array_push($addAll, $data1);
        }
        $result = model('ProductComment')->saveAll($addAll);
        if($result){
            return json(['data' => false, 'msg' => '评论成功', 'code' => 1]);
        }else{
            return json(['data' => false, 'msg' => '评论失败', 'code' => 0]);
        }   
    }

}