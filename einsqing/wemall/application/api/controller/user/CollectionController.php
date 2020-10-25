<?php
namespace app\api\controller\user;
use app\api\controller\BaseController;

class CollectionController extends BaseController
{
	// 获取收藏列表
    public function index()
    {
        $user_id = $this->get_user_id();

        $collectionlist = model('UserCollection')->with('user,product.file')->where('user_id',$user_id)->paginate(1)->toArray();
        $data['collection'] = $collectionlist['data'];
        return json(['data' => $data, 'msg' => '收藏列表', 'code' => 1]);
    }

    // 添加收藏
    public function add()
    {
    	$user_id = $this->get_user_id();
    	$product_id = input('param.id');

    	$result = model('UserCollection')->create([
                'user_id'  =>  $user_id,
                'product_id' =>  $product_id,
                'status'  =>  1
            ]);
    	if($result){
    		$data['collection'] = $result;
    		return json(['data' => $data, 'msg' => '收藏成功', 'code' => 1]);
		}else{
			return json(['data' => false, 'msg' => '收藏失败', 'code' => 0]);
		}
    }

	//取消收藏
    public function cancel()
    {
        $id = input('param.id');
        $user_id = $this->get_user_id();

        $map = array();
        $map['id'] = $id;
        $map['user_id'] = $user_id;
        
        $result = model('UserCollection')->where($map)->update(['status' => 0]);
        if($result){
            return json(['data' => false, 'msg' => '取消成功', 'code' => 1]);
        }else{
            return json(['data' => false, 'msg' => '取消失败', 'code' => 0]);
        }
    }


}