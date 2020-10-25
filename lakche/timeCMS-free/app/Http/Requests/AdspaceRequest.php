<?php

namespace App\Http\Requests;

class AdspaceRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'is_open' => 'boolean',
            'hash' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            "name" => '名称',
            'is_open' => '启用状态',
            'hash' => 'HASH值',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute不能为空.',
            'integer' => ':attribute只能为整数.',
            'boolean' => ':attribute格式错误.',
            'in' => ':attribute不存在.',
        ];
    }
}
