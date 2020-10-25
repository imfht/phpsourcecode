<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AddBookRequest extends Request
{

    /**
     * @var array
     */
    protected $rules = [
            'title'  => 'required'
    ];

    /**
     * @var array
     */
    protected $messages = [
            'title.required' => '必须填写“笔记本名称”'
    ];
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
        return $this->rules;
    }

    /**
     * @return array
     */
    public function messages(){
        return $this->messages;
    }
}
