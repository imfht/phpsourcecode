<?php
namespace app\mall\service;

/**
 * 标签接口
 */
class LabelService {

    /**
     * 栏目列表
     */
    public function classList($data) {
        $where = array();
        //上级栏目
        if (isset($data['parent_id'])) {
            $where['parent_id'] = $data['parent_id'];
        }
        //指定栏目
        if (!empty($data['class_id'])) {
            $where['_sql'][] = 'class_id in (' . $data['class_id'] . ')';
        }
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        return target('mall/MallClass')->loadList($where, $data['limit']);
    }

    /**
     * 栏目树列表
     */
    public function classTreeList($data) {
        $where = array();
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        if (isset($data['parent_id'])) {
            $data['parent_id'] = intval($data['parent_id']);
        }
        $list = target('mall/MallClass')->loadList($where, 0);
        $cartList = target('mall/MallClass')->getTree($list);
        if ($data['parent_id']) {
            $cartList = $this->_searchSubClass($cartList, $data['parent_id']);
        }
        return array_slice($cartList, 0, $data['limit']);
    }

    public function _searchSubClass($data, $parentId = 0) {
        foreach ($data as $list) {
            if ($list['class_id'] == $parentId) {
                return $list['children'];
            } else {
                return $this->_searchSubClass($list['children'], $parentId);
            }
        }

    }

    /**
     * 内容列表
     */
    public function contentList($data) {
        $where = [];
        //指定栏目内容
        if (!empty($data['class_id'])) {
            $classWhere = 'B.class_id in (' . $data['class_id'] . ')';
        }
        //指定栏目下子栏目内容
        if ($data['sub'] && !empty($data['class_id'])) {
            $classIds = target('mall/MallClass')->getSubClassId($data['class_id']);
            if (!empty($classIds)) {
                $classWhere = "B.class_id in ({$classIds})";
            }
        }
        if (!empty($classWhere)) {
            $where['_sql'][] = $classWhere;
        }
        if (!empty($data['pos'])) {
            $where['_sql'][] = 'FIND_IN_SET('.$data['pos'].', A.pos_id)';
        }
        //是否带形象图
        if (isset($data['image'])) {
            if ($data['image'] == true) {
                $where['_sql'][] = 'A.image <> ""';
            } else {
                $where['B.image'] = '';
            }
        }
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        //调用数量
        if (empty($data['limit'])) {
            $data['limit'] = 10;
        }
        //模型调用
        $data['model_id'] = intval($data['model_id']);
        if(empty($data['order'])) {
            $data['order'] = 'A.content_id desc';
        }
        $model = target('mall/Mall');
        $list = $model->loadList($where, $data['limit'], $data['order'], $data['model_id']);
        return $list;
    }

}
