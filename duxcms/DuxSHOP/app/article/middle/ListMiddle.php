<?php

/**
 * 文章列表
 */

namespace app\article\middle;

class ListMiddle extends \app\base\middle\BaseMiddle {

    protected $crumb = [];
    protected $classInfo = [];
    protected $listWhere = [];
    protected $listOrder = [];
    protected $listLimit = 20;
    protected $listModel = 0;
    private $tpl = '';

    public function __construct() {
        parent::__construct();
        $this->tpl = $this->siteConfig['tpl_class'];
    }

    private function getClass() {
        if ($this->classInfo) {
            return $this->classInfo;
        }
        $classId = $this->params['classId'];
        if (empty($classId)) {
            return [];
        }
        $this->classInfo = target('article/ArticleClass')->getInfo($classId);

        return $this->classInfo;
    }

    private function getCrumb() {
        if ($this->crumb) {
            return $this->crumb;
        }
        $classId = $this->params['classId'];
        if (empty($classId)) {
            return [];
        }
        $this->crumb = target('article/ArticleClass')->loadCrumbList($classId);

        return $this->crumb;
    }

    protected function classInfo() {
        $this->classInfo = $this->getClass();
        if (empty($this->classInfo)) {
            return $this->run([
                'classInfo' => $this->classInfo,
                'parentClassInfo' => [],
                'topClassInfo' => [],
            ]);
        }
        if ($this->classInfo['url']) {
            $this->data['url'] = $this->classInfo['url'];

            return $this->stop('跳转', 302, $this->classInfo['url']);
        }
        $this->crumb = $this->getCrumb();
        $parentClassInfo = array_slice($this->crumb, -2, 1);
        if (empty($parentClassInfo)) {
            $parentClassInfo = $this->crumb[0];
        } else {
            $parentClassInfo = $parentClassInfo[0];
        }
        $topClassInfo = $this->crumb[0];

        if ($this->classInfo['tpl_class']) {
            $this->tpl = $this->classInfo['tpl_class'];
        }

        return $this->run([
            'classInfo' => $this->classInfo,
            'parentClassInfo' => $parentClassInfo,
            'topClassInfo' => $topClassInfo,
            'tpl' => $this->tpl
        ]);
    }

    protected function meta($title = '', $name = '', $url = '') {
        $classId = $this->params['classId'];
        if ($classId) {
            $this->crumb = $this->getCrumb();
            $this->classInfo = $this->getClass();
            $this->setMeta($this->classInfo['name'], $this->classInfo['keyword'], $this->classInfo['description']);
            $this->setCrumb($this->crumb);
        } else {
            $this->setName($name ? $name : '新闻资讯');
            $this->setMeta($title ? $title : '新闻资讯');
            $this->setCrumb([
                [
                    'name' => $title,
                    'url' => $url ? $url : url()
                ]
            ]);
        }
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data($filterStatus = true) {
        $classId = $this->params['classId'];
        $keyword = str_len(html_clear(urldecode($this->params['keyword'])), 10, false);
        $this->params['limit'] = intval($this->params['limit']);
        $listLimit = $this->params['limit'] ? $this->params['limit'] : 20;
        $modelId = $this->params['model_id'] ? $this->params['model_id'] : 0;
        $tag = str_len(html_clear(urldecode($this->params['tag'])), 10, false);
        $classIds = 0;
        if ($classId) {
            $this->classInfo = $this->getClass();
            $classIds = target('article/ArticleClass')->getSubClassId($classId);
            $modelId = $this->classInfo['model_id'];
        }

        $tagInfo = [];
        if ($tag) {
            $tagInfo = target('site/SiteTags')->getWhereInfo([
                'name' => $tag
            ]);
            if (empty($tagInfo)) {
                return $this->stop('标签不存在', 404);
            }
        }

        $filter = [];
        $where = [];
        $filterWhere = [];

        if ($classIds) {
            $filterWhere['_sql'] = 'A.class_id in(' . $classIds . ')';
        }
        if ($keyword) {
            $filterWhere['_sql'][] = 'B.title like "%' . $keyword . '%"';
        }
        if ($tagInfo) {
            $filterWhere['_sql'][] = 'FIND_IN_SET("' . $tagInfo['tag_id'] . '", B.tags_id)';
        }

        if ($filterStatus) {
            $contentData = target('article/Article')->table('article(A)')->join('site_content(B)', ['B.content_id', 'A.content_id'])->field(['A.content_id'])->where($filterWhere)->limit(0)->select();
            $filter = target('site/SiteFilter')->getFilter($contentData, ['id' => $classId]);
            if ($filter['ids']) {
                $where['_sql'][] = 'A.content_id in (' . $filter['ids'] . ')';
                $where['A.status'] = 1;
            }
        } else {
            $where['A.status'] = 1;
            $where = $filterWhere;
        }
        if ($where) {
            $model = target('article/Article');
            $count = $model->countList($where);
            $pageData = $this->pageData($count, $listLimit);
            $list = $model->loadList($where, $pageData['limit'], '', $modelId);
        } else {
            $pageData = [];
            $list = [];
            $count = 0;
        }

        if ($keyword && $list) {
            target('site/siteSearch')->stats($keyword, 'article');
        }

        return $this->run([
            'tagInfo' => $tagInfo,
            'pageParams' => $filter['urlParam'],
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'attrList' => $filter['attrList']
        ]);
    }


}