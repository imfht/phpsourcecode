<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class  SystemConfigValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'flag' => [
                'regex:/^[a-z][a-zA-Z0-9_]{2,100}$/',
                'unique:system_configs'
            ],
            'title' => 'between:2,100|unique:system_configs',
            'system_config_group' => 'required'
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }
    }


    public function updateValidate($request_data, $table_id)
    {
        $rules = [
            'flag' => [
                'regex:/^[a-z][a-zA-Z0-9_]{2,100}$/',
                Rule::unique('system_configs')->ignore($table_id)
            ],
            'title' => [
                'between:2,100',
                Rule::unique('system_configs')->ignore($table_id)
            ],
            'system_config_group' => 'required'
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
            'flag.regex' => '标识只能是2-100位的字母、数字、下划线组成',
            'flag.unique' => '标识已经存在',
            'title.between' => '配置标题只能在:min-:max个字符范围',
            'title.unique' => '配置标题已经被占用',
            'system_config_group.required' => '请选择配置分组'
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }
}
