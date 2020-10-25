<?php
namespace Common\Event;

class EmailEvent{

    public function send($email, $title, $content){
        lib("Msg.PHPMailer.PHPMailer");
        $mail = new \PHPMailer(); //实例化

        $mail->IsSMTP(); // 启用SMTP
        $mail->SMTPAuth = true; //启用smtp认证
        $mail->WordWrap = 50; //设置每行字符长度
        $mail->IsHTML(true); // 是否HTML格式邮件
        $mail->CharSet='utf-8'; //设置邮件编码

        $mail->Host=C('EMAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
        $mail->Username = C('EMAIL_USERNAME'); //你的邮箱名
        $mail->Password = C('EMAIL_PASSWORD'); //邮箱密码
        $mail->From = C('EMAIL_USERNAME'); //发件人地址（也就是你的邮箱地址）
        $mail->FromName = C('EMAIL_FROM'); //发件人姓名

        $mail->AddAddress($email);
        $mail->Subject =$title; //邮件主题
        $mail->Body = $content; //邮件内容
        //$mail->addAttachment('test.txt');
        //$mail->AltBody = "备用内容"; //邮件正文不支持HTML的备用显示
        if($mail->Send()){
        	return true;
        }else{
            return false;
        }   
    }

    public function verifyEmailSend($email){
        if(empty($email)) return array('status'=>0,'message'=>'邮箱不能为空');

        //TODO:邮箱格式验证
        //检测邮箱重复
        $map['email']=array('eq',$email);
        $map['id']=array('neq',UID);
        $status=M('user')->where($map)->getField('id');
        if($status) return array('status'=>0,'message'=>'邮箱'.$email.'已被占用,请更换其他邮箱');


        //发送邮件
        $email_verify=get_random(10);
        $link=U('Member/UserExtend/VerifyEmail',array('verify'=>$email_verify),'html',true);
        $email_tpl=D('Message')->getEmailTpl('email_verify');
        if(empty($email_tpl)){
            return array('status'=>0,'message'=>'认证邮件模板丢失');
        }
        $email_link='<a href="'.$link.'" target="_blank">'.$link.'</a>';
        $email_content=str_replace('#VERIFYLINK#',$email_link,$email_tpl['content']);
        $email_title=$email_tpl['title'];

        if(!$this->send($email,$email_title,$email_content)){
            return array('status'=>0,'message'=>'认证邮件发送失败');
        }
        //缓存邮箱验证码30分钟
        S('VerifyEmail-'.UID,$email_verify,108000);

        return array('status'=>1,'message'=>'认证邮件发送成功');
    }

    public function verifyEmailReturn($verify){
        if(empty($verify)) return false;

        //对比邮箱验证码缓存
        $verify_cache=S('VerifyEmail-'.UID);
        if($verify == $verify_cache){
            M('userExtend')->where('uid='.UID)->setfield('extend_email',1);
            S('VerifyEmail-'.UID,NULL);
            return array('status'=>1,'message'=>'验证邮件发送成功');
        }
        return array('status'=>0,'message'=>'邮箱验证链接已失效请重新认证');
    }
    
}