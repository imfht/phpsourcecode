<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
                    'name'=>'required|unique:users|min:2|max:30',
                    'email'=>'required|unique:users|email',
                    'password'=>'confirmed|required|min:6'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name'=>'required|unique:users,name,'.$this->input('name').',name|min:2|max:30',
                    'email'=>'required|unique:users,email,'.$this->input('email').',email|min:2|max:60',
                    'password'=>'confirmed|min:6'
                ];
            }
            default:break;
        }
    }

    public function attributes()
    {
        return [
            'name'=>'用户名',
            'email'=>'电子邮件',
            'password'=>'密码'
        ];
    }
}
