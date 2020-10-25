<?php

/**
 * 文章管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\article\admin;

class ContentAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'Article';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '文章管理',
                'description' => '管理站点中的文章信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'class_id' => 'B.class_id',
            'pos_id' => 'pos_id',
            'keyword' => 'A.title'
        ];
    }

    protected function _indexWhere($whereMaps) {


        if($whereMaps['pos_id']) {
            $whereMaps['_sql'] = 'FIND_IN_SET('.$whereMaps['pos_id'].', A.pos_id)';
        }

        unset($whereMaps['pos_id']);

        return $whereMaps;
    }

    public function _indexOrder() {
        return 'A.sort asc, A.create_time desc, B.article_id desc';
    }


    public function _indexAssign($pageMaps) {
        $classId = $pageMaps['class_id'];
        return array(
            'posList' => target('site/SitePosition')->loadList(),
            'treeHtml' => target('article/ArticleClass')->getHtmlTree(0, $classId, ['parent_id', 'class_id', 'name']),
            'classId' => $classId
        );
    }

    public function _addAssign() {
        $classId = request('get', 'class_id', 0, 'intval');
        $classInfo = target('article/ArticleClass')->getInfo($classId);
        $modelId = intval($classInfo['model_id']);
        $modelHtml = target('site/SiteModel')->getHtml($modelId, [], 1);
        $filterId = intval($classInfo['filter_id']);
        $filterHtml = target('site/SiteFilter')->getHtml($filterId);
        return array(
            'classList' => target('article/ArticleClass')->loadTreeList(['A.model_id' => $modelId]),
            'posList' => target('site/SitePosition')->loadList(),
            'modelHtml' => $modelHtml,
            'filterHtml' => $filterHtml,
            'classId' => $classId
        );
    }

    public function _editAssign($info) {
        $classId = intval($info['class_id']);
        $modelId = intval($info['model_id']);
        $classInfo = target('article/ArticleClass')->getInfo($classId);
        $modelInfo = target('site/SiteModel')->getContent($modelId, $info['content_id']);
        $modelHtml = target('site/SiteModel')->getHtml($modelId, $modelInfo, 1);
        $filterId = intval($info['filter_id']);
        $filterHtml = target('site/SiteFilter')->getHtml($filterId, $info['content_id']);
        return array(
            'classList' => target('article/ArticleClass')->loadTreeList(['A.model_id' => $modelId]),
            'filterAttr' => target('site/SiteFilterAttr')->loadList(['filter_id' => $classInfo['filter_id']]),
            'posList' => target('site/SitePosition')->loadList(),
            'modelHtml' => $modelHtml,
            'filterHtml' => $filterHtml,
            'classId' => $classId
        );
    }

    public function _statusData($id, $status) {
        $info = target('site/SiteContent')->getInfo($id);
        return target('site/SiteContent')->where([
            'content_id' => $info['content_id']
        ])->data([
            'status' => $status
        ])->update();
    }


}