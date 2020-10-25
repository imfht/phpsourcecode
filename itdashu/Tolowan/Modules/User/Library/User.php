<?php
namespace Modules\User\Library;

use Phalcon\Mvc\User\Plugin;

class User extends Plugin
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $active;
    public $email_active;
    public $phone_active;
    public $face;
    public $roles = array('anonymous' => 'anonymous');
    public $userCookie;
    protected $isLogin = false;

    public function __construct()
    {
        if ($this->cookies->has('user')) {
            $cookieUser = $this->cookies->get('user');
            if (@unserialize($cookieUser)) {
                $cookieUser = unserialize($cookieUser);
            }
            if (is_array($cookieUser) && isset($cookieUser['id']) && !empty(trim($cookieUser['id']))) {
                foreach ($cookieUser as $key => $value) {
                    $this->{$key} = $value;
                }
                $this->userCookie = $cookieUser;
                $this->isLogin = true;
            } else {
                $this->cookies->delete('user');
            }
        }
    }

    public function updateCookie($name, $value)
    {
        if (isset($this->userCookie[$name])) {
            $this->userCookie[$name] = $value;
            $this->{$name} = $value;
            $userCookie = serialize($this->userCookie);
            $this->cookies->set('user', $userCookie);
        } else {
            return false;
        }
    }

    public function isLogin()
    {
        return $this->isLogin;
    }

    public function isAdmin()
    {
        if (is_array($this->roles) && isset($this->roles['admin'])) {
            return true;
        }
        return false;
    }

    public function userinfo()
    {
        $output = '<li>用户ID：' . $this->uid . '</li>';
        $output .= '<li>登陆时间' . date('y-m-d H:i:s', $this->loginTime) . '</li>';
        $output = '<ul>' . $output . '</ul>';
        return $output;
    }

    public static function checkLogin($data)
    {
        global $di;
        $flash = $di->getShared('flash');
        $security = $di->getShared('security');
        if (isset($data['user']) && isset($data['password'])) {
            //$userType = 'name';
            if (preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', $data['user'])) {
                //$userType = 'email';
                $user = UserEntity::findFirstByEmail($data['user']);
            } elseif (preg_match("/^1[34578]\d{9}$/", $data['user'])) {
                //$userType = 'tel';
                $user = UserEntity::findFirstByPhone($data['user']);
                if (!$user) {
                    $user = UserEntity::findFirstByName($data['user']);
                }
            } else {
                $user = UserEntity::findFirstByName($data['user']);
            }
            if ($user) {
                if ($security->checkHash($data['password'], $user->password)) {
                    $flash->success('用户验证成功');
                    return $user;
                } else {
                    $flash->error('用户密码错误');
                }
            } else {
                $flash->error('用户不存在');
            }
        } else {
            $flash->error('用户手机号或密码不能为空');
        }
        return false;
    }

    public function login($id)
    {
        $entity = $this->entityManager->get('user');
        $user = $entity->findFirst($id, true);
        if ($user) {
            $userData = array(
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'active' => $user->active,
                'email_validate' => $user->email_validate,
                'phone_validate' => $user->phone_validate,
                'face' => $user->face->value,
                'roles' => [],
            );
            foreach ($user->roles as $role) {
                $userData['roles'][$role->value] = $role->state;
            }
            $this->isLogin = true;
            foreach ($userData as $key => $value) {
                $this->{$key} = $value;
            }
            $cookieUser = serialize($userData);
            $this->cookies->set('user', $cookieUser);
            return true;
        }
        return false;
    }

    public function logout()
    {
        $this->isLogin = false;
        $this->cookies->set('user', '');
    }
}
