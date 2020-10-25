<?php

namespace App\Http\Requests;

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

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'name'=>'required|unique:roles|min:2|max:20',
                    'display_name'=>'required|min:2|max:60',
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name'=>'required|unique:roles,name,'.$this->input('name').',name|min:2|max:20',
                    'display_name'=>'required|min:2|max:60',
                ];
            }
            default:break;
        }
    }

    public function attributes()
    {
        return [
            'name'=>'角色名称',
            'display_name'=>'显示名称',
            'description'=>'描述'
        ];
    }
}
