<?php

namespace App\Validates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class  AppVersionValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'port' => 'required',
            'system' => 'required',
            'version_sn' => 'between:2,20|unique:app_versions',
            'version_intro' => 'between:2,200',
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function updateValidate($request_data, $version_id = 0)
    {
        $rules = [
            'port' => 'required',
            'system' => 'required',
            'version_sn' => [
                'between:2,20',
                Rule::unique('app_versions')->ignore($version_id),
            ],
            'version_intro' => 'between:2,200',
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function destroyValidate()
    {
        return $this->baseSucceed($this->data, $this->message);
    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'port.required' => '请选择 app',
            'system.required' => '请选择 app',
            'version_sn.between' => '版本号只能在:min-:max个字符范围',
            'version_sn.unique' => '版本已经发布过了',
            'version_intro.between' => '版本描述只能在:min-:max个字符范围',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }
}
