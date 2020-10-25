<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 短信请求接口服务（继承用户中心）
 */
namespace app\system\controller\api\v1;
use app\system\controller\api\Base;
use app\common\model\SystemMember;
use app\common\event\Passport;
use app\common\facade\Alisms as AppAlisms;

class Alisms extends Base {

     /**
     * 不限制随便发送
     */
    public function getSms(){
        if(request()->isPost()){
            $data = [
                'phone_id'  => $this->request->param('phone/s')
            ];
            $validate = $this->validate($data,'Sms.getsms');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            $sms = AppAlisms::putSms($data['phone_id'],$this->miniapp->member_id);
            return json($sms);
        }else{
            return $this->error("404 NOT FOUND");
        }
    } 

    /**
     * 获取注册验证码
     */
    public function getRegSms(){
        if(request()->isPost()){
            $data = [
                'phone_id'  => $this->request->param('phone/s')
            ];
            $validate = $this->validate($data,'Sms.getsms');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            $user  = SystemMember::where(['phone_id' => $data['phone_id']])->find();
            if(isset($user)) {
                return json(['code'=>0,'message' => "手机已被注册"]);
            }
            $sms = AppAlisms::putSms($data['phone_id'],$this->miniapp->member_id);
            return json($sms);
        }else{
            return $this->error("404 NOT FOUND");
        }
    } 
  
    /**
     * 获取登录/找回密码等验证码
     */
    public function getLoginSms(){
        if(request()->isPost()){
            $data = [
                'phone_id'  => $this->request->param('phone/s')
            ];
            $validate = $this->validate($data,'Sms.getsms');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            //判断是否登录
            $getuser = Passport::getUser();
            if($getuser){
                if($data['phone_id'] != $getuser['phone_id']){
                    return json(['code'=>0,'message'=>"请确认手机号输入正确"]);
                }
            }
            $user  = SystemMember::where(['phone_id' => $data['phone_id']])->find();
            if(empty($user)) {
                return json(['code'=>0,'message'=>"用户不存在"]);
            }
            $sms = AppAlisms::putSms($data['phone_id'],$this->miniapp->member_id);
            return json($sms);
        }else{
            return $this->error("404 NOT FOUND");
        }
    } 
}