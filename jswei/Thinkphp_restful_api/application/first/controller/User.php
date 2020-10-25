<?php

namespace app\first\controller;

use app\first\validate\Member as MemberValidate;
use app\first\model\Member as Member;
use think\facade\Request;
use think\facade\Validate;

/**
 * Class User
 * @title 用户接口
 * @url /v1/user
 * @desc  有关于用户的接口
 * @version 1.0
 * @readme
 */
class User extends Base
{

    //是否开启授权认证
    public $apiAuth = true;
    //附加方法
    protected $extraActionList = [];

    //跳过鉴权的方法
    protected $skipAuthActionList = ['sendCode','register','login','checkCode'];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /**
     * @title 用户登录
     * @method login
     * @param string $phone 账号 true
     * @param string $password 密码 true md5
     * @route('v1/user/login')
     * @return Object User 用户信息
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login(){
        $data = request()->post();
        $memberValidate= new MemberValidate;
        if(!$memberValidate->scene('login')->check($data)){
           return $this->sendError(0,$memberValidate->getError());
        }
        $member = new Member;
        $_member = $member::where('username',$data['username'])->find();
        if(!$_member){
            return $this->sendError(0,lang('unknown'));
        }
        $_member = $_member->toArray();
        $pwd = $this->get_password($data['password'],1);
        if($_member['password']!=$pwd){
            return $this->sendError(0,lang('error',[lang('password')]));
        }
        return $this->sendSuccess(['status'=>1,'message'=>lang('success',[lang('login')]),'user'=>$member::getMember($_member['id'])]);
    }

    /**
     * @title 发送验证码
     * @method sendCode
     * @param string $to 手机号/邮箱 true
     * @return json data 发送结果
     * @readme
     */
    public function sendCode($to='')
    {
        if(!$to){
            return $this->sendError(0,lang('empty',[lang('receive')]));
        }
        if(strstr($to,'@')===false){
            if(!Validate::is($to,'/^1[34578]\d{9}$/')){
                return $this->sendError(0,lang('unrequire',[lang('phone'),lang('phone')]));
            }
            $arr = send_sms($to);
            return $this->sendSuccess($arr);
        }else{
            if(!Validate::is($to,'email')){
                return $this->sendError(0,lang('unrequire',[lang('phone'),lang('email')]));
            }
            $_code = no_random(0,9,6);
            $html = "【".$this->site['title']."】:您本次的验证码:".
                $_code.",有效时间为15分钟.如果您没有使用【".$this->site['title']
                ."】相关产品,请自动忽略此邮件谢谢:)";

            if(!think_send_mail($to, $to,"【".$this->site['title']."】",$html)){
                return $this->sendError(0,lang('error',['']));
            }
            cookie($_code.'_session_code', $_code, 60*15);
            return $this->sendSuccess(['status'=>1,'message'=>lang('done',[lang('send')])]);
        }
    }

    /**
     * @title 检查验证码是否正确
     * @method checkCode
     * @param string $code 验证码 true
     * @return json data 验证信息
     */
    public function checkCode($code=''){
        if(!$code){
            return $this->sendError(0,lang('empty',[lang('verify')]));
        }
        $data = $this->check_verify($code);
        return $this->sendSuccess($data);
    }

    /**
     * @title 修改手机号
     * @method phone
     * @param int $id 用户id true
     * @param string $phone 手机号 true
     * @return Object User 用户信息
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\exception\DbException
     */
    public function phone($id=0,$phone=''){
        $validate = new MemberValidate;
        if(!$id){
            return $this->sendError(0,lang('require',[lang('phone')]));
        }
        $data = \request()->post();
        if(!$validate->scene('phone')->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $member = Member::get($id);
        if(!$member){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        if(!db('member')->update($data)){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        $_m = $member::getMember($id);
        return $this->sendSuccess(['status'=>1,'message'=>lang('success',[lang('upgrade')]),'user'=>$_m]);
    }

    /**
     * @title 修改头像
     * @method head
     * @param int $id 用户id true
     * @param Object/string head File文件/base64 true
     * @return Object User 用户信息
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function head($id=0){
        if(!request()->isPost()){
            return $this->sendError(0,lang('error',[lang('unrequest')]));
        }
        if(!$id){
            return $this->sendError(0,'用户id必填');
        }
        $upload = new Uploadify();
        $_file = $upload->upload_head();
        if(!$_file['status']){
            return $this->sendError(0,$_file['message']);
        }
        $member = new Member;
        $m  = $member::getMember($id);
        if(!$m){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        $m->head = $_file['path'];
        if(!$m->save()){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        $_m = $member::getMember($id);
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('success',[lang('upgrade')]),
            'user'=>$_m
        ]);
    }


    /**
     * @title 设置签名
     * @method info
     * @param int $id 用户id true
     * @param string $info 个性签名 true
     * @return Object 用户信息
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function info($id=0,$info=''){
        if(!request()->isPost()){
            return $this->sendError(0,lang('unrequest'));
        }
        $data = request()->post();
        $validate = new MemberValidate;
        if(!$validate->scene('info')->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $member = new Member;
        $_member = $member::get($id);
        if(!$_member){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        $_member->information=$info;
        if(!$_member->save()){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        $_m = $member::getMember($id);
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('upgrade')])]),
            'user'=>$_m
        ]);
    }

    /**
     * @title 修改密码
     * @method password
     * @param int $id 用户id true
     * @param string $password 密码 true null md5
     * @return json data 修改结果
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function password($id=0,$password=''){
        if(!request()->isPost()){
            return $this->sendError(0,lang('unrequest'));
        }
        $data = request()->post();
        $validate = new MemberValidate;
        if(!$validate->scene('password')->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $member = new Member;
        $_member = $member::get($id);

        if(!$_member){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        $_member->password = $this->get_password($password,1);
        if(!$_member->save()){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('upgrade')])])
        ]);
    }

    /**
     * @title 设置用户喜好
     * @method hobbies
     * @param string $id 用户id true
     * @param string $hobbies 用户喜好 true
     * @return Object User 用户信息
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function hobbies($id='',$hobbies=''){
        if(!request()->isPost()){
            return $this->sendError(0,lang('unrequest'));
        }
        $data = request()->post();
        $validate = new MemberValidate;
        if(!$validate->scene('hobby')->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $member = new Member;
        $_member = $member::get($id);
        if(!$_member){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        $_member->hobbies = $hobbies;

        if(!$_member->save()){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        $_m = Member::getMember($id);
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('upgrade')])]),
            'user'=>$_m
        ]);
    }

    /**
     * @title 获取列表
     * @method get
     * @param string $id 用户id
     * @return Object User 用户信息
     * @readme
     */
    public function get($id=0)
    {
        if(!$id){
            return $this->sendError(0,lang('empty',[lang('id')]));
        }
        $member = new Member;
        $user = $member::getMember($id);
        if(!$user){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('query')])]),
            'user'=>$user
        ]);
    }

    /**
     * @title 创建用户
     * @return Object User  注册用户
     * @route('v1/user/register')
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     */
    public function register()
    {
        $data = request()->post();
        $validate = new MemberValidate;
        if(!$validate->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $member = new Member;
        $last = Member::order('id desc')->find()->toArray();
        $client_id = intval($last['client_id'])+1;
        $client_id = str_pad($client_id, 10, "0", STR_PAD_LEFT);
        $data['password'] = $this->get_password($data['password'],1);
        $data['client_id'] = $client_id;
        $data['secret'] = $this->getSecret();
        if(!$member->allowField(true)->save($data)){
            return $this->sendError(0,$member->getError());
        }
        $_member = $member::getMember($member->getLastInsID());
        return $this->sendSuccess(['status'=>1,'message'=>lang('done',[lang('register')]),'data'=>$_member]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @title 更新用户
     * @param  int $id 主键
     * @return string name 名字
     * @readme
     * @return \think\Response
     */
    public function update($id)
    {
        $testUserData = self::testUserData();
        $data = $testUserData[$id];
        //更新
        $data['age'] = \request()->post('');
        return $this->sendSuccess($data);
    }

    /**
     * @title  修改用户昵称
     * @return Object User 用户
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function nickname($id=0,$nickname=''){
        if(!request()->isPost()){
            return $this->sendError(0,lang('unrequest'));
        }
        $data = request()->post();
        $validate = new MemberValidate;
        if(!$validate->scene('nickname')->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $member = new Member;
        $_member = $member::get($id);
        if(!$_member){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        $_member->nickname = $nickname;

        if(!$_member->save()){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        $m = $member::getMember($id);
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('upgrade')])]),
            'data'=>$m
        ]);
    }

    /**
     * @title 修改性别
     * @method sex
     * @param int $id 用户id true
     * @param int $sex 性别 ture -1 -1未知,0男,1女
     * @return Object User 用户信息
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function sex($id=0,$sex=-1){
        if(!request()->isPost()){
            return $this->sendError(0,lang('unrequest'));
        }
        $data = request()->post();
        $validate = new MemberValidate;
        if(!$validate->scene('sex')->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $member = new Member;
        $_member = $member::get($id);
        if(!$_member){
            return $this->sendError(0,lang('unalready',[lang('user')]));
        }
        $_member->sex = $sex;
        if(!$_member->save()){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        $m = $member::getMember($id);
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('upgrade')])]),
            'data'=>$m
        ]);
    }
    /**
     * @title 删除指定资源
     * @param  int $id 主键 true
     * @return object user  用户信息
     * @title 删除用户
     * @return \think\Response
     */
    public function delete($id)
    {
        $validate = new MemberValidate;
        if (!$validate->scene('delete')->check(['id'=>$id])) {
            return $this->sendError(0,$validate->getError());
        }
       $user = Member::getMember($id);
       if(!$user){
           return $this->sendError(0,lang('unalready',[lang('user')]));
       }
       Member::destroy($id);
       return $this->sendSuccess(['status'=>1,'data'=>$user]);
    }

}