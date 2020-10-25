<?php
namespace Modules\User\Library;

use Modules\Core\Models\Log;
use Modules\User\Entity\Fields\UserFieldGold as UserGold;
use Modules\User\Entity\Fields\UserFieldScore as UserScore;
use Modules\User\Entity\User as UserModel;
use Core\Library\Email;
use Core\Config;
use Modules\Core\Models\Meta as MetaModel;

class Common
{
    //积分+1
    public static function score($uid, $score, $log = null)
    {
        $userScore = UserScore::findFirstByEid($uid);
        if (!$userScore) {
            $userScore = new UserScore();
            $userScore->value = 0;
        }
        $userScore->value += $score;
        if ($userScore->save()) {
            if (!is_null($log)) {
                $logOb = new Log();
                $logOb->id = $log['id'];
                $logOb->data = $log['notice'];
                $logOb->save();
            }
            return $userScore;
        } else {
            return false;
        }
    }

    //金币+1
    public static function gold($uid, $gold, $log = null)
    {
        global $di;
        $db = $di->getShared('db');
        $db->begin();
        $userGold = UserGold::findFirstByEid($uid);
        if (!$userGold) {
            $userGold = new UserGold();
            $userGold->value = 0;
        }
        $userGold->value += $gold;
        if (!is_null($log)) {
            $logOb = new Log();
            $logOb->id = $log['id'];
            $logOb->data = $log['notice'];
            $logOb->save();
        }
        if ($userGold->save() && $logOb->save()) {
            $db->commit();
            return $userGold;
        } else {
            $db->rollback();
            return false;
        }
    }

    //转换
    public static function idToName($id)
    {
        $user = UserModel::findFirst($id);
        if ($user) {
            return $user->name;
        }
        return '';
    }

    //检测用户是否存在，并发送重置密码邮件
    public static function existUserAndSend($value)
    {
        global $di;
        $coreConfig = Config::get('m.core.config');
        $user = false;
        if (preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', $data['user'])) {
            //$userType = 'email';
            $user = UserModel::findFirstByEmail($value);
        } elseif (preg_match("/^1[34578]\d{9}$/", $value)) {
            //$userType = 'tel';
            $di->getShared('flash')->error('暂不支持通过该方法找回密码');
            return false;
            $user = UserModel::findFirstByPhone($value);
            if (!$user) {
                $user = UserModel::findFirstByName($value);
            }
        } else {
            $user = UserModel::findFirstByName($value);
        }
        if (!isset($user->email) || !$user->email) {
            $di->getShared('flash')->error('该账户不存在，或者注册时没有填写正确的邮箱地址');
            return false;
        }
        do {
            $saltCode = randomString(6);
            $existCode = MetaModel::findFirstById('emailSalt' . $saltCode);
        } while (!$existCode);
        $sendInfo = [
            'title' => '【' . $coreConfig['name'] . '】' . $user->name . '，您正在找回密码',
            'body' => '您的验证码：' . $saltCode,
        ];
        $metaModel = new MetaModel();
        $metaModel->id = 'emailSalt' . $saltCode;
        $metaModel->data = serialize([
            'time' => time(),
            'salt' => $saltCode,
            'user' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone
        ]);
        if ($metaModel->save()) {
            $emailSend = Email::send($user->email, $user->name, $sendInfo['title'], $sendInfo['body'], false);
            if ($emailSend['state'] == true) {
                $di->getShared('flash')->success('已发送验证码到您的邮件：' . hiddenString($user->email) . '，二十四小时内有效，请注意查收');
                return true;
            } else {
                $di->getShared('flash')->error('验证邮件发送失败，请稍后再试');
                return false;
            }
        } else {
            return false;
        }
    }

    //重置密码
    public static function resetPassword($form)
    {
        global $di;
        $formData = $form->getData();
        $metaModel = MetaModel::findFirstById('emailSalt' . $formData['salt']);
        if (!$metaModel) {
            $di->getShared('flash')->error('信息不存在');
            return false;
        }
        $data = unserialize($metaModel->data);
        if ($formData['email'] == $data['email'] && $formData['salt'] = $data['salt']) {
            if ($data['time'] + 86400 < time()) {
                $metaModel->delete();
                $di->getShared('flash')->error('验证码已失效，请重新操作。');
                return false;
            }
            $user = UserModel::findFirst($data->user);
            $user->password = $di->getShared('security')->hash($formData['password']);
            if ($user->save()) {
                $metaModel->delete();
                $di->getShared('flash')->error('密码修改成功，您现在可以使用新密码登录了。');
                return true;
            } else {
                $di->getShared('flash')->error('密码修改失败，请稍后重试。');
                return false;
            }
        } else {
            $di->getShared('flash')->error('您输入的验证码或者邮箱错误。');
            return false;
        }
    }
    //以下内容，暂未清理
    //注册
    public static function register($form)
    {
        global $di;
        $data = $form->getData();
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->password = $di->getShared('security')->hash($data['password']);
        $user->active = 1;
        $user->email_validate = 1;
        $user->phone_validate = 1;
        if ($user->save()) {
            return $user;
        }
        return false;

    }

    //登陆
    public static function login($form)
    {
        global $di;
        $data = $form->getData();
        if (strpos($data['user'], '@')) {
            $user = UserModel::findFirstByEmail($data['user']);
        } else {
            $user = UserModel::findFirstByPhone($data['user']);
        }
        if (!$user) {
            return false;
        }
        if ($di->getShared('security')->checkHash($data['password'], $user->password)) {
            if ($di->getShared('user')->login($user->id) === true) {
                return $user;
            }
            return false;
        } else {
            return false;
        }
    }

    public static function userRoles($uid)
    {
        $userRoles = UserRoles::findByUid($uid);
        $userRolesArr = array();
        foreach ($userRoles as $ur) {
            $userRolesArr[$ur->role] = $ur->role;
        }
        return $userRolesArr;
    }

    public static function userDelete($uid)
    {
        global $di;
        $db = $di->getShared('db');
        $db->begin();
        $user = UserModel::findFirst($uid);
        if (!$user) {
            return false;
        }
        if ($user->delete()) {
            $userRoles = UserRoles::findByUid($uid);
            $number = 0;
            foreach ($userRoles as $ur) {
                if ($ur->delete()) {
                    $number++;
                }
            }
            if (count($userRoles) == $number) {
                $db->commit();
                return true;
            }
        }
        $db->rollback();
        return false;
    }
}
