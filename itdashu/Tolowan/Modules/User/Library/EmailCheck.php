<?php
namespace Modules\User\Library;

use Core\Config;
use Phalcon\Security\Random;
use Modules\User\Entity\User;
use Core\Library\Email;
use Modules\User\Models\EmailCheck as EmailCheckModel;

class EmailCheck
{
    public $userModel;
    protected $body;
    protected $emailCheck = null;
    protected $title;
    protected $salt;

    public function __construct($user)
    {
        if (!is_object($user)) {
            $user = User::findFirst($user);
            if (!$user) {
                return false;
            }
        } else {
            $this->userModel = $user;
        }
        $emailCheck = EmailCheckModel::findFirst($this->userModel->id);
        if (!$emailCheck) {
            $this->emailCheck = $emailCheck;
        }
    }

    public function setBody($body = false)
    {
        $config = Config::get('m.core.config');
        $this->title = $this->userModel->name . ':请激活您的邮箱';
        if ($body === false) {
            $output = '【' . $config['name'] . '】：' . $this->userModel->name . '，您正在验证这个邮箱，这是你的验证码：' . $this->getRandom();
            $this->body = $output;
        } else {
            $this->body = $body;
        }
    }

    public function check($salt)
    {
        if (is_null($this->emailCheck)) {
            return false;
        }
        if ($this->isOvertime() === false && $this->emailCheck->salt === $salt) {
            return true;
        }
        return false;
    }

    public function getRandom($len = 6)
    {
        if ($this->salt !== false) {
            return $this->salt;
        }
        $random = new Random();
        $this->salt = $random->hex($len);
        return $this->salt;
    }

    public function send()
    {
        $this->setBody();
        if (is_null($this->emailCheck)) {
            $this->emailCheck = new EmailCheckModel();
        }
        $this->emailCheck->id = $this->userModel->id;
        $this->emailCheck->email = $this->userModel->email;
        $this->emailCheck->salt = $this->salt;
        $this->emailCheck->created = time();
        if ($this->emailCheck->save()) {
            $sendState = Email::send($this->userModel->email, $this->userModel->name, $this->title, $this->body);
            if ($sendState['state'] === true) {
                //$this->flash->success('激活邮件已发送，请点击邮件中链接激活邮件');
                return true;
            } else {
                //$this->flash->error('邮件发送失败'.$sendState['manage']);
                return false;
            }
        } else {
            return false;
        }
    }

    //是否过期
    public function isOvertime()
    {
        if (is_null($this->emailCheck)) {
            return true;
        }
        return $this->emailCheck->isOvertime();
    }
}