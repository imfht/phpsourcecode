<?php

namespace App\Validates;

use App\Traits\RedisTrait;
use Illuminate\Support\Facades\Validator;
use DB;

class  ApiMessageValidate extends Validate
{
    use RedisTrait;
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'title' => 'between:2,30|unique:api_messages',
            'content' => 'between:2,500',
            'type' => 'required',
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed();
        } else {
            return $this->baseFailed($this->message);
        }

    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'title.between' => '标题只能在:min-:max个字符范围',
            'title.unique' => '消息已经发送过了',
            'type.required' => '请选择消息类型',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            $this->message = $validator->errors()->first();;
            return false;
        }
        return true;
    }
}
