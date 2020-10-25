<?php

namespace App\Http\Requests;

class FriendLinkRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'views' => 'required|integer',
            'sort' => 'required|integer',
            'is_open' => 'boolean',
            'hash' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            "name" => '赞助商名称',
            "url" => '赞助商网站',
            'sort' => '排序',
            'views' => '浏览量',
            'is_open' => '开放浏览',
            'cover' => 'LOGO',
            "thumb" => 'LOGO微缩图',
            'hash' => 'HASH值',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute不能为空.',
            'integer' => ':attribute只能为整数.',
            'boolean' => ':attribute格式错误.',
        ];
    }
}
