<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RuleRequest extends FormRequest
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
            'parent_id' => 'integer',
            'name'      => 'required|between:1,20',
            'fonts'     => 'max:128',
            'route'     => 'max:256',
            'sort'      => 'required:integer',
            'is_hidden' => 'required:integer',
            'status'    => 'required:integer',
        ];
    }

    public function messages()
    {
        return [
            'parent_id.integer'  => '表单不合法',
            'name.required'      => '权限名称不能为空',
            'name.between'       => '权限名称长度应该在1~20位之间',
            'fonts.max'          => '菜单图标不能超过128个字符',
            'route.max'          => '权限路由不能超过256个字符',
            'sort.required'      => '排序不能为空',
            'sort.integer'       => '表单不合法',
            'is_hidden.required' => '是否隐藏选项不能为空哦',
            'is_hidden.integer'  => '表单不合法',
            'status.required'    => '状态不能为空',
            'status.integer'     => '表单不合法',
        ];
    }
}
