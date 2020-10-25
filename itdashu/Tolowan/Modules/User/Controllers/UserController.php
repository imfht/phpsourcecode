<?php
namespace Modules\User\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Modules\Core\Models\Log;
use Modules\User\Library\Common;
use Modules\User\Library\CropAvatar;
use Modules\User\Entity\Fields\UserFieldFace as UserFace;

class UserController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $userEntity = $this->entityManager->get('user');
        $user = $userEntity->findFirst($this->user->id,true);
        $userInfo = $userEntity->findFirst($this->user->id);
        $userCenterForm = $this->form->create('user.baseInfoForm',$userInfo);
        $this->variables += [
            '#templates' => array(
                'pageUserCenter',
                'pageUserCenterIndex',
            ),
            'title' => '用户中心-个人资料',
            'description' => '用户中心-个人资料',
            'key' => '用户中心',
            'userInfo' => $user,
            'content' => array(
                'data' => $userCenterForm->renderForm(),
            ),
        ];
    }

    public function signAction()
    {
        extract($this->variables['router_params']);
        $userConfig = Config::get('m.user.config');
        $this->variables['#templates'] = 'json';
        $uid = $this->user->id;
        $logId = 'userSign' . $uid;
        $userSign = Log::findFirstById($logId);
        if ($userSign && date('Ymd', time()) == date('Ymd', $userSign->created)) {
            $data = array(
                'state' => true,
                'notice' => '亲，您今天已经签到过了～',
            );
        } else {
            $notice = '签到成功，积分 <span class="red">+' . $userConfig['signScore'] . '</span>';
            if (Common::score($uid, 1, array('id' => $logId, 'notice' => $notice))) {
                $data = array(
                    'state' => true,
                    'notice' => $notice,
                );
            } else {
                $data = array(
                    'state' => true,
                    'notice' => '签到失败，请稍后再试',
                );
            }
        }
        $this->variables['data'] = $data;
    }

    public function cropFaceAction()
    {
        extract($this->variables['router_params']);
        $crop = new CropAvatar(
            md5($this->user->id),
            isset($_POST['avatar_src']) ? $_POST['avatar_src'] : null,
            isset($_POST['avatar_data']) ? $_POST['avatar_data'] : null,
            isset($_FILES['avatar_file']) ? $_FILES['avatar_file'] : null
        );

        $response = array(
            'state' => 200,
            'message' => $crop->getMsg(),
            'result' => $crop->getResult(),
        );
        if (empty($crop->getMsg())) {
            $userInfo = UserFace::findFirstByEid($this->user->id);
            if (!$userInfo) {
                $userInfo = new UserFace();
                $userInfo->eid = $this->user->id;
                //return $this->notFount();
            }
            $userInfo->value = $crop->getResult();
            if ($userInfo->save()) {
                $this->user->updateCookie('face', $userInfo->value);
            }
        }
        $this->variables += array(
            '#templates' => 'json',
            'data' => $response,
        );
    }

    public function logoutAction()
    {
        extract($this->variables['router_params']);
        $this->user->logout();
        return $this->moved();
    }

    public function checkEmailAction()
    {
        extract($this->variables['router_params']);
        $this->variables['keywords'] = $this->variables['description'] = $this->variables['title'] = '验证邮箱';
        $passwordForm = Config::get('user.checkEmail');
        $passwordForm = $this->form->create($passwordForm);
        $userEntity = $this->entityManager->get('user');
        $user = $userEntity->findFirst($this->user->id, true);
        $this->variables += array(
            '#templates' => array(
                'pageUserCenter',
                'pageUserCenterCheckEmail',
            ),
            'userInfo' => $user,
            'content' => array(
                'data' => $passwordForm->renderForm(),
            ),
        );
    }

    public function sendEmail()
    {
        extract($this->variables['router_params']);
    }

    public function passwordAction()
    {
        extract($this->variables['router_params']);
        $this->variables['keywords'] = $this->variables['description'] = $this->variables['title'] = '修改密码';
        $passwordForm = Config::get('user.passwordForm');
        $passwordForm = $this->form->create($passwordForm);
        $userEntity = $this->entityManager->get('user');
        $user = $userEntity->findFirst($this->user->id, true);
        if ($passwordForm->isValid()) {
            if ($passwordForm) {
                $this->user->logout();
                return $this->moved(array(
                    'for' => 'login',
                ));
            }
        }
        $this->variables += array(
            '#templates' => array(
                'pageUserCenter',
                'pageUserCenterPassword',
            ),
            'userInfo' => $user,
            'content' => array(
                'data' => $passwordForm->renderForm(),
            ),
        );
    }
}
