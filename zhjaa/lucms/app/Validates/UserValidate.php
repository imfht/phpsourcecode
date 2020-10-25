<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;

class  UserValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];


    public function storeValidate($request_data)
    {
        $rules = [
            'name' => 'required|between:3,50',
            'password' => 'between:6,12|alpha_num|confirmed',
            'email' => 'email|unique:users'
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function updateValidate($request_data)
    {
        $rules = [
            'name' => 'required|between:3,50',
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function destroyValidate($model)
    {
        if ($model->id === 1) return $this->baseFailed('不允许删除最高管理员');
        return $this->baseSucceed();
    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'name.required' => '昵称不能为空',
            'name.between' => '昵称只能在:min-:max个字符范围',
            'password.between' => '密码只能在:min-:max个字符范围',
            'password.alpha_num' => '密码只能是数字跟字母',
            'password.confirmed' => '两次输入密码不一致',
            'email.email' => '请输入正确的邮箱格式',
            'email.unique' => '邮箱已经被占用',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }
}
