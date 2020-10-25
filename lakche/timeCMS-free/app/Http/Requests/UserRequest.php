<?php

namespace App\Http\Requests;

class UserRequest extends Request
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return  [
            'attr' => 'required|in:admin,email,tel',
        ];
    }

    public function attributes()
    {
        return [
            "attr" => '属性',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute不能为空.',
            'in' => ':attribute不可修改.',
        ];
    }
}
