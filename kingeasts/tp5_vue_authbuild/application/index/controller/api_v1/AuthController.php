<?php
// +----------------------------------------------------------------------
// | spb2
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace app\index\controller\api_v1;


use app\common\controller\Resful;

class AuthController extends Resful
{
    public function login()
    {
        $form = $this->request->post();
        return $this->success(['data'=>$form]);

    }

}