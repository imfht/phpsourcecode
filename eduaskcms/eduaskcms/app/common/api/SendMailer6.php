<?php
namespace app\common\api;

##php >5.5
class SendMailer6
{
    private $Host;
    private $From;
    private $Password;
    private $FromName;
    
    public function __construct()
    {  
        $this->Host = trim(setting('email_host'));
        $this->From = trim(setting('email_from'));
        $this->Password = trim(setting('email_password'));
        $this->FromName = trim(setting('email_fromname'));
        
        if (empty($this->Host) || empty($this->From) || empty($this->Password) || empty($this->FromName)) {
            exception('请在“系统设置”中配置邮件接口');
            exit;
        }        
    }
    /**
    * @param $tomail string 接受邮件的账号
    * @param $subject string 邮件标题
    * @param $body string 邮件主体内容 支持html
    * @param $fromname string 发件人名称 可以不填
    * @param $attachment array 附件路径
    */ 
    public function send($tomail, $subject, $body, $fromname = '', $attachment = null)
    {
        include \Env::get('root_path') . 'vendor' . DS . 'PHPMailer-6.0.1' . DS . 'src' . DS . 'PHPMailer.php';
        
        include \Env::get('root_path') . 'vendor' . DS . 'PHPMailer-6.0.1' . DS . 'src' . DS . 'SMTP.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer;
        //设置编码 
        $mail->CharSet = 'utf-8';
        //告诉服务器使用什么协议
        $mail->isSMTP();
        //我的服务商
        $mail->Host = $this->Host;
        //打开smtp认证
        $mail->SMTPAuth = true ;
        //发送html
        $mail->isHTML(true);
        
        //发件人
        $mail->Username = $this->From;
        $mail->Password = $this->Password;
        $mail->From  = $this->From;
        $mail->FromName = $fromname ? $fromname : $this->FromName;
        
        //发件内容
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        //收件人
        settype($tomail, 'array');
        foreach ($tomail as $each) {
            $mail->addAddress($each);
        }
        
        //添加附件
        //添加附件
        if (!empty($attachment)) {
            settype($attachment, 'array');
            foreach ($attachment as $file) {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
                
        return $mail->Send() ? true : $mail->ErrorInfo;
    }    
}
