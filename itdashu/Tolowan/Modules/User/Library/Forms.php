<?php
namespace Modules\User\Library;

class Form{
    /*
        注册用户
         */
    public static function register($formEntity)
    {
        global $di;
        //$userConfig = Options::cacheGet('user_setting', array());
        $user = new Muser();
        $user->name = $formEntity['settings']['formData']['username'];
        $user->email = $formEntity['settings']['formData']['email'];
        $user->created = time();
        $user->password = $di->getShared('security')->hash($formEntity['settings']['formData']['password']);
        $user->active = 1;

        if ($user->create()) {
            self::createUserRoles($user->id, array('user'));
            $di->getShared('session')->set('user', $user->id);
            $di->getShared('session')->set('loginTime', time());
            return true;
        } else {
            return false;
        }
    }
    public static function saveUserRoles($uid, $roles)
    {
        $output = array();
        $userRoles = UserRoles::findByUid($uid);
        foreach ($userRoles as $role) {
            $search_role = array_search($role->role, $roles);
            if ($search_role == false) {
                $role->delete();
            } else {
                unset($roles[$search_role]);
            }
        }
        unset($role);
        foreach ($roles as $role) {
            $userRole = new UserRoles();
            $userRole->role = $role;
            $userRole->uid = $uid;
            $userRole->created = time();
            $userRole->save();
        }
    }
    public static function createUserRoles($uid, $roles)
    {
        //该函数不检测用户是否存在
        foreach ($roles as $role) {
            $userRoles = new UserRoles();
            $userRoles->role = $role;
            $userRoles->uid = $uid;
            $userRoles->created = time();
            $userRoles->save();
        }
    }
    public static function saveUser($formEntity)
    {
        global $di;
        $data = $formEntity['settings']['formData'];
        $user = false;
        //config::printCode($formEntity);
        if (isset($formEntity['settings']['id'])) {
            $user = Muser::findFirst($formEntity['settings']['id']);
        }
        if (!$user) {
            $user = new Muser();
            $user->active = 1;
            $user->created = time();
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $di->getShared('security')->hash($data['password']);
        if ($user->save()) {
            $di->getShared('flash')->success('用户保存成功');
            if (isset($data['roles'])) {
                if (is_string($data['roles'])) {
                    $data['roles'] = array($data['roles']);
                }
                if (is_array($data['roles'])) {
                    self::saveUserRoles($formEntity['settings']['id'], $data['roles']);
                }
            }
            return $user;
        } else {
            $di->getShared('flash')->error('用户保存失败');
            return false;
        }
    }
    //编辑用户
    public function editor($formEntity)
    {
        $uid = $this->user->u['id'];
        $user = UserInfo::findFirst("uid = '$uid'");
        if (!isset($user->id)) {
            $user = new UserInfo();
            $user->uid = $uid;
        }
        //print_r($formEntity['settings']['formData']);
        $user->sex = $formEntity['settings']['formData']['sex'];
        $user->face = '/images/user/default.jpg';
        $user->description = $formEntity['settings']['formData']['description'];
        $user->site_name = $formEntity['settings']['formData']['site_name'];
        $user->site = $formEntity['settings']['formData']['site'];
        $user->address = $formEntity['settings']['formData']['address'];
        if ($user->save()) {
            $this->flash->success('用户保存成功');
        } else {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }
        }
    }
    //新建用户
    public function createUser($data)
    {
        $user = new Muser();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $this->getDI()->getSecurity()->hash($data['password']);
        $user->created = time();
        if ($user->save()) {
            $this->getDI()->getFlashSession()->success('用户保存成功，正在录入角色信息...');
            if (isset($data['role']) && !empty($data['role'])) {
                $this->saveRoles($user->id, $data['role']);
            }
        } else {
            return false;
        }
    }
    //保存用户角色信息
    public function saveRoles($user, $role)
    {
        $roles = Options::get('roles');
        if (is_array($role)) {
            foreach ($role as $value) {
                if (isset($roles['data'][$value])) {
                    $this->_saveRole($user, $value);
                } else {
                    $this->getDI()->getFlashSession()->error('保存角色失败，不存在角色：' . $value);
                }
            }
        } else {
            if (isset($roles['data'][$role])) {
                $this->_saveRole($user, $role);
            } else {
                $this->getDI()->getFlashSession->error('保存角色失败，不存在角色：' . $role);
            }
        }
    }
    public function _saveRole($user, $role)
    {
        $roleModel = new UserRoles();
        $roleModel->uid = $user;
        $roleModel->role = $role;
        $roleModel->created = time();
        if ($roleModel->save()) {
            $this->getDI()->getFlashSession()->success('成功将用户载入角色：' . $role);
            return true;
        } else {
            $this->getDI()->getFlashSession->error('保存角色失败：' . $role);
            return false;
        }
    }
}