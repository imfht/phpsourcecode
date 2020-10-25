<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信统一公共服务接口
 */
namespace app\system\controller\api\v1;
use app\system\controller\api\Base;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemUser;
use app\common\facade\WechatMp;
use app\common\facade\WechatProgram;
use app\common\facade\Alisms;
use app\common\facade\Upload;
use think\facade\Request;
use Exception;

class Miniapp extends Base{
    
    /**
     * 小程序登录接口
     * @return void
     */
    public function login(){
        return self::miniappLogin();
    }
    
    /**
     * 检查应用是否登录
     * @return void
     */
    public function checkLogin(){
        if($this->user) {
            return enjson(200,'已登录');
        } else {
            return enjson(401,'未登录');
        }
    }

    /**
     * 判断是否应用创始人
     * @return void
     */
    public function isManage(){
        if ($this->user) {
            $result = SystemMemberMiniapp::field('uid')->where(['id'=>$this->miniapp_id])->find();
            if ($result->uid == $this->user->id) {
                return enjson(200,'是管理员',['status'=>1]);
            }
        }
        return enjson(204,'非管理员',['status'=>0]);
    }

    /**
     * 获取邀请码的用户信息
     * @return void
     */
    public function getCodeUser(){
        return $this->getUCodeUser();
    }
    
    /**
     * 通过小程序绑定公众号帐号
     * @param [type] $ids
     * @return void
     */
    public function bindOfficial(){
        $code = Request::param('code');
        $app  = Request::param('app/d');
        $official = WechatMp::isTypes($app);
        if(empty($official)){
            return view('api/bind_official_error');
        }
        if(empty($code)){
            $response = $official->oauth->scopes(['snsapi_base'])->redirect(api(1,'system/miniapp/bindOfficial',$app));
            return $response->send();
        }else{
            $view['openid'] = $official->oauth->user()->getID();
            $view['app']    = $app;
            return view('api/bind_official')->assign($view);
        }
    }

    /**
     * 绑定微信小程序的公众号
     * @return void
     */
    public function bindWechatPhone(){
        $this->isUserAuth();
        if (request()->isPost()) {
            $param = [];
            $param['errMsg']        = Request::param('errMsg','getPhoneNumber:fail');
            $param['encryptedData'] = Request::param('encryptedData');
            $param['iv']            = Request::param('iv');
            $param['sign']          = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] = 200){
                try {  
                    $decryptedData = WechatProgram::isTypes($this->miniapp_id)->encryptor->decryptData($this->user->session_key,$param['iv'],$param['encryptedData']);
                    if(!empty($decryptedData['purePhoneNumber'])){
                        $phone = $decryptedData['purePhoneNumber'];
                        if ($this->user->phone_uid == $phone) {
                            return enjson(403,'手机号相同不用更换');
                        }
                        $rel = SystemUser::where(['member_miniapp_id' => $this->miniapp_id,'phone_uid' => $phone])->field('id')->count();
                        if($rel){
                            return enjson(403,'手机号已被占用');
                        }
                        //验证码通过
                        $result  = SystemUser::where(['id' =>$this->user->id])->update(['phone_uid' => $phone]);
                        if ($result) {
                            return enjson(200,'手机号绑定成功',['phone_uid' => $phone]);
                        }
                        return enjson(403,'手机号绑定失败');
                    }
                }catch (Exception $e) {
                    return enjson(401,'请先登录帐号');
                }
            }else{
                return enjson($rel['code'],$rel['msg']);
            }            
        }
    }

     /**
     * 获取绑定手机验证码
     * @return void
     */
    public function getBindWechatPhoneSms(){
        $this->isUserAuth();
        if (request()->isPost()) {
            $param = [];
            $param['errMsg']        = Request::param('errMsg','getPhoneNumber:fail');
            $param['encryptedData'] = Request::param('encryptedData');
            $param['iv']            = Request::param('iv');
            $param['sign']          = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson($rel['code'],$rel['msg']);
            }
            $decryptedData = WechatProgram::isTypes($this->miniapp_id)->encryptor->decryptData($this->user->session_key,$param['iv'],$param['encryptedData']);
            if(!empty($decryptedData['purePhoneNumber'])){
                $phone = $decryptedData['purePhoneNumber'];
                if ($this->user->phone_uid != $phone) {
                    return enjson(403,'手机号验证失败');
                }
                $data['phone_id'] = $this->user->phone_uid;
                $data['code']     = getcode(6);
                Alisms::setSms($data);
                return enjson(200,'验证成功',['session_id' =>session_id(),'sms' => $data['code']]);
            }
        }
    }
 
    /**
     * 商城图片上传图片
     * @return void
     */
    public function upload(){
        $this->isUserAuth();
        if(request()->isPost()){
            $rel = Upload::index(strtolower(create_code($this->member_miniapp_id)).DS.'user');
            if($rel['error'] == 0){
                return enjson(200,'成功',$rel['url']);
            }else{
                return enjson(204);
            }
        }
    }
}