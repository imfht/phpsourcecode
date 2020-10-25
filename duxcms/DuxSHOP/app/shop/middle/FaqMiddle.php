<?php

/**
 * 商品咨询
 */

namespace app\shop\middle;

class FaqMiddle extends \app\base\middle\BaseMiddle {

    private $crumb = [];
    private $info = [];

    private $app = '';
    private $id = '';
    private $type = 0;


    private function getInfo() {
        $this->id = intval($this->params['id']);
        $this->app = html_clear($this->params['app']);
        if($this->info) {
            return $this->info;
        }

        if (empty($this->id) || empty($this->app)) {
            return [];
        }
        $this->info = target($this->app . '/'. $this->app)->getInfo($this->id);
        return $this->info;
    }

    private function getCrumb() {
        if($this->crumb) {
            return $this->crumb;
        }
        $classId = $this->info['class_id'];
        if (empty($classId)) {
            return [];
        }
        $this->crumb = target($this->app . '/'. $this->app .'Class')->loadCrumbList($classId);
        return $this->crumb;
    }

    protected function meta() {
        $this->info = $this->getInfo();
        $this->crumb = $this->getCrumb();
        if(empty($this->info) || !$this->info['status']) {
            return $this->stop('商品不存在！', 404);
        }
        $this->setMeta('商品咨询 - ' . $this->info['title']);
        $this->setName('商品咨询');
        $this->setCrumb(array_merge($this->crumb, [[
            'name' => '商品咨询',
            'url' => URL
        ]]));
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function info() {
        $info = $this->getInfo();
        return $this->run([
            'info' => $info,
        ]);
    }

    protected function data() {
        $this->id = intval($this->params['id']);
        $this->app = html_clear($this->params['app']);
        $this->params['limit'] = intval($this->params['limit']);
        $where['A.app'] = $this->app;
        $where['A.has_id'] = $this->id;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target('shop/ShopFaq');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'time desc');

        return $this->run([
            'app' => $this->app,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }


}