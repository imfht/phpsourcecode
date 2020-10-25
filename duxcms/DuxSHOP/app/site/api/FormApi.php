<?php

/**
 * 自定义表单
 */

namespace app\site\api;

use \app\base\api\BaseApi;

class FormApi extends BaseApi {

    public function index() {
        target('site/Form', 'middle')->setParams([
            'id' => $this->data['id'],
            'limit' => $this->data['limit'],
        ])->data()->export(function ($data) {
            if(!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($data['pageLimit'], $data['pageList'], $data['pageData']),
                ]);
            }else {
                $this->error('暂无更多记录', 404);
            }
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function info() {
        target('site/FormInfo', 'middle')->setParams([
            'id' => $this->data['id'],
            'form_id' => $this->data['form_id'],
        ])->data()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function submit() {
        target('site/Form', 'middle')->setParams($this->data)->post()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}