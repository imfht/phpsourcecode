<?php

/**
 * 分类管理
 */
namespace app\mall\model;

use app\system\model\SystemModel;

class MallClassModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'class_id'
    ];

    protected function base($where) {
        return $this->table('site_class(A)')
            ->join('mall_class(B)', ['B.category_id', 'A.category_id'])
            ->field(['A.*', 'B.class_id', 'B.parent_id', 'B.tpl_class', 'B.tpl_content', 'B.spec_group_id'])
            ->where((array)$where);
    }

    /**
     * 获取分类树
     * @return array
     */
    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.sort asc, B.class_id asc')
            ->select();
        if(empty($list)){
            return [];
        }
        foreach($list as $key => $vo) {
            if(empty($vo['url'])) {
                $list[$key]['url'] = $this->getUrl($vo['class_id']);
            }
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        return $this->base($where)->find();
    }

    public function getInfo($id) {
        $where = [];
        $where['B.class_id'] = $id;
        return $this->getWhereInfo($where);
    }

    /**
     * 获取分类树
     * @param array $where
     * @param int $limit
     * @param string $order
     * @param int $patrntId
     * @return array
     */
    public function loadTreeList(array $where = [], $limit = 0, $order = '', $patrntId = 0) {
        $class = new \dux\lib\Category(['class_id', 'parent_id', 'name', 'cname']);
        $list = $this->loadList($where, $limit, $order);
        if(empty($list)){
            return [];
        }
        $list = $class->getTree($list, $patrntId);
        return $list;
    }

    /**
     * 获取菜单面包屑
     * @param int $classId 菜单ID
     * @return array 菜单表列表
     */
    public function loadCrumbList($classId)
    {
        $data = $this->loadList();
        $cat = new \dux\lib\Category(['class_id', 'parent_id', 'name', 'cname']);
        $data = $cat->getPath($data, $classId);
        return $data;
    }

    /**
     * 获取子栏目ID
     * @param array $classId 当前栏目ID
     * @return string 子栏目ID
     */
    public function getSubClassId($classId)
    {
        $data = $this->loadTreeList([], 0, '', $classId);
        $list = array();
        $list[] = $classId;
        foreach ($data as $value) {
            $list[]=$value['class_id'];
        }
        return implode(',', $list);
    }

    public function _delBefore($id) {
        $info = $this->getInfo($id);
        return target('site/SiteClass')->delData($info['category_id']);
    }

    /**
     * 获取分类链接
     * @param $id
     * @return string
     */
    public function getUrl($id) {
        return url(VIEW_LAYER_NAME . '/mall/Index/index',array('id' => $id));
    }

    /**
     * 保存栏目数据
     * @param string $type
     * @param array $data
     * @return bool
     */
    public function saveData($type = 'add', $data = []) {
        $this->beginTransaction();
        $data = empty($data) ? $_POST : $data;
        if ($type == 'add') {
            $names = explode("\n", $data['name']);

            foreach($names as $name) {
                if(empty($name)) {
                    continue;
                }

                unset($data['category_id']);
                $data['name'] = $name;
                $id = target('site/SiteClass')->saveData('add', $data);
                if (!$id) {
                    $this->rollBack();
                    $this->error = target('site/SiteClass')->getError();
                    return false;
                }
                $data['category_id'] = $id;
                $id = parent::saveData('add', $data);
                if (!$id) {
                    $this->rollBack();
                    $this->error = $this->getError();
                    return false;
                }
            }
        }
        if ($type == 'edit') {
            $info = $this->getInfo($data['class_id']);
            $data['category_id'] = $info['category_id'];
            if ($data['parent_id'] == $data['class_id']) {
                $this->rollBack();
                $this->error = '您不能将当前分类设置为上级分类!';
                return false;
            }
            $cat = $this->loadTreeList([], 0, '', $data['class_id']);
            if($cat) {
                foreach ($cat as $vo) {
                    if ($data['parent_id'] == $vo['class_id']) {
                        $this->rollBack();
                        $this->error = '不可以将上一级分类移动到子分类';
                        return false;
                    }
                }
            }
            $status = target('site/SiteClass')->saveData('edit', $data);
            if (!$status) {
                $this->rollBack();
                $this->error = target('site/SiteClass')->getError();
                return false;
            }
            $status = parent::saveData('edit', $data);
            if (!$status) {
                $this->rollBack();
                $this->error = $this->getError();
                return false;
            }
            $id = $data['class_id'];
        }
        $this->commit();
        return $id;
    }



    public function hasCoupon($coupon, $order) {
        if ($coupon['type'] <> 'class') {
            return false;
        }
        if (!$coupon['has_id']) {
            return false;
        }
        $classIds = [];
        $classList = $this->loadCrumbList($coupon['has_id']);

        foreach ($classList as $vo) {
            $classIds[] = $vo['class_id'];
        }

        if(empty($classIds)) {
            return false;
        }

        $goodsIds = [];
        foreach ($order['items'] as $v) {
            $goodsIds[] = $v['app_id'];
        }
        $goodsIds = implode(',', $goodsIds);
        $goodsList = target('mall/Mall')->loadList([
            '_sql' => 'B.mall_id in ('.$goodsIds.')'
        ]);

        if(empty($goodsList)) {
            return false;
        }

        $goodsData = [];
        foreach ($goodsList as $vo) {
            $goodsData[$vo['mall_id']] = $vo;
        }

        $total = 0;
        $ids = [];
        foreach ($order['items'] as $v) {
            if (!in_array($goodsData[$v['app_id']]['class_id'], $classIds)) {
                continue;
            }
            $total = price_calculate($total, '+', $v['total']);
            $ids[] = $v['id'];
        }

        if (bccomp($coupon['meet_money'], $total, 2) !== 1) {
            return $ids;
        }

        return false;
    }

    public function urlCoupon($info) {
        return url(VIEW_LAYER_NAME . '/mall/Index/index', ['id' => $info['has_id']]);
    }

}