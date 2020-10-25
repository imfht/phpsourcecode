<?php

/**
 * 自定义表单
 */

namespace app\site\mobile;

class FormMobile extends \app\base\mobile\SiteMobile {


    public function index() {
        $id = request('get', 'id', 0, 'intval');
        $pageLimit = request('get', 'limit', 0, 'intval');
        $urlParams = [
            'id' => $id,
            'limit' => $pageLimit,
        ];
        target('site/Form', 'middle')->setParams($urlParams)->meta()->data()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
            $this->mobileDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        target('site/Form', 'middle')->setParams([
            'id' => request('get', 'id'),
            'limit' => request('get', 'limit')
        ])->data()->export(function ($data) {
            if(!empty($data['pageList'])) {
                $this->success([
                    'data' => $data['pageList'],
                    'page' => $data['pageData']['page'],
                ]);
            }else {
                $this->error('暂无数据');
            }
        }, function ($message, $code, $url) {
            $this->error('暂无数据');
        });
    }

    public function info() {
        $id = request('get', 'id', 0, 'intval');
        $formId = request('get', 'form_id', 0, 'intval');
        $urlParams = [
            'id' => $id,
            'form_id' => $formId
        ];
        target('site/FormInfo', 'middle')->setParams($urlParams)->meta()->data()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->mobileDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function submit() {
        target('site/Form', 'middle')->setParams(request('post'))->post()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}