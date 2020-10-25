<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 平台用户接口
 */
namespace app\system\controller\api\v1;
use app\system\controller\api\Base;
use app\common\model\SystemUserAddress;
use app\common\model\SystemUserLevel;
use app\common\model\SystemUser;
use app\common\facade\Alisms;
use think\facade\Request;

class User extends Base{

    public function initialize() {
        parent::initialize();
        $this->isUserAuth();
    }

    /**
     * 获取默认地址
     * @return void
     */
    public function getAddress(){
        $param['signkey'] = Request::param('signkey');
        $param['sign']    = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $result  = SystemUserAddress::field('name,telphone,is_first,address,id')->where(['user_id'=>$this->user->id,'is_first'=>1])->find();
        if(empty($result)){
            return enjson(204);
        }else{
            return enjson(200,$result);
        }
    }
    
    /**
     * 保存或修改我的地址
     */
    public function createAddress(){
        if(request()->isPost()){
            $param = [
                'name'     => Request::param('name/s'),
                'telphone' => Request::param('telphone/s'),
                'city'     => Request::param('city/s'),
                'address'  => Request::param('address/s'),
                'sign'     => Request::param('sign/s'),
            ];
            $validate = $this->validate($param,'Address.add');
            if(true !== $validate){
                return json(['code'=>403,'msg'=>$validate]);
            }
            if(!empty($param['sign'])){
                $rel = $this->apiSign($param);
                if($rel['code'] != 200){
                    return enjson(403,'签名失败');
                }
            }
            //把所有地址重置非默认
            $data['name']              = $param['name'];
            $data['telphone']          = $param['telphone'];
            $data['is_first']          = 1;
            $data['address']           = $param['city'].$param['address'];
            $data['user_id']           = $this->user->id;
            $data['member_miniapp_id'] = $this->miniapp_id;
            $data['create_time']       = time();
            $data['update_time']       = time();
            SystemUserAddress::where(['user_id' => $this->user->id])->update(['is_first' => 0]);
            $result  = SystemUserAddress::insertGetId($data);
            if($result){
                $data['id'] = $result;
                return json(['code'=>200,'data'=>$data,'msg'=>'获取成功']);
            }else{
                return json(['code'=>403,'msg'=>'获取失败']);
            }
        }else{
            return json(['code'=>401,'msg'=>'用户认证失败']);
        }
    }

    /**
     * 绑定手机号验证码
     * @param  int $user_id 用户ID
     * @param  string $safepassword 验证的安全密码
     */
    public function bindPhoneNumber(){
        if (request()->isPost()) {
            $data = [
                'phone' => Request::param('phone/s'),
                'code'  => Request::param('code/d'),
            ];
            $validate = $this->validate($data, 'User.bindphone');
            if (true !== $validate) {
                return json(['code'=>403,'msg'=>$validate]);
            }
            //判断验证码
            $is_sms = Alisms::isSms($data['phone'],$data['code']);
            if (!$is_sms) {
                return json(['code'=>403,'msg'=>"验证码错误"]);
            }
            //验证码通过
            $result  = SystemUser::where(['id' =>$this->user->id])->update(['phone_uid' => $data['phone']]);
            if ($result) {
                return json(['code'=>200,'msg'=>'绑定成功']);
            }
            return json(['code'=>403,'msg'=>'绑定失败']);
        }
    }

    /**
     * 获取绑定手机号验证码
     * @param  int $user_id 用户ID
     * @param  string $safepassword 验证的安全密码
     */
    public function getPhoneCode(){
        if (request()->isPost()) {
            $data = [
                'phone' => Request::param('phone/s'),
                'types' => Request::param('types/d'),
            ];
            $validate = $this->validate($data, 'User.getphone');
            if (true !== $validate) {
                return json(['code'=>403,'msg'=>$validate]);
            }
            if ($data['types']) { //验证自己手机号
                if ($this->user->phone_uid != $data['phone']) {
                    return json(['code'=>403,'msg'=>'不是您绑定的手机号']);
                }
            } else {
                //新绑定
                $rel = SystemUser::where(['member_miniapp_id'=>$this->miniapp_id,'phone_uid' => $data['phone']])->field('phone_uid')->find();
                if(!empty($rel)){
                    return json(['code'=>403,'msg'=>'手机号已绑定']);
                }
            }
            $rel = Alisms::putSms($data['phone'],$this->miniapp->member_id);
            if ($rel['code'] == 200) {
                return json(['code'=>200,'msg'=>$rel['message'],'data'=>session_id()]);
            } else {
                return json(['code'=>403,'msg'=>$rel['message']]);
            }
        }
    }

    /**
     * 获取已绑定手机号验证码
     * @param  int $user_id 用户ID
     * @param  string $safepassword 验证的安全密码
     */
    public function getUserPhoneCode(){
        if(empty($this->user->phone_uid)){
            return json(['code'=>403,'msg'=>'你没有绑定手机号']);
        }
        $rel =  Alisms::putSms($this->user->phone_uid,$this->miniapp->member_id);
        if($rel['code'] == 200){
            return json(['code'=>200,'msg'=>$rel['message'],'data'=>session_id()]);
        }else{
            return json(['code'=>403,'msg'=>$rel['message']]);
        }
    }


    /**
     * 获取已绑定手机号验证码
     * @param  int $user_id 用户ID
     * @param  string $safepassword 验证的安全密码
     */
    public function getFriendPhoneCode(){
        if (request()->isPost()) {
            $data['phone'] = Request::param('phone/s');
            $validate = $this->validate($data, 'User.getphone');
            if (true !== $validate) {
                return json(['code'=>403,'msg'=>$validate]);
            }
            if($this->user->phone_uid == $data['phone']){
                return json(['code'=>403,'msg'=>'非好友手机号']);
            }
            //查找所属用户
            $user = SystemUser::field('phone_uid')->where(['member_miniapp_id'=>$this->miniapp_id,'phone_uid' => $data['phone']])->find();
            if(empty($user)){
                return json(['code'=>403,'msg'=>'未找到当前用户']);
            }    
            $rel = Alisms::putSms($user->phone_uid,$this->miniapp->member_id);
            if ($rel['code'] == 200) {
                return json(['code'=>200,'msg'=>$rel['message'],'data'=>session_id()]);
            } else {
                return json(['code'=>403,'msg'=>$rel['message']]);
            }
        }
    }

     /**
     * 验证是否绑定手机号
     * @param  int $user_id 用户ID
     * @param  string $safepassword 验证的安全密码
     */
    public function isBandPhone(){
        if(empty($this->user->phone_uid)){
            return json(['code'=>204,'msg'=>'未绑']);
        }
        return json(['code'=>200,'msg'=>'绑定']);
    }

    /**
     * 验证是否设置安全密码
     * @param  int $user_id 用户ID
     * @param  string $safepassword 验证的安全密码
     */
    public function isSafePassword(int $types = 0){
        if($types){
            if(!$this->user->phone_uid){
               return json(['code'=>302,'msg'=>'请先认证手机号','url'=>'/pages/helper/bindphone']);
            }
        }
        if($this->user->safe_password){
            return json(['code'=>200,'msg'=>'已设置安全密码']);
        }else{
            return json(['code'=>204,'msg'=>'未设置安全密码']);
        }        
    }

    /**
     * 检查旧的安全密码
     */
    public function checkSafePassword(){
        if(request()->isPost()){
            $data = [
                'safepassword' => Request::param('safepassword/s'),
            ];
            $validate = $this->validate($data,'User.safepassword');
            if(true !== $validate){
                return json(['code'=>403,'msg'=>$validate]);
            }
            if(password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return json(['code'=>200,'msg'=>'验证通过']);
            }else{
                return json(['code'=>403,'msg'=>'安全密码不正确']);
            }
        }
    }

    /**
     * 设置安全密码
     *
     * @return void
     */
    public function setSafePassword(){
        if(request()->isPost()){
            $data = [
                'safepassword'      => Request::param('safepassword/s'),
                'password_confirm'  => Request::param('resafepassword/s'),
                'code'              => Request::param('code/s'),
            ];
            $validate = $this->validate($data,'User.setSafePassword');
            if(true !== $validate){
                return json(['code'=>403,'msg'=>$validate]);
            }
            //判断安全密码是否正确
            $is_sms = Alisms::isSms($this->user->phone_uid,$data['code']);
            if (!$is_sms) {
                return json(['code'=>403,'msg'=>"验证码错误"]);
            }
            $result =  SystemUser::updateSafePasspord($this->user->id,$data['safepassword']);
            if($result){
                return json(['code'=>200,'msg'=>'修改成功']);
            }else{
                return json(['code'=>403,'msg'=>'修改失败']);
            } 
        }
    }

    /**
     * 读取我的推荐用户(只显示两层关系)
     * @return void
     */
    public function levelUser(){
        $info = SystemUserLevel::levelUser($this->user->id,1);
        if($info){
            return json(['code'=>200,'msg'=>'成功','data' => $info]);  
        }
        return json(['code'=>204,'msg'=>'空内容']);  
    }
    
    /**
     * 获取邀请码的用户信息
     * @return void
     */
    public function getCodeUser(){
        return $this->getUCodeUser();
    }
}