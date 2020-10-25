<?php

/**
 * 推广用户管理
 */
namespace app\sale\model;

use app\system\model\SystemModel;

class SaleUserModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'id',
        'into' => '',
        'out' => '',
    ];

    protected function base($where) {
        return $this->table('sale_user(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->join('sale_user_level(C)', ['C.level_id', 'A.level_id'])
            ->join('member_user(D)', ['D.user_id', 'A.parent_id'], '>')
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)','B.avatar(user_avatar)', 'C.name(level_name)', 'D.email(parent_email)', 'D.tel(parent_tel)', 'D.nickname(parent_nickname)', 'D.avatar(parent_avatar)',])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['parent_name'] = target('member/MemberUser')->getNickname($vo['parent_nickname'], $vo['parent_tel'], $vo['parent_email']);
            $list[$key]['user_avatar'] = target('member/MemberUser')->getAvatar($vo['user_id']);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
            $info['parent_name'] = target('member/MemberUser')->getNickname($info['parent_nickname'], $info['parent_tel'], $info['parent_email']);
            $info['user_avatar'] = target('member/MemberUser')->getAvatar($info['user_id']);
        }

        return $info;
    }


    /**
     * 查询下线数据
     * @param $userId
     * @return array
     */
    public function levelList($userId, $max = 3) {
        $userList = $this->loadList();
        $list = $this->levelData($userList, $userId, 1, $max + 1);

        if(empty($list)) {
            return $list;
        }

        $data = [];
        foreach ($list as $key => $vo) {
            $data[$vo['deep']][] = $vo;
        }
        return $data;
    }

    protected function levelData($data, $pid, $deep = 1, $max = 3) {
        static $tree = array();
        if($deep == $max) {
            return $tree;
        }
        foreach ($data as $row) {
            if ($row ['parent_id'] == $pid) {
                $row ['deep'] = $deep;
                $tree [] = $row;
                $this->levelData($data, $row['user_id'], $deep + 1, $max);
            }
        }
        return $tree;
    }

    /**
     * 查询树形数据
     * @param $rows
     * @param string $id
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    public function getDataTree($rows, $id = 'id', $pid = 'parentid', $child = 'child', $root = 0) {
        $tree = array();
        if (is_array($rows)) {
            $array = array();
            foreach ($rows as $key => $item) {
                $array[$item[$id]] =& $rows[$key];
            }
            foreach ($rows as $key => $item) {
                $parentId = $item[$pid];
                if ($root == $parentId) {
                    $tree[] =& $rows[$key];
                } else {
                    if (isset($array[$parentId])) {
                        $parent =& $array[$parentId];
                        $parent[$child][] =& $rows[$key];
                    }
                }
            }
        }
        return $tree;
    }


    /**
     * 递归查询上级用户
     * @param $userId
     * @param int $star
     * @param int $stop
     * @return array
     */
    public function loadParentList($userId, $star = 0, $stop = 3) {
        $data = parent::loadList();
        $cat = new \dux\lib\Category(['user_id', 'parent_id', 'name', 'cname']);
        $data = $cat->getPath($data, $userId);
        $data = array_reverse($data);

        return array_slice($data, $star, $stop);
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
        $class = new \dux\lib\Category(['user_id', 'parent_id', 'name', 'cname']);
        $list = $this->loadList($where, $limit, $order);
        if(empty($list)){
            return [];
        }
        $list = $class->getTree($list, $patrntId);
        return $list;
    }


}