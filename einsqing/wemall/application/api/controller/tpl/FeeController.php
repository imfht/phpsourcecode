<?php
namespace app\api\controller\tpl;
use app\api\controller\BaseController;

class FeeController extends BaseController
{

    // 获取费用模版
    public function index()
    {
        $feeList = model('FeeTpl')->where('status',1)->order('id', 'desc')->select();

        $data['feeList'] = $feeList;
        return json(['data' => $data, 'msg' => '费用模版', 'code' => 1]);
    }


}











