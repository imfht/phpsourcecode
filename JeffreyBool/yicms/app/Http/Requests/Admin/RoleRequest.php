<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => 'required|between:3,10',
            'remark' => 'max:300',
            'order'  => 'required:integer',
            'status' => 'required:integer',
        ];
    }

    public function messages()
    {
        return [
            'name.required'   => '角色名称不能为空',
            'name.between'    => '角色名称长度应该在3~10位之间',
            'remark.max'      => '角色描述不能超过300个字符',
            'order.required'  => '排序不能为空',
            'order.integer'   => '表单不合法',
            'status.required' => '状态不能为空',
            'status.integer'  => '表单不合法',
        ];
    }
}
