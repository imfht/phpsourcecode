<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信公众号回调处理
 */
namespace app\system\controller\event;
use think\Controller;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemUser;
use app\common\event\User;
use app\common\facade\WechatMp as Mp;
use encrypter\Encrypter;
use filter\Filter;
use Exception;

class WechatMp extends Controller{

    /**
     *  发起微信授权
     */
    public function putWechat(){
        try {
            $app    = $this->request->param('app/d');
            $scope  = $this->request->param('scope/d',1);
            $url    = $this->request->param('url');
            $official = Mp::isTypes($app);
            if(!$official){
                $this->error('请先授权您的公众号');
            }
            $response = $official->oauth->scopes([$scope ? 'snsapi_userinfo' : 'snsapi_base'])->redirect(url('system/event.wechatMp/getWechat',['app' => $app,'url' => $url],true,true));
            return $response->send();
        }catch (Exception $e) {
            $this->error('授权失败');
        }
    }
    
    /**
     *  回调微信授权
     */
    public function getWechat(){
        $app  = $this->request->param('app/d');
        $code = $this->request->param('code');
        $url  = $this->request->param('url');
        if(empty($code)){
            return $this->redirect('system/event.wechatMp/putWechat',['app' => $app,'url' => $url]);
        }
        //判断是否开放平台应用
        $miniapp = SystemMemberMiniapp::where(['id' => $app])->find();
        if(!$miniapp){
            $this->error('未找到已授权应用');
        }
        $official = Mp::isTypes($app);
        if(!$official){
            $this->error('请先授权您的公众号');
        }
        $rel = $official->oauth->user();
        $result = SystemUser::where(['member_miniapp_id' => $miniapp->miniapp_id,'official_uid' => $rel->getID()])->find();
        if(empty($result)){
            $nickName = Filter::filter_Emoji($rel->getName());
            $data['miniapp_id']   = $app;
            $data['wechat_uid']   = empty($rel['unionid']) ? '' : $rel['unionid'];
            $data['official_uid'] = $rel->getID();
            $data['nickname']     = $nickName ?? '微信-'.time();
            $data['avatar']       = $rel->getAvatar();
            $data['miniapp_uid']  = '';
            $data['session_key']  = '';
            $uid = SystemUser::wechatReg($data,false);
            if(!$uid){
                return $this->redirect(Encrypter::cpDecode($url));
            }
            User::setLogin(['id'=> $uid,'nickname'=>$rel->getName()]);
        }else{
            User::setLogin($result);
        }
        return $this->redirect(Encrypter::cpDecode($url));
    }
}