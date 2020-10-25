<?php

/**
 * 导航管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteNavModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'nav_id',
        'validate' => [
            'group_id' => [
                'empty' => ['', '未获取到分组信息!', 'must', 'all'],
            ],
            'name' => [
                'len' => ['1, 20', '导航名称输入不正确!', 'must', 'all'],
            ],
            'url' => [
                'empty' => ['', '导航地址输入不正确!', 'must', 'all'],
            ],
        ],
        'format' => [
            'name' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'subname' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'keyword' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'description' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];

    public function _editBefore($data) {
        if ($data['parent_id'] == $data['nav_id']) {
            $this->error = '您不能将当前分类设置为上级分类!';
            return false;
        }
        $cat = $this->loadTreeList('', 0, '', $data['nav_id']);
        if($cat) {
            foreach ($cat as $vo) {
                if ($data['parent_id'] == $vo['nav_id']) {
                    $this->error = '不可以将上一级分类移动到子分类';
                    return false;
                }
            }
        }
        return $data;
    }

    /**
     * 获取导航接口数据
     * @return array
     */
    public function getSiteNav() {
        $list = hook('service', 'nav', 'site');
        $data = array();
        foreach ((array)$list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }

    /**
     * 导航格式化
     * @param array $where
     * @param int $limit
     * @param string $order
     * @return array
     */
    public function loadList($where = [], $limit = 0, $order = '') {
        $list = parent::loadList($where, $limit, 'sort asc, nav_id asc');
        if(empty($list)){
            return [];
        }
        return $list;
    }

    /**
     * 获取分类树
     * @return array
     */
    public function loadTreeList($where = [], $limit = 0, $order = 'sort asc, nav_id asc', $pid = 0) {
        $class = new \dux\lib\Category(['nav_id', 'parent_id', 'name', 'cname']);
        $list = $this->loadList($where, $limit, $order);
        $data = $class->getTree($list, $pid);
        return $data;
    }

}