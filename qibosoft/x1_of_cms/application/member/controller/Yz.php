<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;


class Yz extends MemberBase
{
    /**
     * 获取邮箱或手机注册码
     * @param string $type
     */
    public function getnum($type='',$to=''){
        //邮箱注册码与手机注册码,不建议同时启用,所以这里没分开处理
        if( time()-get_cookie('send_num') <60 ){
            return $this->err_js('1分钟后,才能再次获取验证码!');
        }elseif( time()-cache('send_num'.md5(get_ip()))<60){
            return $this->err_js('1分钟后,当前IP才能再次获取验证码!');
        }
        $num = cache(get_cookie('user_sid').$to) ?: rand(1000,9999);
        $send_num = $num;
        //$send_num = get_md5_num($to.$num,6);
        $title = '来自《'.config('webdb.webname').'》的验证码,请注意查收';
        $content = '你的验证码是:'.$send_num;
        cache(get_cookie('user_sid').$to,$num,600);
        if($type=='mobphone'){
            $result = send_sms($to,$send_num);
        }elseif($type=='email'){
            $rs = UserModel::get_info($to,'email');
            if($rs && $rs['uid']!=$this->user['uid'] ){
                $result = '当前邮箱已经被另个一帐号 '.$rs['username'].' 占用了,请更换一个邮箱';
            }else{
                $result = send_mail($to,$title,$content);
            }
        }else{
            $result = '请选择类型!';
        }
        if($result===true){
            set_cookie('send_num', time());
            cache('send_num'.md5(get_ip()),time(),100);
            return $this->ok_js();
        }else{
            return $this->err_js($result);
        }
    }
    
    /**
     * 验证邮箱
     * @param string $email 新邮箱
     * @param string $email_code 验证码
     * @param string $old_email 旧邮箱
     * @return mixed|string
     */
    public function index($email='',$email_code='',$old_email='')
    {
        if($this->request->isPost()){
            $num = cache(get_cookie('user_sid').$email);
            $send_num = $num;
            //$send_num = get_md5_num($email.$num,6);
            if( $email_code!=$send_num  || empty($num)) {
                $this->error('验证码不对');
            }
            
            if($this->user['email_yz'] && $old_email!=$this->user['email']){
                $this->error('旧邮箱不对');
            }
            
            $array = [
                    'uid'=>$this->user['uid'],
                    'email'=>$email,
                    'email_yz'=>1,
            ];
            if (UserModel::edit_user($array)) {
                $this->success('验证成功','index');
            }else{
                $this->error('数据写入失败');
            }
        }
        $this->user['email'] = preg_replace("/([\w]{2})(.*?)@(.*+)/i","\\1***@\\3",$this->user['email']);
        return $this->fetch();
    }
    
    
    /**
     * 证件验证
     * @return mixed|string
     */
    public function idcard()
    {
        if (plugins_config('baidu_api')) {
            $url = purl('baidu_api/certificates/identity_index',[],'member');
            return $this->redirect($url); 
        }
        $data = get_post('post');
        if($this->request->isPost()){
            if($this->user['idcard_yz']){
                $this->error('资料已通过审核,不可再修改');
            }
            if($data['truename']==''||$data['idcard']==''){
                $this->error('主体名称与证件号码为必填项');
            }
            $array = [
                    'uid'=>$this->user['uid'],
                    'truename'=>$data['truename'],
                    'idcard'=>$data['idcard'],
                     'idcardpic'=>$data['idcardpic'],
            ];
            if (UserModel::edit_user($array)) {
                $title = $this->user['username'].'申请实名认证了，请尽快进后台用户资料管理那里进行审核！';
                $content = $title;
                send_admin_msg($title,$content);
                $this->success('数据已成功提交,请等待管理员人工审核');
            }else{
                $this->error('数据写入失败');
            }
        }
        return $this->fetch();
    }
    
    /**
     * 验证手机号
     * @param string $mobphone 新手机号
     * @param string $mobphone_code 验证码
     * @param string $old_mobphone 旧手机号
     * @return mixed|string
     */
    public function mob($mobphone='',$mobphone_code='',$old_mobphone='')
    {
        if($this->request->isPost()){            
            $num = cache(get_cookie('user_sid').$mobphone);
            $send_num = $num;
            //$send_num = get_md5_num($mobphone.$num,6);
            if( $mobphone_code!=$send_num  || empty($num)) {
                $this->error('验证码不对');
            }
            
            if($this->user['mob_yz'] && $old_mobphone!=$this->user['mobphone']){
                $this->error('旧手机号码不对');
            }
            
            $array = [
                    'uid'=>$this->user['uid'],
                    'mobphone'=>$mobphone,
                    'mob_yz'=>1,
            ];
            
            if (UserModel::edit_user($array)) {
                $this->success('验证成功','index');
            }else{
                $this->error('数据写入失败');
            }
        }        
        return $this->fetch();
    }    

}
