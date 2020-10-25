<?php

namespace App\Http\Requests;

class AttachmentRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'file' => 'required_without:upfile',
            'class' => 'required|in:category,article,page,person,project,user,system,friendLink,apimages',
            'type' => 'required|in:image,cover,file',
            'hash' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            "file" => '上传文件',
            'class' => '上传文件分类',
            'type' => '上传文件所属',
            'hash' => 'HASH值',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute不能为空.',
            'in' => ':attribute不在可选范围内.',
        ];
    }
}
