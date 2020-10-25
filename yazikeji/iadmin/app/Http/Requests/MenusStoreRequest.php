<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenusStoreRequest extends FormRequest
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
            'name' => 'required|unique:sys_menus',
            'display_name' => 'required',
            'uri' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '控制器标识不允许重复',
            'name.required' => '控制器标识不允许为空',
            'display_name.required' => '菜单名称不允许为空',
            'uri.required' => '路由地址不允许为空'
        ];
    }
}
