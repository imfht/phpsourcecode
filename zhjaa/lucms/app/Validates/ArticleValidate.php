<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class  ArticleValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'title' => [
                'required',
                'between:2,30',
            ],
            'category_id' => 'required|integer|min:1',
            'content' => 'required',
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function updateValidate($request_data, $table_id = 0)
    {
        $rules = [
            'title' => [
                'required',
                'between:2,30',
            ],
            'category_id' => 'required|integer|min:1',
            'content' => 'required',
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function destroyValidate($model)
    {
        return $this->baseSucceed($this->data, $this->message);
    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'title.required' => '标题不能为空',
            'name.between' => '标题称只能在:min-:max个字符范围',
            'category_id.required' => '必须选择分类',
            'category_id.integer' => '分类格式不正确',
            'category_id.min' => '必须选择分类',
            'content.required' => '必须填写内容',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }
}
