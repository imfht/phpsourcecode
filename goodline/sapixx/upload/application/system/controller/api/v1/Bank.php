<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 个人银行账号
 */
namespace app\system\controller\api\v1;
use app\system\controller\api\Base;
use app\common\model\SystemUserBank;

class Bank extends Base{

    public function initialize() {
        parent::initialize();
        if(!$this->user){
            exit(json_encode(['code'=>401,'msg'=>'用户认证失败']));
        }
    }

    /**
     * 绑定个人应用信息
     **/ 
    public function bind(){
        if (request()->isPost()) {
            $data = [
                'name'              => $this->request->param('name/s'),
                'bankname'          => $this->request->param('bankname/s'),
                'idcard'            => $this->request->param('idcard/s'),
                'bankid'            => $this->request->param('bankid/s'),
                'bankid_confirm'    => $this->request->param('bankid_confirm/s'),
                'safepassword'      => $this->request->param('safepassword/s'),
            ];
            $validate = $this->validate($data, 'UserBank.bind');
            if (true !== $validate) {
                return json(['code'=>403,'msg'=>$validate]);
            }
            //判断安全密码是否正确
            if(!password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return json(['code'=>403,'msg'=>'安全密码不正确']);
            }
            //更新银行信息
            $rel = SystemUserBank::editer($this->miniapp_id,$this->user->id,$data);
            if($rel){
                return json(['code'=>200,'msg'=>"成功绑定"]);
            }else{
                return json(['code'=>403,'msg'=>"绑定失败"]);
            }
        }
    }
    
    /**
     * 获取银行信息
     */
    public function info(){
        if(!$this->user->safe_password){
            return json(['code'=>302,'msg'=>'请先设置您的安全密码','url'=>'/pages/user/safe']);
        }
        //更新银行信息
        $rel = SystemUserBank::field('name,idcard,bankname,bankid')->where(['member_miniapp_id'=>$this->miniapp_id,'user_id' => $this->user->id])->find();
        if($rel){
            $data['name']     = $rel['name'];
            $data['idcard']   = $rel['idcard'];
            $data['bankname'] = $rel['bankname'];
            $data['bankid']   = $rel['bankid'];
            return json(['code'=>200,'msg'=>"成功",'data' => $data]);
        }else{
            return json(['code'=>204,'msg'=>"失败"]);
        }
    }
}