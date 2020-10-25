<?php

/**
 * 商品咨询
 */

namespace app\shop\controller;

class FaqController extends \app\base\controller\SiteController {


    protected $_middle = 'shop/Faq';

    /**
     * 首页
     */
    public function index() {

        $app = request('get', 'app', 0, 'html_clear');
        $id = request('get', 'id', 0, 'intval');
        $pageLimit = request('get', 'limit', 0, 'intval');
        $urlParams = [
            'id' => $id,
            'app' => $app,
            'limit' => $pageLimit,
        ];
        target($this->_middle, 'middle')->setParams($urlParams)->meta()->info()->data()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
            $this->siteDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        $app = request('get', 'app', 0, 'html_clear');
        $id = request('get', 'id', 0, 'intval');
        $pageLimit = request('get', 'limit', 0, 'intval');
        $urlParams = [
            'id' => $id,
            'app' => $app,
            'limit' => $pageLimit,
        ];
        target($this->_middle, 'middle')->setParams($urlParams)->data()->export(function ($data) use ($urlParams) {
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

}