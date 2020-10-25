<?php

namespace App\Http\Requests;

class CategoryRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $rules = [
            'title' => 'required',
            'sort' => 'integer',
            'is_nav_show' => 'boolean',
            'hash' => 'required',
        ];

        if(Request::get('parent_id') != ''){
            $rules[] = ['parent_id' => 'exists:categories,id'];
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            "title" => '栏目标题',
            'sort' => '栏目排序',
            'info' => '栏目简介',
            'parent_id' => '上级栏目',
            'cover' => '栏目封面',
            'thumb' => '栏目封面微缩图',
            'is_nav_show' => '导航显示',
            'keywords' => 'seo关键字',
            'description' => 'seo描述',
            'templet_all' => '带子分类模板',
            'templet_nosub' => '不带子分类模板',
            'templet_article' => '文章模板',
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
