<?php
namespace Modules\user\Library;

use Core\Config;
use Modules\User\Models\User as Muser;
use Modules\User\Models\UserRoles;
use Phalcon\Exception;

class Form{
    public static function saveUser($form){
        global $di;
        $db = $di->getShared('db');
        $formEntity = $form->formEntity;
        $data = $form->getData();
        $user = false;
        if(!isset($data['roles'])){
            $data['roles'] = array('user');
        }
        //config::printCode($formEntity);
        $db->begin();
        if (isset($formEntity['settings']['id'])) {
            $user = Muser::findFirst($formEntity['settings']['id']);
            if($data['password']){
                $user->password = $di->getShared('security')->hash($data['password']);
            }
        }
        if (!$user) {
            $user = new User();
            $user->active = 1;
            $user->created = time();
            $user->password = $di->getShared('security')->hash($data['password']);
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        echo '保存之前';
        if($user->save()){
            echo '保存之后';
            if(self::saveUserRoles($user->id,$data['roles'])){
                echo '角色';
                $db->commit();
                return $user;
            }
        }
        $db->rollback();
        return false;
    }
    public static function login($form){
        global $di;
        $data = $form->getData();
        if(strpos($data['user'],'@')){
            $user = Muser::findFirstByEmail($data['user']);
        }else{
            $user = Muser::findFirstByPhone($data['user']);
        }
        if($di->getShared('security')->checkHash($data['password'],$user->password)){
            echo 'sss';
            $di->getShared('session')->set('user', $user->id);
            $di->getShared('session')->set('loginTime', time());
            return $user;
        }else{
            return false;
        }
    }
    public static function saveUserRoles($uid,$roles){
        if(!is_array($roles)){
            return false;
        }
        $rolesList = Config::get('m.user.roles');
        $userRoles = UserRoles::findByUid($uid);
        foreach($userRoles as $ur){
            if(!in_array($ur->role,$roles)){
                $ur->delete();
            }else{
                $key = array_search($ur->role,$roles);
                unset($roles[$key]);
            }
        }
        $number = 0;
        foreach($roles as $role){
            if(isset($rolesList[$role])){
                $roleModel = new UserRoles();
                $roleModel->uid = $uid;
                $roleModel->role = $role;
                $roleModel->created = time();
                if($roleModel->save()){
                    $number++;
                }
            }
        }
        if(count($roles) == $number){
            return true;
        }
        return false;
    }
}