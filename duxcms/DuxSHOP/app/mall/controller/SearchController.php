<?php

/**
 * 商品搜索
 */

namespace app\mall\controller;

class SearchController extends \app\base\controller\SiteController {

    protected $_middle = 'mall/List';

    protected $urlParams = [];

    public function init() {
        $limit = request('get', 'limit');
        $keyword = request('get', 'keyword');
        if(empty($keyword)) {
            $this->error('请输入搜索关键词!');
        }
        $this->urlParams = [
            'keyword' => $keyword,
            'limit' => $limit,
        ];

    }

    public function index() {
        target($this->_middle, 'middle')->setParams($this->urlParams)->meta($this->urlParams['keyword'] .' - 商品搜索', '商品搜索', url('index', ['keyword' => $this->urlParams['keyword']]))->data()->filter()->export(function ($data) {
            $this->assign($data);
            $this->assign('urlParams', $this->urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $this->urlParams));
            $this->siteDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        target($this->_middle, 'middle')->setParams($this->urlParams)->data()->export(function ($data) {
            if(!empty($data['pageList'])) {
                $this->success([
                    'data' => $data['pageList'],
                    'page' => $data['pageData']['page']
                ]);
            }else {
                $this->error('暂无数据');
            }

        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}