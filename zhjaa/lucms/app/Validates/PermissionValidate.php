<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class  PermissionValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'name' => [
                'required',
                'between:3,50',
                'regex: /^\w+$/',
                'unique:permissions'
            ],
            'guard_name' => 'required|between:2,30',
            'description' => 'required'
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function updateValidate($request_data, $table_id = 0)
    {
        $rules = [
            'name' => [
                'required',
                'between:3,50',
                'regex: /^\w+$/',
                Rule::unique('permissions')->ignore($table_id),
            ],
            'guard_name' => 'required|between:2,30',
            'description' => 'required'
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'name.regex' => '权限名称只能由字母、数字、以及下划线（ _ ）组成',
            'name.required' => '权限名称不能为空',
            'name.between' => '权限名称只能在:min-:max个字符范围',
            'name.unique' => '权限名称已经被占用',
            'guard_name.required' => '看守噐不能为空',
            'guard_name.between' => '看守器只能在:min-:max个字符范围',
            'description.required' => '说明不能为空',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }

    public function destroyValidate($model)
    {
        $is_role_has_this_permission = DB::table('role_has_permissions')
            ->where('permission_id', $model->id)
            ->count();
        if ($is_role_has_this_permission) return $this->baseFailed('有角色在使用该权限,无法删除');
        return $this->baseSucceed($this->data, $this->message);
    }
}
