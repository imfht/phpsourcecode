<?php
namespace app\member\controller;

use app\common\controller\MemberBase;


class Bindlogin extends MemberBase
{

    /**
     * 绑定QQ登录
     * @return mixed|string
     */
    public function qq($ckcode='')
    {
        $need_check = $this->need_check($ckcode);
        if ($need_check==false) {
            $sid = get_cookie('user_sid');
            cache('bind_'.$sid,$this->user['uid'],600);
            $this->assign('sid',$sid);
        }
        $this->assign('need_check',$need_check);
        return $this->fetch();
    }
    
    /**
     * 检查是否需要验证手机或邮箱
     * @param string $ckcode
     * @return boolean|string
     */
    private function need_check($ckcode=''){        
        $need_check = false;
        if ($this->user['weixin_api']!='') {
            if ($this->user['mob_yz']) {
                $need_check = 'phone';
                if ($ckcode!='') {
                    if ($ckcode!=$this->user['mobphone']) {
                        $this->error('手机号码不对!');
                    }else{
                        $need_check = false;
                    }
                }
            }elseif($this->user['email_yz']){
                $need_check = 'email';
                if ($ckcode!='') {
                    if ($ckcode!=$this->user['email']) {
                        $this->error('邮箱不对!');
                    }else{
                        $need_check = false;
                    }
                }
            }
        }
        return $need_check;
    }
  
    /**
     * 绑定微信登录
     * @return mixed|string
     */
    public function weixin($ckcode='')
    {
        $need_check = $this->need_check($ckcode);
        if ($need_check==false) {
            $sid = get_cookie('user_sid');
            cache('bind_'.$sid,$this->user['uid'],600);
            $url = purl('weixin/login/index',[],'index').'?type=bind&sid='.$sid;
            $this->assign('url',$url);
        }
        $this->assign('need_check',$need_check);
        return $this->fetch();
    }
    
    public function ckbind($wxid=''){
        if($wxid!=$this->user['weixin_api']){
            return $this->ok_js();
        }else{
            return $this->err_js();
        }
    }


}
