<?php
namespace app\api\controller\shop;
use app\api\controller\BaseController;

class FeedbackController extends BaseController
{
    //反馈
    public function index(){
    	$user_id = $this->get_user_id();
    	if (request()->isPost()){
    		$value = input('param.value');
    		$result = model('Feedback')->create([
                'user_id' => $user_id,
                'value' => $value,
            ]);

            if($result){
                return json(['data' => $result, 'msg' => '提交成功', 'code' => 1]);
            }else{
                return json(['data' => $result, 'msg' => '提交失败', 'code' => 0]);
            }
    	}else{
    		$backList = model('Feedback')->with('user')->order('id desc')->paginate()->toArray();
    		
    		$data['backList'] = $backList['data'];
			return json(['data' => $data, 'msg' => '反馈', 'code' => 1]);
    	}
    }

}