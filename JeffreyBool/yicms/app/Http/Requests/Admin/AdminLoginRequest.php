<?php

namespace App\Http\Requests\Admin;

use App\Rules\AdminLoginRule;
use App\Repositories\AdminsRepository;
use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{


    protected $adminsRepository;

    /**
     * AdminLoginRequest constructor.
     * @param AdminsRepository $adminsRepository
     */
    public function __construct(AdminsRepository $adminsRepository)
    {
        $this->adminsRepository = $adminsRepository;
    }

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
            'name'              => 'required',
            'password'          => ['required', new AdminLoginRule($this->adminsRepository, \Request::get('name'))],
            'geetest_challenge' => 'geetest'
        ];
    }

    /**
     * 提示信息s
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'     => '管理员名称不能为空',
            'password.required' => '密码不能为空',
            'geetest'           => '验证码校验失败',
        ];
    }
}
