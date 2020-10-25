<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\ucenter\controller;
use app\common\controller\ApiBase;
use app\common\model\UserFocus;
class Api extends ApiBase
{

    public function changeinfo(){
        // show($this->request->param());
        // die;
        if($this->request->isAJax()&&$this->getuid()>0){
            $where = $this->request->only(['uid']);
            $password = $this->request->only(['password']);
            $data = $this->request->param();
            $data['salt'] = rand_str(4);
            if(empty($password['password'])){
                unset($data['password']);
            }else{
               $data['password']=encode_pwd($password['password'],$data['salt']);
            }
            unset($data['uid']);
            if((int)$where['uid']>0){
                //修改
               $re = model('Base')->getedit('users',['where'=>$where],$data); 
           }else{
                //新加
               $where['uid']=model('Base')->getadd('users',$data);
            }
            
            if(model('Base')->getone('users_attrib',['where'=>$where,'cache'=>false])){
                //修改
                model('Base')->getedit('users_attrib',['where'=>$where],$data);
            }else{

                //新加
                $data['uid'] = $where['uid'];
                model('Base')->getadd('users_attrib',$data);
            }

            // $this->success('修改成功','','');
            return returnJson(0,'','修改成功');
        }

      
    }
    public function adminlogin(){

        $user_name = $this->request->only(['user_name']);
        $password = $this->request->only(['password']);
        $returnurl = $this->request->only(['returnurl']);
        $returnurl = $returnurl?decode($returnurl):"";
        if(empty($user_name['user_name'])||empty($password['password'])){
            returnJson(1,'','用户名或者密码不能为空');
        }
        $re  = $this->getbase->query("SELECT * FROM ".config('database.prefix')."admin where user_name='{$user_name['user_name']}' OR email='{$user_name['user_name']}'");
        $re =current($re);
        if(is_array($re)&&!empty($re)){
            //比对密码  
            if($re['password']==encode_pwd($password['password'],$re['salt'])){
                 parent::Auth('ucenter')->creatAdmin($re);
                 //hook('loginSuccess',$re);##登陆成功勾子
                 $data['url'] = input('gourl');
                 // show($data);
                 returnJson(0,$data,'');
            }else{
                returnJson(1,'','密码错误');
            }
        }else{
            returnJson(1,'','用户名不存在');
        }
    }
   /**
    * [logo 登陆接口处理]
    * @Author   Jerry
    * @DateTime 2017-04-30
    * @Example  eg:
    * @return   [type]     [description]
    */
    public function logo()
    {
        $user_name = $this->request->only(['user_name']);
        $password = $this->request->only(['password']);
        $returnurl = $this->request->only(['returnurl']);
        $returnurl = $returnurl?decode($returnurl):"";
        if(empty($user_name['user_name'])||empty($password['password'])){
            returnJson(1,'','用户名或者密码不能为空');
        }
        $re  = $this->getbase->query("SELECT * FROM ".config('database.prefix')."users where user_name='{$user_name['user_name']}' OR email='{$user_name['user_name']}'");
        $re =current($re);
        if(is_array($re)&&!empty($re)){
            //比对密码  
            if($re['password']==encode_pwd($password['password'],$re['salt'])){
                 parent::Auth('ucenter')->creatUser($re);
                 hook('loginSuccess',$re);##登陆成功勾子
                 $data['gourl'] = decode(input('gourl'));
                 returnJson(0,$data,'登陆成功');
            }else{
                returnJson(1,'','密码错误');
            }
        }else{
            returnJson(1,'','用户名不存在');
        }
    }
    /**
     * [reg 注册]
     * @return [type] [description]
     */
    public function reg(){
      if($this->request->isAJax()){
        $user_name = tostr($this->request->only(['user_name']));
        $password = tostr($this->request->only(['password']));
        $repassword = tostr($this->request->only(['repassword']));
        $email = tostr($this->request->only(['email']));
        //协议
        $agreement = tostr($this->request->only(['agreement']));
        //用户注册名不允许出现以下关键字:
        $not_user=explode(",", getset('censoruser'));

        // $this->vali($rule,$msg,$this->re)
        if(!$user_name) return returnJson(1,'','用户名不能为空');
        if(in_array($user_name, $not_user)) return returnJson(1,'','此用户名已被管理员设置为禁止注册');
        if(strlen($user_name)<getset('username_length_min')) return returnJson(1,'','用户名不能小于'.getset('username_length_min').'位');
        if(strlen($user_name)>getset('username_length_max')) return returnJson(1,'','用户名不能大于'.getset('username_length_max').'位');
        if(!filter($email, 'email')) return returnJson(1,'','邮箱格式错误');
        if(!$password) return returnJson(1,'','密码不能为空');
        if(strlen($password)<6) return returnJson(1,'','密码必须大于6位');
        if($password!=$repassword) return returnJson(1,'','两次密码不一样');
        //是否开启验证码
        if(getset('register_seccode')=="Y"){
          if(!captcha_check(current($this->request->only(['captcha']))))  return returnJson(1,'','验证码错误');
        }
        //注册协议
        if(!$agreement) $this->error('你必须同意注意协议才可以注册');
        if(model('Base')->getone('users',['where'=>['user_name'=>"{$user_name}"]])) return returnJson(1,'','用户名已存在');
        if(model('Base')->getone('users',['where'=>['email'=>"{$email}"]])) return returnJson(1,'','邮箱已存在');
        // show($password);
        $data['salt'] = rand_str(4);
        $data['password'] = encode_pwd($password,$data['salt']);
        $data['email'] = $email;
        $data['user_name'] = $user_name;
        $data['avatar_file'] = "/public/static/images/default_avatar/default.jpg";
        //是否开启邮箱验证
         if(getset('register_valid_type')=="Y"){ 
            $data['valid_email']=0;
        }else{
            $data['valid_email']=1;
        }
        // die;
        if(model('Base')->getadd('users',$data)){
            hook('regSuccess',$data);##注册成功勾子
          return returnJson(0,'','注册成功');
        }else{
          return returnJson(1,'','服务器异常,重试');
        }
      }
    }
    /**
     * [logout 前台退出]
     * @return [type] [description]
     */
    public function logout(){
      if($this->request->isAJax()){
        parent::Auth('ucenter')->logout();
        returnJson(0,'','退出成功');
      }
    }
    /**
     * [adminLoginOut 后台退出]
     * @Author   Jerry
     * @DateTime 2017-06-12T17:04:19+0800
     * @Example  eg:
     * @return   [type]                   [description]
     */
    public function adminLoginOut(){
        if($this->request->isAJax()){
            parent::Auth('ucenter')->delAdmin();
            returnJson(0,'','退出成功');
      }
    }

    public function focusUser(){
        if($this->request->isAJax()){
            $con=$this->request->param();
            $uid=$this->getuid();
            $data['fans_uid'] = $uid;
            $data['friend_uid'] = $con['uid'];
            if ($uid == 0){
                $this->success('请先登录','/ucenter/user/login.html',null,1);
            }
            $result= model('UserFocus')->get_focus_st($data);
            if (!empty($result)){
                $status=model('UserFocus')->unfocus($data);
                if (empty($status)){
                    $this->error('取消关注失败!');
                }else{
                    $this->success('取消关注成功!');
                }
            }else{
                $status=model('UserFocus')->focus($data);
                if (empty($status)){
                    $this->error('关注失败!');
                }else{
                    $this->success('关注成功!');
                }
            }
        }
    }
    /**
     * [bang_users 从第三方绑定到本地帐号，需要填写本地测试]
     * @return [type] [description]
     */
    public function bang_users(){
        if($this->request->isAJax()){
            $user_name = $this->request->only(['user_name']);
            $password = $this->request->only(['password']);
            $returnurl = $this->request->only(['returnurl']);
            if(empty($user_name['user_name'])||empty($password['password'])){
                return returnJson(1,'','用户名或者密码不能为空');
            }
            $re  = $this->getbase->query("SELECT * FROM ".config('database.prefix')."users where user_name='{$user_name['user_name']}' OR email='{$user_name['user_name']}'");
            $re =current($re);
            if(is_array($re)&&!empty($re)){
                //比对密码  
                if($re['password']==encode_pwd($password['password'],$re['salt'])){

                    if($this->insertsns($re['uid'],"qq")){
                         parent::Auth('ucenter')->creatUser($re);
                         hook('loginSuccess',$re);##登陆成功勾子
                          $data['url']='/';
                        // $this->success('绑定成功',"/");  
                        return returnJson(0,$data,'绑定成功');
                    }else{
                         return returnJson(0,'','服务器异常，请稍后再试');
                    }
                   
                }else{
                     return returnJson(1,'','密码错误');
                }
            }else{
                 return returnJson(1,'','用户名不存在');
            }

            
        }
    }
    /**
     * [creat_sns_user 通过第三方直接生成用户]
     * @return [type] [description]
     */
    public function creat_sns_user(){
         if($this->request->isAJax()){
        $user_name = tostr($this->request->only(['user_name']));
        $password = tostr($this->request->only(['password']));
        //用户注册名不允许出现以下关键字:
        $not_user=explode(",", getset('censoruser'));
        if(!$user_name) $this->error('用户名不能为空');
        if(in_array($user_name, $not_user))  return returnJson(1,'','此用户名已被管理员设置为禁止注册');
        if(strlen($user_name)<getset('username_length_min')) return returnJson(1,'','用户名不能小于'.getset('username_length_min').'位');
        if(strlen($user_name)>getset('username_length_max')) return returnJson(1,'','用户名不能大于'.getset('username_length_max').'位');
        if(!$password) return returnJson(1,'','密码不能为空');
        if(strlen($password)<6) return returnJson(1,'','密码必须大于6位');
        if(model('Base')->getone('users',['where'=>['user_name'=>"{$user_name}"]])) return returnJson(1,'','用户名已存在');
        $data['salt'] = rand_str(4);
        $data['password'] = encode_pwd($password,$data['salt']);
        $data['user_name'] = $user_name;
        //是否开启邮箱验证
         if(getset('register_valid_type')=="Y"){ 
            $data['valid_email']=0;
        }else{
            $data['valid_email']=1;
        }
        if($uid = model('Base')->getadd('users',$data)){
            //第三方信息
            if($this->insertsns($uid,"qq")){
                        $re =$this->getbase->getall('users',"uid = '{$uid}'"); 
                         parent::Auth('ucenter')->creatUser($re);
                         hook('loginSuccess',$re);##登陆成功勾子
                          $data['url']='/';
                        return returnJson(0,$data,'创建成功');
                }else{
                    return returnJson(1,'','服务器异常,重试');
                }
        }else{
          return returnJson(1,'','服务器异常,重试');
        }
      }
    }
    /**
     * [insertsns 插入sns信息]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    private function insertsns($uid,$type="qq"){
        switch ($type) {
            case 'qq':
               $table = "users_qq";
                break;
            
            default:
               $table = "users_qq";
                break;
        }
        $data = session('sns_token');
        $data['uid'] = $uid;
        if($this->getbase->getadd($table,$data)){
              return true;
           
        }else{
            return false;
        }
    }
}
