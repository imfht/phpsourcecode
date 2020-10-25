<?php

namespace App\Http\Requests;

class PersonRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'sort' => 'required|integer',
            'point' => 'required|integer',
            'age' => 'required|integer',
            'is_recommend' => 'boolean',
            'is_show' => 'boolean',
            'sex' => 'boolean',
        ];
    }

    public function attributes()
    {
        return [
            "name" => '姓名',
            "title" => '头衔',
            "sex" => '性别',
            'sort' => '排序',
            "point" => '贡献度',
            "age" => '从业时间',
            "tag" => '特长',
            'is_recommend' => '是否推荐',
            'is_show' => '是否显示',
            "head" => '头像',
            "head_thumbnail" => '头像微缩图',
            "url" => '外链网址',
            'keywords' => 'seo关键字',
            'description' => 'seo描述',
            "info" => '简介',
            'text' => '详情',
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
