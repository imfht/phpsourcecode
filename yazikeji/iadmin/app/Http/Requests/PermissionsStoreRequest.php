<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionsStoreRequest extends FormRequest
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
            'name'          => 'required|unique:sys_permissions',
            'display_name'  => 'required',
            'pid'           => 'regex:/[0-9]+/',
            'sort'          => 'regex:/[0-9]+/'
        ];
    }

    public function messages()
    {
        return [
            'name.required'             => '权限标识不能为空',
            'name.unique'               => '权限标识不能重复',
            'display_name.required'     => '权限名称不能为空',
            'pid.regex'                 => '父类ID只能是数字',
            'sort.regex'                => '排序数值只能是数字'
        ];
    }
}
