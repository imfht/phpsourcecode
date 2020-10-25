<?php

namespace App\Http\Requests;

class ProjectRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'category_id' => 'required|integer|exists:categories,id',
            'sort' => 'required|integer',
            'views' => 'required|integer',
            'cost' => 'required|numeric',
            'period' => 'required|integer',
            'is_recommend' => 'boolean',
            'is_show' => 'boolean',
        ];
    }

    public function attributes()
    {
        return [
            "title" => '项目名称',
            'category_id' => '项目分类',
            'sort' => '项目排序',
            'views' => '浏览量',
            "tag" => '项目标签',
            'is_recommend' => '是否推荐',
            'is_show' => '是否显示',
            "cover" => '封面图',
            "thumb" => '封面微缩图',
            "cost" => '项目费用',
            "period" => '项目周期',
            "person_id" => '参与人员',
            "info" => '项目简介',
            "url" => '外链网址',
            'keywords' => 'seo关键字',
            'description' => 'seo描述',
            'text' => '项目详情',
            'time' => '进度时间',
            'event' => '进度事件',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute不能为空.',
            'integer' => ':attribute只能为整数.',
            'exists' => ':attribute不存在.',
            'numeric' => ':attribute只能为数字.',
            'boolean' => ':attribute格式错误.',
        ];
    }
}
