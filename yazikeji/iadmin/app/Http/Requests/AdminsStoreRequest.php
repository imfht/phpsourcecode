<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'                 => 'required|email|unique:sys_admins',
            'nickname'              => 'required|unique:sys_admins|regex:/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]+$/u',
            'active'                => 'required|boolean',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required',
            'role'                  => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'email.required'                    => '邮箱不能为空',
            'nickname.required'                 => '昵称不能为空',
            'active.required'                   => '状态不能为空',
            'password.required'                 => '密码不能为空',
            'password_confirmation.required'    => '确认密码不能为空',
            'role.required'                     => '必须为用户分配角色',
            'email.email'                       => '邮箱格式不正确',
            'email.unique'                      => '邮箱账号已经存在',
            'nickname.unique'                   => '昵称已经存在',
            'nickname.regex'                    => '昵称只允许包含字母、数字、中文',
            'active.boolean'                    => '状态字段只允许bool类型',
            'password.confirmed'                => '确认密码输入不正确',
            'role.numeric'                      => '角色只允许数字'
        ];
    }
}
