<?php

namespace App\Http\Requests;

class ArticleRequest extends Request
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
        'is_recommend' => 'boolean',
        'is_top' => 'boolean',
        'is_show' => 'boolean',
    ];
  }

  public function attributes()
  {
    return [
        "title" => '文章标题',
        'category_id' => '文章分类',
        'sort' => '文章排序',
        'views' => '浏览量',
        'is_recommend' => '是否推荐',
        'is_top' => '是否置顶',
        'is_show' => '是否显示',
        "info" => '文章简介',
        "tag" => '文章标签',
        "url" => '外链网址',
        "cover" => '封面图',
        "thumb" => '封面微缩图',
        'text' => '文章详情',
        'subtitle' => '副标题',
        'author' => '文章作者',
        'source' => '文章来源',
        'keywords' => 'seo关键字',
        'description' => 'seo描述',
    ];
  }

  public function messages()
  {
    return [
        'required' => ':attribute不能为空.',
        'integer' => ':attribute只能为整数.',
        'exists' => ':attribute不存在.',
        'boolean' => ':attribute格式错误.',
    ];
  }
}
