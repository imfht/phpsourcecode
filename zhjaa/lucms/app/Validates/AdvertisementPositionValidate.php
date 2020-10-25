<?php

namespace App\Validates;

use App\Models\Advertisement;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use DB;

class  AdvertisementPositionValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'name' => [
                'required',
                'between:2,50',
                'unique:advertisement_positions'
            ],
            'type' => [
                Rule::in(['default', 'model', 'spa']),
            ]
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
            'name' => [
                'required',
                'between:2,50',
                Rule::unique('advertisement_positions')->ignore($table_id),
            ],
            'type' => [
                Rule::in(['default', 'model', 'spa']),
            ]
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
            'name.required' => '广告位名称不能为空',
            'name.between' => '广告位名称只能在:min-:max个字符范围',
            'name.unique' => '广告位名称已经被占用',
            'type.in' => '广告位类型不正确',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }

    public function destroyValidate($model)
    {
        $is_advertisement_has_this_position = Advertisement::where('advertisement_positions_id', $model->id)->count();
        if ($is_advertisement_has_this_position) return $this->baseFailed('有广告正在使用该广告位，不允许删除');
        return $this->baseSucceed($this->data, $this->message);
    }
}
