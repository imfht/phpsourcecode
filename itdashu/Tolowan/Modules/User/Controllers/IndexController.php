<?php
namespace Modules\User\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Modules\User\Library\Common;
use Modules\User\Entity\User as UserModel;

class IndexController extends Controller
{
    public function indexAction()
    {
        extract($this->variables['router_params']);
        $userEntity = $this->entityManager->get('user');
        $user = $userEntity->findFirst($id, true);
        if (!$user) {
            return $this->getNotFount();
        }
        $this->variables['title'] = $user->name . ' 的主页';
        $this->variables['description'] = $user->name . ' 的主页';
        $this->variables['keywords'] = $user->name;
        $this->variables += [
            '#templates' => 'pageUser',
            'data' => $user,
        ];
    }

    public function remoteAction()
    {
        extract($this->variables['router_params']);
        $this->variables['page'] = [
            '#templates' => 'remote',
        ];
        $valid = false;
        $this->response->setContentType('application/json', 'UTF-8');
        $username = $this->request->getPost('username');
        $user = UserModel::findFirstByName($username);
        if ($user) {
            $valid = false;
        } else {
            $valid = true;
        }
        echo json_encode(['valid' => $valid]);
    }

    public function loginAction()
    {
        extract($this->variables['router_params']);
        $this->variables['keywords'] = $this->variables['description'] = $this->variables['title'] = '用户登陆';
        $loginForm = Config::get('user.loginForm');
        $loginForm = $this->form->create($loginForm);
        if ($loginForm->isValid()) {
            $state = $loginForm->save();
            if ($state !== false) {
                return $this->temMoved();
            }
        }
        if ($this->user->isLogin() === true) {
            $this->flash->success('登陆，更换账户请退出后登陆');
            return $this->temMoved(['for' => 'index']);
        }
        $this->variables += [
            '#templates' => 'login',
            'data' => $loginForm->renderForm(),
        ];
    }

    public function logoutAction()
    {
        extract($this->variables['router_params']);
        $this->user->logout();
        if ($this->request->has('rd') && $this->request->get('rd') != $this->variables['url']) {
            $url = $this->request->get('rd');
        } else {
            $url = $this->url->get(['for' => 'index']);
        }
        return $this->temMoved($url);
    }

    public function checkEmailAction()
    {
        extract($this->variables['router_params']);
        $this->variables['keywords'] = $this->variables['description'] = $this->variables['title'] = '确认邮箱';
        $passwordForm = Config::get('user.checkEmail');
        $passwordForm = $this->form->create($passwordForm);
        $this->variables['page'] = [
            '#templates' => 'page',
        ];
        $this->variables['page']['loginForm'] = $passwordForm;
    }

    public function passwordAction()
    {
        extract($this->variables['router_params']);
        $this->variables['keywords'] = $this->variables['description'] = $this->variables['title'] = '找回密码';
        $passwordForm = Config::get('user.passwordForm');
        $passwordForm = $this->form->create($passwordForm);
        if ($passwordForm->isValid()) {
            //发送邮件
            $this->temMoved(['for' => 'resetPassword']);
        }
        $this->variables += [
            '#templates' => 'password',
        ];
        $this->variables['data'] = $passwordForm->renderForm();
    }

    public function resetPasswordAction()
    {
        extract($this->variables['router_params']);
        $this->variables['keywords'] = $this->variables['description'] = $this->variables['title'] = '找回密码';
        $passwordForm = Config::get('user.resetPasswordForm');
        $passwordForm = $this->form->create($passwordForm);
        if ($passwordForm->isValid()) {
            //发送邮件
            $passwordForm->save();
        }
        $this->variables += [
            '#templates' => 'resetPassword',
        ];
        $this->variables['data'] = $passwordForm->renderForm();
    }

    public function registerAction()
    {
        extract($this->variables['router_params']);
        $userEntity = $this->entityManager->get('user');
        $userAddForm = $userEntity->addForm('user');
        $this->variables['#templates'] = 'register';
        $this->variables['keywords'] = $this->variables['description'] = $this->variables['title'] = '用户注册';

        $this->variables['title'] = '用户注册';
        $this->variables['description'] = '用户注册';
        $this->variables['data'] = $userAddForm->renderForm();
    }
}
