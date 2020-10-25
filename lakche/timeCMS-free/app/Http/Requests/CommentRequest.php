<?php

namespace App\Http\Requests;

class CommentRequest extends Request
{
  public function authorize()
  {
    return auth()->check();
  }

  public function rules()
  {
    return [
        'article_id' => 'required|integer|exists:articles,id',
        'user_id' => 'integer|exists:users,id',
        'comment_id' => 'integer|exists:comments,id',
        'name' => 'required',
        'phone' => 'required',
        'info' => 'required',
        'is_open' => 'boolean',
        'is_show' => 'boolean',
    ];
  }

  public function attributes()
  {
    return [
        "article_id" => '文章',
        'user_id' => '用户',
        'comment_id' => '留言',
        'name' => '姓名',
        'phone' => '联系方式',
        'is_show' => '是否显示',
        'is_open' => '是否审核',
        "info" => '留言内容',
        "hash" => 'hash',
        "ip" => 'ip',
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
