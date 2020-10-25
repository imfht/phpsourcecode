<?php
namespace Action;
use HY\Action;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class User extends HYBBS {
    public $menu_action;
    public function __construct(){
		parent::__construct();
        //{hook a_user_init}

        $this->view = IS_MOBILE ? $this->conf['wapuserview2'] : $this->conf['userview2'];

    }
    public function _no(){
        header("location: ".WWW);
        exit;
    }
    //消息跳转 设置已读 
    public function mess(){
        //{hook a_user_mess_1}
        if(!IS_LOGIN)
            return $this->message('请登录');
        //{hook a_user_mess_2}
        $id = intval(X("get.id") );
        if(empty($id))
            return $this->message('ID参数不完整');
        //{hook a_user_mess_3}
        $Mess = M("Mess");
        $data = $Mess->read($id);
        if(empty($data))
            return $this->message('不存在该消息');
        if($data['uid'] != $this->_user['id'])
            return $this->message('这条消息不属于你');
        //{hook a_user_mess_4}
        //设置已读
        if(!$data['view']) //如果是未读状态
        {
            $Mess->set_state($id);
            //未读消息 -1
            M("User")->update_int($data['uid'],'mess',"-");
        }
        //{hook a_user_mess_v}

        header("location: ".WWW.URL('thread',$data['tid']) );
        exit;



    }
    public function Edit(){
        //{hook a_user_edit_1}
        if(!IS_LOGIN)
            return $this->message('请登录');
        $ps = htmlspecialchars(strip_tags(X("post.ps")));
        if(!empty($ps)){
            S("User")->update(array(
                'ps'=>$ps
            ),array(
                'id'=>NOW_UID
            ));
            return $this->message('保存成功',true);
        }

        $pass1 = X("post.pass1");
        $pass2 = X("post.pass2");
        //{hook a_user_edit_2}
        if($pass1 != $pass2)
            return $this->message("两次密码不一致");
        $UserLib = L("User");
        if(!$UserLib->check_pass($pass1))
            return $this->message('密码不符合规则');
        //{hook a_user_edit_3}
        $newpass = $UserLib->md5_md5($pass1,$this->_user['salt']);
        $this->_user['pass'] = $newpass;
        S("User")->update(array(
            'pass'=>$this->_user['pass']
        ),array(
            'id'=>$this->_user['id']
        ));
        //{hook a_user_edit_4}
        cookie('HYBBS_HEX',$UserLib->set_cookie($this->_user));
        return $this->message("修改成功",true);

    }


    //找回密码
    public function repass(){
        //{hook a_user_repass_1}
        $this->v("title","找回密码");
        if(IS_LOGIN)
            return $this->message("你已经登录,请注销后找回密码?");
        //{hook a_user_repass_2}
        if(IS_GET){
            
            $this->display('user_repass');
        }
    }
    //提交更改密码
    public function recode2(){
        //{hook a_user_recode2_1}
        $email = X("post.email");
        $code = strtoupper(X("post.code"));
        $pass1=X("post.pass1");
        $pass2=X("post.pass2");
        //{hook a_user_recode2_2}
        if(empty($email)||empty($code)||empty($pass1)||empty($pass2))
            $this->json(array('error'=>false,'info'=>'参数不完整,请填写好表单!'));
        if($pass1 != $pass2)
            $this->json(array('error'=>false,'info'=>'确认密码不一致'));
        //{hook a_user_recode2_3}
        $UserLib = L("User");
        if(!$UserLib->check_pass($pass1))
            $this->json(array('error'=>false,'info'=>'新密码不符合规则,必须大于等于5位'));
        //{hook a_user_recode2_4}
        $User = M("User");

        if(!$User->is_email($email))
            $this->json(array('error'=>false,'info'=>'邮箱不存在!'));
        $data = $User->email_read($email);
        if(empty($data))
            $this->json(array('error'=>false,'info'=>'邮箱不存在.'));
        //{hook a_user_recode2_5}
        if(strlen($code) != 6)
            $this->json(array('error'=>false,'info'=>'验证码是6位的.'));
        //{hook a_user_recode2_6}
        $cookie = cookie("HY_EMAIL");
        if(empty($cookie))
            $this->json(array('error'=>false,'info'=>'验证码已经过期,请24小时后再来修改密码,紧急请联系管理员.'));

        //{hook a_user_recode2_7}
        $Encrypt = L("Encrypt");
        $cr = $Encrypt->decrypt($cookie,$data['salt'].C("MD5_KEY"));
        if($cr != $code)
            $this->json(array('error'=>false,'info'=>'验证码错误.'));
        //{hook a_user_recode2_8}
        $User->update(array('pass'=>L("User")->md5_md5($pass1,$data['salt'])),array('id'=>$data['id']));
        cookie('HY_EMAIL',null);
        $this->json(array('error'=>true,'info'=>'修改成功.'));


    }
    //发送验证码
    public function recode(){
        //{hook a_user_recode_1}
        $email = X("post.email");
        

        $emailhost = $this->conf['emailhost'];
        $emailport = $this->conf['emailport'];
        $emailuser = $this->conf['emailuser'];
        $emailpass = $this->conf['emailpass'];
        //{hook a_user_recode_2}

        if(empty($emailhost) || empty($emailport))
            $this->json(array('error'=>false,'info'=>'网站没开启邮箱功能,请联系网站管理员'));
        //{hook a_user_recode_3}
        $User = M("User");
        if(!$User->is_email($email))
            $this->json(array('error'=>false,'info'=>'该邮箱不存在!'));
        //{hook a_user_recode_4}
        $data = M("User")->email_read($email);
        if(empty($data))
            $this->json(array('error'=>false,'info'=>'该邮箱不存在.'));
        //{hook a_user_recode_5}
        if($data['etime'] > NOW_TIME)
            $this->json(array('error'=>false,'info'=>'24小时你只允许发送一次验证码.'));

        //{hook a_user_recode_6}
        $code = rand_code(6);

        $Email = L("Email");

        $Encrypt = L("Encrypt");

        //{hook a_user_recode_7}
        $Email->init($emailhost,$emailport,true,$emailuser,$emailpass);
        if(!$Email->sendmail($email,$emailuser,$this->conf['emailtitle'],'验证码:'.$code.$this->conf['emailcontent'],'HTML'))
            $this->json(array('error'=>false,'info'=>'发送失败,具体原因:'.$Email->error_mess));
        cookie('HY_EMAIL',$Encrypt->encrypt($code,$data['salt'].C("MD5_KEY")),300); //有效期5分钟
        $User->update(array('etime'=>NOW_TIME+86400),array('id'=>$data['id']));
        $this->json(array('error'=>true,'info'=>'发送成功!'));
        

    }

    //登录账号
    public function Login(){
        //cookie("test",34);
        //{hook a_user_login_1}
        $this->v("title","登录页面");
        if(IS_LOGIN)
            return $this->message("你都已经登录了,登录那么多次干嘛");

        if(IS_GET){
            //{hook a_user_login_2}
            //
            $re_url = X("server.HTTP_REFERER");
            if(strpos($re_url,WWW)!= -1 && strpos($re_url,'/user')===false)
                cookie('re_url',$re_url);
            
            $this->display('user_login');
        }
        elseif(IS_POST){
            $user = X("post.user");
            $pass = X("post.pass");

            $UserLib = L("User");
            //{hook a_user_login_3}

            $msg = $UserLib->check_user($user);
            //检查用户名格式是否正确
            if(!empty($msg))
                return $this->message($msg);

            if(!$UserLib->check_pass($pass))
                return $this->message('密码不符合规则');
            //{hook a_user_login_4}
            $User = M("User");
            if(!$User->is_user($user))
                return $this->message("账号不存在!");

            $data = $User->find("*",array('user'=>$user));
            //{hook a_user_login_5}
            if(!empty($data)){
                //{hook a_user_login_51}
                
                //密码正确
                if($data['pass'] == $UserLib->md5_md5($pass,$data['salt'])){//登录成功
                    $Friend = S("Friend");
                    $sum = $Friend->sum("c",array('uid1'=>$data['id']));
                    M("Chat_count")->update(array('c'=>$sum),array('uid'=>$data['id']));

                    //{hook a_user_login_52}
                    //更新用户所有缓存 一个星期更新缓存
                    if($data['ctime']+(86400*7) < NOW_TIME){

                        $count1 = $Friend->count(array('AND'=>array('uid1'=>$data['id'],'OR'=>array('state'=>array(1,2)))));
                        $count2 = $Friend->count(array('AND'=>array('uid2'=>$data['id'],'OR'=>array('state'=>array(1,2)))));

                        $User->update(array(
                            'ctime'=>NOW_TIME,
                            'threads'=>S("Thread")->count(array('uid'=>$data['id'])),
                            'posts'=>S("Post")->count(array('uid'=>$data['id'])),
                            'follow'=>$count1,
                            'fans'=>$count2,
                            

                        ),array(
                            'id'=>$data['id']
                        ));
                    }
                    //在线用户记录
                    $ol = S('ol');
                    if($ol->has(array('uid'=>$data['id'])))
                        $ol->update(array('atime'=>NOW_TIME),array('uid'=>$data['id']));
                    else
                        $ol->insert(array('uid'=>$data['id'],'username'=>$data['user'],'ip'=>ip2long($_SERVER['ip']),'group'=>$data['group'],'atime'=>NOW_TIME));
                    //{hook a_user_login_53}

                    $this->update_ol_1();

                    //在线用户结束
                    cookie('HYBBS_HEX',$UserLib->set_cookie($data));
                    $this->init_user();
                    //{hook a_user_login_54}
                    $re_url = cookie('re_url');
                    cookie('re_url',null);
                    return $this->message("登录成功 !",true,$re_url);
                }else{
                    //{hook a_user_login_56}
                    return $this->message("密码错误!");
                }
            }else{
                return $this->message('账号数据不存在!');
            }
        }
        //{hook a_user_login_6}
    }
    //注册账号
    public function Add(){

        //{hook a_user_add_1}

        $this->v("title","注册用户");
        if(IS_LOGIN)
            return $this->message("你都已经登录了,还注册那么多账号干嘛");
        if(IS_GET){
            //{hook a_user_add_2}
            $re_url = X("server.HTTP_REFERER");
            
            if(strpos($re_url,WWW)!= -1 && strpos($re_url,'/user')===false)
                cookie('re_url',$re_url);
            $this->display('user_add');
        }
        elseif(IS_POST){
            $user = X("post.user");
            $pass1 = X("post.pass1");
            $pass2 = X("post.pass2");
            $email = X("post.email");
            //{hook a_user_add_3}
            if($pass1 != $pass2)
                return $this->message("两次密码不一致");

            $UserLib = L("User");
            $msg = $UserLib->check_user($user);
            //检查用户名格式是否正确
            if(!empty($msg))
                return $this->message($msg);

            if(!$UserLib->check_pass($pass1))
                return $this->message('密码不符合规则');

            //{hook a_user_add_4}

            $msg = $UserLib->check_email($email);

            if(!empty($msg))
                return $this->message($msg);

            //{hook a_user_add_5}
            $User = M("User");
            if($User->is_user($user))
                return $this->message("账号已经存在!");

            if($User->is_email($email))
                return $this->message("邮箱已经存在!");


            //{hook a_user_add_6}
            $User->add_user($user,$pass1,$email);


            cookie('HYBBS_HEX',$UserLib->set_cookie(
                            $User->read(
                                $User->user_to_id($user)
                            )
                        )
            );
            //{hook a_user_add_v}
            $this->_count['user']++;
            $this->_count['day_user']++;
            $this->CacheObj->bbs_count = $this->_count;
            $re_url = cookie('re_url');
            cookie('re_url',null);

            return $this->message("账号注册成功",true,$re_url);
        }
        //{hook a_user_add_7}
    }
    //上传头像
    public function ava(){
        //{hook a_user_ava_1}
        $this->v("title","更改头像");
        if(!IS_LOGIN) return $this->message("请登录后操作 Error =1 !");

        //{hook a_user_ava_2}
        $id = $this->_user['id'];
        if(empty($id)) return $this->message("请重新登录 Error =2  !");


        L("Upload");
        //{hook a_user_ava_3}
        $upload = new \Lib\Upload();
        $upload->maxSize   =     3145728 ;// 设置附件上传大小  3M
        $upload->exts      =     array('jpg', 'bmp', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     INDEX_PATH . 'upload/avatar/'; // 设置附件上传根目录
        $upload->saveExt    =   "jpg";
        $upload->replace    =   true;
        $upload->autoSub    =   false;
        $upload->saveName   =   md5($this->_user['user']);
        if(!is_dir(INDEX_PATH. "upload"))
			mkdir(INDEX_PATH. "upload");
        if(!is_dir($upload->rootPath))
            mkdir($upload->rootPath);
        //{hook a_user_ava_4}
        $info   =   $upload->upload();
        
        if(!$info)
            return $this->message("上传失败!");

        //{hook a_user_ava_5}
        $image = new \Lib\Image();
        $image->open(INDEX_PATH . 'upload/avatar/'.$upload->saveName.".jpg");
        // 生成一个缩放后填充大小150*150的缩略图并保存为thumb.jpg
        $image->thumb(250, 250,$image::IMAGE_THUMB_CENTER)->save(INDEX_PATH . 'upload/avatar/'.$upload->saveName."-a.jpg");
        $image->thumb(150, 150,$image::IMAGE_THUMB_CENTER)->save(INDEX_PATH . 'upload/avatar/'.$upload->saveName."-b.jpg");
        $image->thumb(50  , 50,$image::IMAGE_THUMB_CENTER)->save(INDEX_PATH . 'upload/avatar/'.$upload->saveName."-c.jpg");
        //$image->thumb(150, 150,\Think\Image::IMAGE_THUMB_CENTER)
        //{hook a_user_ava_v}
        return $this->message("上传成功!",true);

    }
    public function out(){
        //{hook a_user_out_1}
        if(!IS_LOGIN)
            $this->message('退出成功',true);
        //{hook a_user_out_v}
        $this->v("title","注销用户");
        S("ol")->delete(array('uid'=>NOW_UID));
        $this->update_ol_1();
        cookie('HYBBS_HEX',null);
        //{hook a_user_out_2}
        $this->init_user();
        $re_url = X("server.HTTP_REFERER");
        if(strpos($re_url,WWW)!= -1 && strpos($re_url,'/user')===false)
            return header("location: ".$re_url);
        
        $this->message('退出成功',true);


    }
    public function isuser(){
        //{hook a_user_isuser_v}
        $user = X("post.user");
        $bool = M("User")->is_user($user);
        return $this->json(array('error'=>$bool));
    }
    public function isemail(){
        //{hook a_user_isemail_v}
        $email = X("post.email");
        $bool = M("User")->is_email($email);
        return $this->json(array('error'=>$bool));
    }

    //{hook a_user_fun}
}
