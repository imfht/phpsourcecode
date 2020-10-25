<?php

/**
 * 文章管理
 */
namespace app\article\model;

use app\system\model\SystemModel;

class ArticleModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'article_id',
        'validate' => [
            'class_id' => [
                'empty' => ['', '请选择分类!', 'must', 'all'],
            ],
        ],
        'format' => [
            'content' => [
                'function' => ['html_in', 'all'],
            ]
        ]
    ];

    protected function base($where, $modelId = 0) {
        $base = $this->table('site_content(A)')
            ->join('article(B)', ['B.content_id', 'A.content_id'])
            ->join('article_class(C)', ['C.class_id', 'B.class_id'])
            ->join('site_class(D)', ['D.category_id', 'C.category_id']);
        $field = ['A.*', 'B.article_id', 'B.class_id', 'B.content', 'D.name(class_name)', 'D.model_id', 'D.filter_id'];
        if($modelId) {
            $modelInfo = target('site/SiteModel')->getInfo($modelId);
            $base = $base->join('model_' . $modelInfo['label'].'(E)', ['E.content_id', 'A.content_id'], '<');
            $field[] = 'E.*';
        }
        return $base
            ->field($field)
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = 'A.sort asc, A.create_time desc, B.article_id desc', $modelId = 0) {
        $list = $this->base($where, $modelId)
            ->limit($limit)
            ->order($order)
            ->select();
        if(empty($list)){
            return [];
        }
        foreach($list as $key => $vo) {
            if(!$vo['url']) {
                $list[$key]['url'] = $this->getUrl($vo['article_id']);
            }
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where, $modelId = 0) {
        return $this->base($where, $modelId)->find();
    }

    public function getInfo($id, $modelId = 0) {
        $where = [];
        $where['B.article_id'] = $id;
        return $this->getWhereInfo($where, $modelId);
    }

    public function _delBefore($id) {
        $info = $this->getInfo($id);
        return target('site/SiteContent')->delData($info['content_id']);
    }

    public function getUrl($id) {
        return url(VIEW_LAYER_NAME . '/article/Info/index',array('id' => $id));
    }

    public function saveData($type = 'add', $data = []) {
        $_POST['app'] = 'article';
        $this->beginTransaction();
        if ($_POST['content'] && empty($_POST['description'])) {
            $_POST['description'] = \dux\lib\Str::strMake($_POST['content'], 250);
        }
        if ($type == 'add') {
            $id = target('site/SiteContent')->saveData('add');
            if (!$id) {
                $this->rollBack();
                $this->error = target('site/SiteContent')->getError();
                return false;
            }
            $_POST['content_id'] = $id;
            $id = parent::saveData('add');
            if (!$id) {
                $this->rollBack();
                $this->error = $this->getError();
                return false;
            }
        }
        if ($type == 'edit') {
            $info = $this->getInfo($_POST['article_id']);
            $_POST['content_id'] = $info['content_id'];
            $status = target('site/SiteContent')->saveData('edit');
            if (!$status) {
                $this->rollBack();
                $this->error = target('site/SiteContent')->getError();
                return false;
            }
            $status = parent::saveData('edit');
            if (!$status) {
                $this->rollBack();
                $this->error = $this->getError();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function delData($id) {
        $info = $this->getInfo($id);
        $this->beginTransaction();
        $where = array();
        $where['article_id'] = $id;
        if (!$this->where($where)->delete()) {
            $this->rollBack();
            return false;
        }
        if (!target('site/SiteContent')->delData($info)) {
            $this->rollBack();
            return false;
        }
        $this->commit();
        return true;
    }

}