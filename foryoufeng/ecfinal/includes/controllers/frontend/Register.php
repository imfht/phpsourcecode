<?php
/**
 * 用户模型
 * Created by PhpStorm.
 * User: root
 * Date: 7/13/16
 * Time: 7:41 PM
 */
include_once UTILS.'Utils.php';
include_once UTILS.'phone/PhoneMsg.php';
class Register extends Frontend
{
    use Utils;

    /**
     * 显示首页默认数据
     */
    public function index()
    {
        if ($_POST) {
            $user_name = $this->checkUser();//用户名检查
            $pwd = trim(I('pwd'));
            $repwd = trim(I('repwd'));
            $phone = $this->checkPhone();//检查手机号
            $code = trim(I('code'));
            $company = trim(I('company')) ? trim(I('company')) : 0;
            if (!$user_name || !$pwd || !$repwd || !$phone || !$code) {
                $this->error('信息不完整');
            }
            //密码检查
            if (strlen($pwd) < 6 || $pwd != $repwd) {
                $this->error('密码格式不正确');
            }
            //检查图像验证码
            if(!$this->check_vcode()){
                $this->error('图像验证码错误');
            }
            //进行短信验证
            $data=M('phone')->check_code($phone,Constant::PHONE_REGISTER,$code);
            if(!$data){
                $this->error('短信验证不通过');
            }else {//检查通过了
                $data['mobile_phone'] = $phone;
                $data['user_name'] = $user_name;
                $data['password'] = md5($pwd);
                $data['msn'] = $company;
                $data['ec_salt'] = '';
                $data['status'] = 1;
                $data['up_time'] = time();
                $data['reg_time'] = $data['up_time'];
                $data['last_time'] = $data['up_time'];
                $data['is_validated'] = 0;
                $user_id = $this->model->add($data);
                if ($user_id) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $data['user_name'];
                    $this->success('注册成功');
                } else {
                    $this->error('注册失败');
                }
            }
        } else {

            if(!$this->is_cached('user/register.html')){
                $this->getTdk(30);//获取描述信息
            }
            $this->display('user/register.html');

        }
    }

    /**
     * 检查用户
     */
    public function checkUser()
    {
        $user_name = trim(I('user_name'));
        if (strlen($user_name) < 3 || strlen($user_name) > 40) {
            $this->error('用户长度符合不要求');
        }
        $data=$this->model->where(['user_name'=>$user_name])->find();
        if ($data) {
            $this->error('用户已存在');
        }
        return $user_name;
    }

    /**
     * 检查手机号
     */
    public function checkPhone()
    {
        $phone = trim(I('phone'));
        $type = trim(I('type'));

        if ($type == Constant::PHONE_REGISTER) {//注册
            $data = $this->model->where(['mobile_phone'=>$phone])->find();
            if ($data) {
                $this->error('手机号已存在');
            }
        } elseif ($type == Constant::PHONE_FORGET) {//找回密码
            $data = $this->model->where(['mobile_phone'=>$phone])->find();
            if (!$data) {
                $this->error('手机号不存在');
            }
        } else {//非法请求
            $this->error('请求错误');
        }
        return $phone;
    }

    /**
     * 发送短信验证码
     */
    public function sengMsg()
    {
        $phone = $this->checkPhone();
        //检查图像验证码
        if(!$this->check_vcode()){
            $this->error('图像验证码错误');
        }

        $code = rand(1000, 9999);

        if (M('phone')->is_send($phone,I('type'))) {
            $this->error('短信已经发送');
        }
        if (M('phone')->send($phone, $code, Constant::PHONE_REGISTER)) {//短信发送成功
            $this->success('短信发送success');
        } else {
            $this->error('短信发送失败');
        }
    }
}