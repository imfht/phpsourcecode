<?php

/**
 * 评论详情
 */

namespace app\shop\middle;

class CommentMiddle extends \app\base\middle\BaseMiddle {

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
        $this->setMeta('评论 - ' . $this->info['title']);
        $this->setName('商品评论');
        $this->setCrumb(array_merge($this->crumb, [
            [
                'name' => '评论',
                'url' => URL
            ]
        ]));
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function info() {
        $info = $this->getInfo();
        $countCommentPositive = target('order/OrderComment')->countList([
            'A.app' => $this->app,
            'A.has_id' => $this->id,
            '_sql' => 'A.level = 4 OR A.level = 5'
        ]);
        $countCommentNeutral = target('order/OrderComment')->countList([
            'A.app' => $this->app,
            'A.has_id' => $this->id,
            '_sql' => 'A.level = 3 OR A.level = 4'
        ]);
        $countCommentNegative = target('order/OrderComment')->countList([
            'A.app' => $this->app,
            'A.has_id' => $this->id,
            '_sql' => 'A.level = 0 OR A.level = 1'
        ]);

        $sumComment = $countCommentPositive + $countCommentNeutral + $countCommentNegative;

        $commentPositiveRate = $sumComment ? round(($countCommentPositive / $sumComment) * 100) : 0;
        $commentNeutralRate = $sumComment ? round(($countCommentNeutral / $sumComment) * 100) : 0;
        $commentNegativeRate = $sumComment ? round(($countCommentNegative / $sumComment) * 100) : 0;

        return $this->run([
            'info' => $info,
            'commentRate' => [
                'positive' => $commentPositiveRate,
                'neutral' => $commentNeutralRate,
                'negative' => $commentNegativeRate,
            ],
            'commentCount' => [
                'positive' => $countCommentPositive,
                'neutral' => $countCommentNeutral,
                'negative' => $countCommentNegative,
            ]
        ]);
    }

    protected function data() {
        $this->id = intval($this->params['id']);
        $this->app = html_clear($this->params['app']);
        $this->type = intval($this->params['type']);
        $this->params['limit'] = intval($this->params['limit']);
        $where = [];
        switch($this->type) {
            case 1:
                $where['_sql'] = 'A.level = 4 OR A.level = 5';
                break;
            case 2:
                $where['_sql'] = 'A.level = 3 OR A.level = 4';
                break;
            case 3:
                $where['_sql'] = 'A.level = 0 OR A.level = 1';
                break;
        }
        $where['A.status'] = 1;
        $where['A.app'] = $this->app;
        $where['A.has_id'] = $this->id;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target('order/OrderComment');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'time desc');

        return $this->run([
            'type' => $this->type,
            'app' => $this->app,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }


}