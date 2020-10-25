<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;

class  SystemVersionValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'title' => 'required|between:2,20',
            'version' => 'required|unique:system_versions',
            'content' => 'required|min:3'
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'title.required' => '标题不能为空',
            'title.between' => '标题只能在:min-:max个字符范围',
            'version.required' => '请填写版本信息',
            'version.unique' => '版本已经存在',
            'content.min' => '版本描述至少:min个字符',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }
}
