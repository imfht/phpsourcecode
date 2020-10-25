<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Controllers\AuthController as BaseAuthController;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseAuthController
{
    protected $view = 'login';

    /**
     * Update user setting.
     *
     * @return Response
     */
    public function putSetting()
    {
        $form = $this->settingForm();

        if (config('admin.demo')) {
            return $form->error('演示模式下不允许此修改操作');
        }

        if (!$this->validateCredentialsWhenUpdatingPassword()) {
            $form->responseValidationMessages('old_password', trans('admin.old_password_error'));
        }

        return $form->update(Admin::user()->getKey());
    }
}
