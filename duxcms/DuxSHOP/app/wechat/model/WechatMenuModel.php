<?php

/**
 * 微信菜单
 */
namespace app\wechat\model;

use app\system\model\SystemModel;

class WechatMenuModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'menu_id',
        'into' => '',
        'out' => '',
    ];

    /**
     * 获取分类树
     * @param array $where
     * @param int $limit
     * @param string $order
     * @param int $patrntId
     * @return array
     */
    public function loadTreeList(array $where = [], $limit = 0, $order = '', $patrntId = 0) {
        $class = new \dux\lib\Category(['menu_id', 'parent_id', 'name', 'cname']);
        $list = $this->loadList($where, $limit, $order);
        if(empty($list)){
            return [];
        }
        return $class->getTree($list, $patrntId);
    }


    public function _saveBefore($data ,$type) {

        if(!$data['type']) {
            if(strpos($data['key'],'http://', 0) === false) {
                $this->error = '跳转页面地址必须以http://开头!';
                return false;
            }
        }

        if($type == 'edit') {

            if ($data['parent_id'] == $data['class_id']) {
                $this->error = '您不能将当前分类设置为上级分类!';
                return false;
            }

            $cat = $this->loadTreeList([], 0, '', $data['class_id']);
            if($cat) {
                foreach ($cat as $vo) {
                    if ($_POST['parent_id'] == $vo['class_id']) {
                        $this->error = '不可以将上一级分类移动到子分类';
                        return false;
                    }
                }
            }

            $catData = $this->countList([
                'parent_id' => $data['parent_id'],
                '_sql' => 'menu_id <> ' . $data['menu_id']
            ]);

        }else {
            $catData = $this->countList([
                'parent_id' => $data['parent_id']
            ]);
        }


        $maxNum = 5;
        if(!$data['parent_id']) {
            $maxNum = 3;
        }



        if($catData >= $maxNum) {
            $this->error = '当前父菜单下的菜单数量不能超过' . $maxNum . '个';
            return false;
        }


        return $data;
    }


}