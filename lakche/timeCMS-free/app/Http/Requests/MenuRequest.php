<?php

namespace App\Http\Requests;

class MenuRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'url' => 'required',
            'position' => 'required|integer|in:0,1,2',
            'sort' => 'required|integer',
            'is_open' => 'boolean',
            'hash' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            "name" => '菜单名称',
            "url" => '菜单网址',
            'sort' => '排序',
            'position' => '位置',
            'is_open' => '开放浏览',
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
