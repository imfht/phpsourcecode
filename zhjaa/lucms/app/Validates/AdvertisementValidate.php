<?php

namespace App\Validates;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use DB;

class  AdvertisementValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function storeValidate($request_data)
    {
        $rules = [
            'name' => [
                'required',
                'between:2,50',
                'unique:advertisements'
            ],
            'content' => 'required',
            'advertisement_positions_id' => 'required|integer|min:1',
            'link_url' => 'required|url',
//            'cover_image' => 'required|integer|min:1'
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function updateValidate($request_data, $advertisemet_id = 0)
    {
        if (!$advertisemet_id) return $this->baseFailed('数据不存在');
        $rules = [
            'name' => [
                'required',
                'between:2,50',
                Rule::unique('advertisements')->ignore($advertisemet_id),
            ],
            'content' => 'required',
            'advertisement_positions_id' => 'required|integer|min:1',
            'link_url' => 'required|url',
//            'cover_image' => 'required|integer|min:1'
        ];
        $rest_validate = $this->validate($request_data, $rules);
        if ($rest_validate === true) {
            return $this->baseSucceed($this->data, $this->message);
        } else {
            $this->message = $rest_validate;
            return $this->baseFailed($this->message);
        }

    }

    public function destroyValidate($advertisement)
    {
        return $this->baseSucceed($this->data, $this->message);
    }

    protected function validate($request_data, $rules)
    {
        $message = [
            'name.required' => '广告标题不能为空',
            'name.between' => '广告标题称只能在:min-:max个字符范围',
            'name.unique' => '广告标题已经被占用',
            'advertisement_positions_id.required' => '必须选择广告位',
            'advertisement_positions_id.integer' => '广告位格式不正确',
            'advertisement_positions_id.min' => '必须选择广告位',
            'cover_image.required' => '必须上传封面图片',
            'cover_image.integer' => '封面图片格式不正确',
            'cover_image.min' => '必须上传封面图片',
            'link_url.required' => '必须填写跳转链接',
            'link_url.url' => '跳转链接格式不正确',
        ];
        $validator = Validator::make($request_data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }


}
