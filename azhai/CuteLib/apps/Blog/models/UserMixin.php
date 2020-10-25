<?php
namespace Blog\Model;

use \Cute\Utility\Word;


/**
 * 用户
 */
trait UserMixin
{
    protected static $pass_head = '1$'; //密码前缀
    protected static $salt_size = 6;    //salt长度
    protected $user_pass = '';
    public $user_login = '';

    public function setPassword($password)
    {
        $salt = self::generateSalt();
        $this->user_pass = $this->hashPassword($password, $salt);
    }

    public static function generateSalt()
    {
        return Word::randHash(self::$salt_size);
    }

    public function hashPassword($password, $salt = '')
    {
        assert(strlen($salt) === self::$salt_size);
        $password = md5($salt . $password);
        return self::$pass_head . $salt . $password;
    }

    public function verifyPassword($password)
    {
        assert(!empty($this->user_pass));
        if (starts_with($this->user_pass, self::$pass_head)) {
            $offset = strlen(self::$pass_head);
            $salt = substr($this->user_pass, $offset, self::$salt_size);
            return $this->hashPassword($password, $salt) === $this->user_pass;
        }
        return false;
    }
}