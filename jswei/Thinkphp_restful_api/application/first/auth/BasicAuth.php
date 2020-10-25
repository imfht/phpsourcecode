<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// |  User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/9 15:18
// +----------------------------------------------------------------------
// | TITLE: 测试的基础验证
// +----------------------------------------------------------------------
namespace app\first\auth;

use app\first\model\Member;
use DawnApi\auth\Basic;
use think\Exception;
use think\Request;
use DawnApi\exception\UnauthorizedException;
use DawnApi\facade\Send;

class BasicAuth extends Basic
{
    use Send;
    /**
     * 获取用户信息后 验证权限,
     * @param Request $request
     * @return bool
     */
    public function certification(Request $request)
    {
        try {
            //用户权限
            //return $this->getClient($request)->checkSign();
            return true;
        } catch (UnauthorizedException $e) {
            return $this->sendError(0,lang('authentication'));
        } catch (Exception $e) {
            return $this->sendError(0,lang('authentication'));
        }
    }

    /**
     * 获取用户信息
     * @return array|mixed|\think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUser()
    {
        $member =  Member::getMember([
            'phone'=>$this->username,
            'password'=>substr($this->password, 10, 15)
        ]);
        if(!$member){
            return $this->sendError(0,lang('authentication'));
        }
        return $member->toArray();
    }

    /**
     * 获取客户端
     * @param Request $request
     * @return $this|object
     */
    public function getClient(Request $request)
    {
        $authorization = $request->header('authorization');
        $authorization = str_replace("Basic ", "", $authorization);
        $authorization = explode(':', base64_decode($authorization));
        $username = $authorization[0];//$_SERVER['PHP_AUTH_USER']
        $password = $authorization[1];//$_SERVER['PHP_AUTH_PW']
        $this->username = $username;
        $this->password = $password;
        return $this;
    }

    /**
     * 验证用户
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkSign(){
        $member =  Member::getMember([
            'username'=>$this->username,
            'password'=>substr($this->password, 10, 15)
        ]);
        return $member?true:false;
    }
}