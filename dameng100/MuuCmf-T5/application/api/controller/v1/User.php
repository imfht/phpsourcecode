<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use Firebase\JWT\JWT;
use app\api\controller\Api;
use app\api\controller\UnauthorizedException;
use app\api\controller\v1\Base;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 */
class User extends Base
{   
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';
    public $apiAuth = false;

    
    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */
    public function index()
    {   
        //验证权限
        if($this->checkAccessToken() !== true){
            return $this->sendError($this->checkAccessToken());
        }

        $uid = $this->uid;

        if(!$uid){
            return $this->sendError('uid error');
        }

        $user_info = query_user([
            'uid',
            'nickname',
            'sex',
            'birthday',
            'reg_ip',
            'last_login_ip',
            'last_login_time',
            'avatar32',
            'avatar128',
            'mobile',
            'email',
            'username',
            'title',
            'signature',
        ], $uid);

        //获取已绑定微信的用户的openid
        $open_id = model('weixin/WeixinOauth')->getOpenid();

        if($open_id){
            $user_info['open_id'] = $open_id;
        }
        
        return $this->sendSuccess('success',$user_info);
    }

    /**
     * post方式
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        //根据action 参数判断操作类型
        $action = input('action','','text');
        switch($action){
            case 'save'://修改用户基本信息
                //需要登陆

                if($this->checkAccessToken() !== true){
                    return $this->sendError($this->checkAccessToken());
                }

                $uid = $this->uid;
                $mobile = input('mobile',0,'intval');//手机号
                $email = input('email','','text');//EMAIL
                $verify = input('verify',0,'intval');//验证码
                $nickname = input('nickname','','text');//昵称
                $sex = input('sex','','intval');//性别
                $signature = input('signature','','text');//签名
                $community = input('post.community', 0, 'intval');
                $district = input('post.district', 0, 'intval');
                $city = input('post.city', 0, 'intval');
                $province = input('post.province', 0, 'intval');
                
                if($uid){
                    if($mobile && $mobile!=0) {
                        $time = time();
                        $resend_time =  modC('SMS_RESEND','60','USERCONFIG');
                        if($time > session('verify_time')+$resend_time ){//验证码超时
                            return $this->sendError('验证码超时');
                        }
                        $ret = model('Verify')->checkVerify($moblie,'mobile',$verify,$uid);
                        if(!$ret){//验证码错误
                            return $this->sendError('验证码错误');
                        }
                        $udata['mobile'] = $mobile;
                    }

                    if($email){
                        $ret = model('Verify')->checkVerify($email,'email',$verify,$uid);
                        if($ret){
                            $udata['email'] = $email;
                        }else{
                            return $this->sendError('邮箱和验证不匹配');
                        }
                    }
                    
                    if($nickname){
                        $mdata['nickname'] = $nickname;
                    }
                    if($sex==1 || $sex==2 || $sex==0){
                        $mdata['sex'] = $sex;
                    }
                    if($signature){
                        $mdata['signature'] = $signature;
                    }
                    /*用户地区*/
                    if($community){
                        $mdata['community'] = $community;
                    }
                    if($district){
                        $mdata['district'] = $district;
                    }
                    if($city){
                        $mdata['city'] = $city;
                    }
                    if($province){
                        $mdata['province'] = $province;
                    }
                    /*用户地区END*/

                    //更新数据
                    if(isset($mdata)){
                        model("common/Member")->save($mdata,['uid'=>$uid]);
                    }
                    if(isset($udata)){
                        model('ucenter/UcenterMember')->save($udata,['uid'=>$uid]);
                    }
                    
                    //清理用户缓存
                    clean_query_user_cache($uid,['nickname','mobile','email','sex','signature']);
                    
                    return $this->sendSuccess('更新完成');
                }
            break;

            case 'register'://用户注册
                //获取参数
                $email = input('post.email','','text');
                $mobile = input('post.mobile','','text');
                $aRegType = input('post.reg_type', 'mobile', 'text');//注册类型，email mobile
                $aNickname = input('post.nickname', '', 'text');
                $aPassword = input('post.password', '', 'text');
                $aRegVerify = input('post.reg_verify', '', 'text');
                $aRole = input('post.role', 1, 'intval'); //初始角色

                if($aRegType == 'email'){
                    $aUsername = $username = $email;
                }
                if($aRegType == 'mobile'){
                    $aUsername = $username = $mobile;
                }
                if(empty($aNickname)){ //昵称为空，昵称等于注册的手机或邮箱
                    $aNickname = rand_nickname();
                }
                
                //注册开关关闭，直接返回错误
                if (!modC('REG_SWITCH', '', 'USERCONFIG')) {
                    return $this->sendError(lang('_ERROR_REGISTER_CLOSED_'));
                }

                //注册用户
                $return = model('ActionLimit')->checkActionLimit('reg', 'ucenter_member', 1, 1, true);
                if ($return && !$return['state']) {
                    return $this->sendError($return['info']);
                }
                if (!$aRole) {
                    return $this->sendError(lang('_ERROR_ROLE_SELECT_').lang('_PERIOD_'));
                }
                //手机或邮箱的验证
                if (($aRegType == 'mobile' && modC('MOBILE_VERIFY_TYPE', 0, 'USERCONFIG') == 1) || (modC('EMAIL_VERIFY_TYPE', 0, 'USERCONFIG') == 2 && $aRegType == 'email')) {
                    if (!model('Verify')->checkVerify($aUsername, $aRegType, $aRegVerify, 0)) {
                        $str = $aRegType == 'mobile' ? lang('_PHONE_') : lang('_EMAIL_');
                        return $this->sendError($aRegType.'验证失败');
                    }
                }

                $aUnType = 0;
                //获取注册类型
                check_username($aUsername, $email, $mobile, $aUnType);
                if ($aRegType == 'email' && $aUnType != 2) {
                    return $this->sendError(lang('_ERROR_EMAIL_FORMAT_'));
                }
                if ($aRegType == 'mobile' && $aUnType != 3) {
                    return $this->sendError(lang('_ERROR_PHONE_FORMAT_'));
                }
                if ($aRegType == 'username' && $aUnType != 1) {
                    return $this->sendError(lang('_ERROR_USERNAME_FORMAT_'));
                }
                if (!check_reg_type($aUnType)) {
                    return $this->sendError(lang('_ERROR_REGISTER_NOT_OPENED_'));
                }

                /* 注册用户 */
                $error_code = $uid = model('ucenter/UcenterMember')->register($aUsername, $aNickname, $aPassword, $email, $mobile, $aUnType);
                
                if (0 < $uid) { //注册成功
                    model('common/Member')->initRoleUser($aRole, $uid); //初始化角色用户

                    $uid = model('ucenter/UcenterMember')->login($username, $aPassword, $aUnType); //通过账号密码取到uid

                    //返回用户jwt验证token
                    $access_token = self::createAccessToken($uid);
                    $refresh_token = self::createRefreshToken($uid);

                    $token_data = ['access_token' => $access_token,'refresh_token' => $refresh_token];
                    
                    return $this->sendSuccess('success',$token_data);

                } else { //注册失败，显示错误信息
                    return $this->sendError(model('ucenter/Member')->showRegError($error_code));
                }
  
            break;

            case 'login'://用户登陆
                $aUsername = $username = input('post.account', '', 'text');
                $aPassword = input('post.password', '', 'text');
                check_username($aUsername, $email, $mobile, $aUnType);

                //根据用户账号密码获取用户ID或返回错误码
                $code = $uid = model('ucenter/UcenterMember')->login($username, $aPassword, $aUnType);
                
                if($code > 0){

                    //返回用户jwt验证token
                    $access_token = self::createAccessToken($uid);
                    $refresh_token = self::createRefreshToken($uid);

                    $token_data = ['access_token' => $access_token,'refresh_token' => $refresh_token];
                    
                    return $this->sendSuccess('success',$token_data);
                    
                }else{
                    $msg = model('common/Member')->showRegError($code);
                    return $this->sendError($msg);
                }
                
            break;

            case 'quick_login'://通过手机号和验证码快速登陆
                $mobile = input('post.mobile','','text');
                $verify = input('post.verify','','text');//接收到的验证码

                //检查验证码是否正确
                $ret = model('Verify')->checkVerify($mobile,'mobile',$verify,0);
                if(!$ret){//验证码错误
                    return $this->sendError($aRegType.'验证失败');
                }
                $resend_time =  modC('SMS_RESEND','60','USERCONFIG');
                if(time() > session('verify_time')+$resend_time ){//验证超时
                    return $this->sendError($aRegType.'验证超时');
                }
                
                //验证通过后获取用户UID
                $uid = model('UcenterMember')->where(['mobile' => $mobile])->column('id');
                //根据ID登陆用户
                if($uid){
                    $jwt = self::createJwt($uid);

                    return $this->sendSuccess('success',$jwt);
                }else{
                    return $this->sendError('login error');
                }
                
            break;

            case 'change_password'://修改密码

                //需要验证登陆
                if($this->checkAccessToken() !== true){
                    return $this->sendError($this->checkAccessToken());
                }
                    
                $old_password = input('post.old_password','','text');
                $new_password = input('post.new_password','','text');
                $confirm_password = input('post.confirm_password','','text');

                if($old_password && $new_password){
                    //检查旧密码是否正确
                    $ret = model('ucenter/UcenterMember')->verifyUser(get_uid(),$old_password);
                    if($ret){
                        //重置用户密码
                        $rs =  model('ucenter/UcenterMember')->changePassword($old_password, $new_password, $confirm_password);

                        if($rs === true){
                            return $this->sendSuccess('密码修改成功');
                        }
                    }else{
                        return $this->sendError('旧密码错误');
                    }
                }
                return $this->sendError('change password error');

            break;

            case 'find_password'://通过手机或邮箱找回密码

                $account = input('post.account','','text');
                $type = input('post.type','','text');
                $verify = input('post.verify','','text');//接收到的验证码
                $password = input('post.password','','text');//新密码设置
                
                //检查验证码是否正确
                $ret = model('Verify')->checkVerify($account,$type,$verify,0);
                if(!$ret){//验证码错误
                    return $this->sendError('验证失败');
                }
                $resend_time =  modC('SMS_RESEND','60','USERCONFIG');
                if(time() > session('verify_time') + $resend_time ){//验证超时
                    return $this->sendError('验证超时');
                }
                //获取用户UID
                switch ($type) {
                    case 'mobile':
                    $uid = model('ucenter/UcenterMember')->where(['mobile' => $account])->column('id');
                    break;
                    case 'email':
                    $uid = model('ucenter/UcenterMember')->where(['email' => $account])->column('id');
                    break;
                }
                //设置新密码
                $password = user_md5($password, config('database.auth_key'));
                $data['id'] = $uid;
                $data['password'] = $password;
                
                $ret = model('UcenterMember')->save($data,['uid'=>$uid]);
                if($ret){
                    //返回成功信息前处理
                    clean_query_user_cache($uid, 'password');//删除缓存
                    Db::name('user_token')->where('uid=' . $uid)->delete();
                    //返回数据
                    return $this->sendSuccess('密码修改成功');
                }
                return $this->sendError('error');

            break;

            case 'upload_avatar'://上传头像
                
                //验证权限
                if($this->checkAccessToken() !== true){
                    return $this->sendError($this->checkAccessToken());
                }

                $uid = $this->uid;
                
                /* 调用文件上传组件上传文件 */
                $files = request()->file();
                
                if (empty($files)) {
                    return $this->sendError('No Avatar Image upload or server upload limit exceeded');
                }

                $arr = model('api/Upload')->upload($files,'avatar','avatar',$uid);

                if(is_array($arr)){
                    return $this->sendSuccess('上传成功',$arr);
                }else{
                    return $this->sendError(model('api/Upload')->getError());
                }
            break;

            case 'save_avatar'://保存裁切后的头像
            
                //验证权限
                if($this->checkAccessToken() !== true){
                    return $this->sendError($this->checkAccessToken());
                }

                $aCrop = input('post.crop', '', 'text');
                $aUid = $uid = $this->uid;
                $aPath = input('post.path', '', 'text');
                
                if (empty($aCrop)) {
                    $this->sendSuccess(lang('_SUCCESS_SAVE_').lang('_EXCLAMATION_'));
                }

                $returnPath = controller('ucenter/UploadAvatar', 'widget')->cropPicture($aCrop,$aPath);

                $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');

                //更新数据库数据
                $data = [
                    'uid' => $aUid,
                    'status' => 1, 
                    'is_temp' => 0,
                    'path' => $returnPath,
                    'driver'=> $driver, 
                    'create_time' => time()
                ];
                $res = Db::name('avatar')->where(['uid' => $aUid])->update($data);
                if (!$res) {
                    Db::name('avatar')->insert($data);
                }
                clean_query_user_cache($aUid, ['avatars','avatars_html']);
                
                return $this->sendSuccess(lang('_SUCCESS_AVATAR_CHANGE_').lang('_EXCLAMATION_'));

            break;

            case 'refresh_token'://刷新token

                $result = $this->checkRefreshToken();
                if(isset($result['status']) && $result['status'] == 1001){
                    return $this->sendSuccess('刷新成功',$result);
                }else{
                    return $this->sendError($result);
                }

            break;

            default:
                return $this->sendError('大小给个参数啥滴');
        } 
    }

    /**
     * get方式
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {   
        $aUid = $id;
        if($aUid){
            $map['uid'] = $aUid;
            $userData=model('member')->where($map)->find();
            if($userData){
                $data = query_user([
                    'uid',
                    'nickname',
                    'sex',
                    'birthday',
                    'reg_ip',
                    'last_login_ip',
                    'last_login_time',
                    'avatar32',
                    'avatar128',
                    'mobile',
                    'email',
                    'username',
                    'title',
                    'signature',
                    'score',
                    'score1',
                    'score2',
                    'score3',
                    'score4'
                ], $aUid);
                
                return $this->sendSuccess('success',$data);
            }else{
                return $this->sendError();  
            }
            return $this->sendError('uid参数错误');  
        }
    }

    /**
     * 根据uid获取已绑定微信的用户openid
     *
     * @param      <type>  $uid    The uid
     */
    protected function getOpenid($uid)
    {
        $open_id = model('weixin/WeixinOauth')->getOpenid();

        return $open_id;
    }

    /**
     * PUT方式
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
        return 'update';
    }

    /**
     * delete方式
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        return 'delete';
    }

    /**
     * 获取除资源方法外的方法
     */
    public function fans($id)
    {
        return $id;
    }

    public function login()
    {
        
    }
}
