<?php
namespace App\Http\Controllers\Main;

use App\Http\Controllers\Main\Base\BaseController;
use App\Common\Visitor;
use App\Common\Code;
use App\Common\Msg;
use App\Http\Auth\Auth;
use App\Models\PublicToken;
use App\Service\AuthService;

/**
 * 用户个人
 * @author Chengcheng
 * @date 2016年10月21日 17:04:44
 */
class SelfController extends BaseController
{


    /**
     * 设置不需要权限的Action
     * @author Chengcheng
     * @date   2016年10月23日 20:39:25
     * @return array
     */
    protected function noAuth()
    {
        return ['loginByQrToken'];
    }

    /**
     * 获取用户信息
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function info()
    {
        //更新用户信息（读取数据库）
        Auth::auth()->updateLoginInfo();
        // 返回结果
        $result["code"]         = Code::SYSTEM_OK;
        $result["msg"]          = Msg::SYSTEM_OK;
        $result["data"]         = Auth::auth()->getLoginInfo();
        $result["data"]['enum'] = json_encode(config('enum'), JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        return $this->ajaxReturn($result);
    }

    /**
     * 重置密码 - 通过原来的密码
     * @author Chengcheng
     */
    public function resetPwdByOld()
    {
        //1 获取输入参数,email 邮箱,passwd 用户密码，token 手机验证码，
        $this->requestData['old_passwd'] = $this->input('oldPasswd', '');
        $this->requestData['new_passwd'] = $this->input('newPasswd', '');
        $this->requestData['member_id']  = Visitor::user()->id;

        //2.1 验证FEmail是否为空
        if (empty($this->requestData['old_passwd'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'old_passwd');
            return $this->ajaxReturn($result);
        }
        if (empty($this->requestData['new_passwd'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'new_passwd');
            return $this->ajaxReturn($result);
        }

        //3 重置密码
        $result = AuthService::resetPasswordByOld($this->requestData);

        //4 返回结果
        return $this->ajaxReturn($result);
    }

    /**
     * 获取token
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function getWsToken()
    {
        //获取参数
        $memberId = Visitor::user()->id;

        //返回结果
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        $result["data"] = $memberId;
        return $this->ajaxReturn($result);
    }

    /**
     * 获取用户信息
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function loginByQrToken()
    {
        //检查token
        $token   = $this->input('token', '');
        if(empty($token)){
            $result=[];
            $result["code"]         = Code::SYSTEM_ERROR;
            $result["msg"]          = "token不能为空。";
            return $this->ajaxReturn($result);
        }

        $checkToken =  PublicToken::model()->where('token',$token)->where('available_time','>',time())->first();
        if(empty($checkToken)){
            $result=[];
            $result["code"]         = Code::SYSTEM_ERROR;
            $result["msg"]          = "token不正确，或者已经失效。";
            return $this->ajaxReturn($result);
        }
        $checkToken->member_id = Visitor::user()->id;
        $checkToken->save();

        // 返回结果
        $result["code"]         = Code::SYSTEM_OK;
        $result["msg"]          = Msg::SYSTEM_OK;
        return $this->ajaxReturn($result);
    }
}