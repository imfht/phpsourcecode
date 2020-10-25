<?php

namespace App\Http\Requests;

class AdimageRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'adspace_id' => 'required|integer|exists:adspaces,id',
            'views' => 'required|integer',
            'sort' => 'required|integer',
            'is_open' => 'boolean',
            'hash' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            "name" => '广告名称',
            "adspace_id" => '广告位置',
            "url" => '广告网址',
            'sort' => '排序',
            'views' => '浏览量',
            'is_open' => '开放浏览',
            'cover' => '广告图片',
            "thumb" => '图片微缩图',
            'hash' => 'HASH值',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute不能为空.',
            'integer' => ':attribute只能为整数.',
            'boolean' => ':attribute格式错误.',
            'exists' => ':attribute不存在.',
        ];
    }
}
