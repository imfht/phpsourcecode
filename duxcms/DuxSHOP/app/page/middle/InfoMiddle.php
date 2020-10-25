<?php

/**
 * 单页详情
 */

namespace app\page\middle;

class InfoMiddle extends \app\base\middle\BaseMiddle {

    private $crumb = [];
    private $info = [];
    private $tpl = '';

    public function __construct() {
        parent::__construct();
        $this->tpl = 'page';
    }

    private function getInfo() {
        if($this->info) {
            return $this->info;
        }
        $id = $this->params['id'];
        if (empty($id)) {
            return [];
        }
        $this->info = target('page/Page')->getInfo($id);
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
        $this->crumb = target('page/Page')->loadCrumbList($classId);
        return $this->crumb;
    }

    protected function meta() {
        $this->info = $this->getInfo();
        $this->crumb = $this->getCrumb();
        $this->setMeta($this->info['title']);
        $this->setName($this->info['title']);
        $this->setCrumb($this->crumb);
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $this->info = $this->getInfo();
        $this->crumb = $this->getCrumb();

        if(empty($this->info)) {
            return $this->stop('页面不存在！', 404);
        }

        if ($this->info['url']) {
            return $this->stop('页面重置!', 302, $pageInfo['url']);
        }

        if($this->info['tpl']) {
            $this->tpl = $this->info['tpl'];
        }

        $parentPageInfo = array_slice($this->crumb, -2, 1);
        if (empty($parentPageInfo)) {
            $parentPageInfo = $crumb[0];
        } else {
            $parentPageInfo = $parentPageInfo[0];
        }
        $topPageInfo = $crumb[0];

        return $this->run([
            'info' => $this->info,
            'parentPageInfo' => $parentPageInfo,
            'topPageInfo' => $topPageInfo,
            'tpl' => $this->tpl
        ]);
    }

}