<?php

namespace App\Http\Requests;

class PageRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'url' => 'required',
            'views' => 'required|integer',
            'view' => 'required_without_all:openurl',
            'is_open' => 'boolean',
            'hash' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            "url" => '访问路径',
            'view' => '对应模板',
            'openurl' => '外链网址',
            'views' => '浏览量',
            'is_open' => '开放浏览',
            'cover' => '封面',
            "thumb" => '封面微缩图',
            'hash' => 'HASH值',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute不能为空.',
            'integer' => ':attribute只能为整数.',
            'required_without_all' => '对应模板和外链网址必选一项填写',
            'boolean' => ':attribute格式错误.',
            'unique' => ':attribute已存在.',
        ];
    }
}
