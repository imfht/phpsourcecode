<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 权限组逻辑
 */
class AuthGroup extends AdminBase
{

    /**
     * 构造方法
     */
    public function _initialize()
    {

    }

    /**
     * 获取权限分组列表
     */
    public function getAuthGroupList($where = [], $field = true, $order = '', $paginate = false)
    {

        return $this->getDataList($where, $field, $order, $paginate);
    }

    /**
     * 权限组添加
     */
    public function groupAdd($data = [])
    {


        $url = es_url('authgroupList');

        return $this->dataAdd($data, true, $url, '权限组添加成功');
    }

    /**
     * 权限组编辑
     */
    public function groupEdit($data = [])
    {

        $url = es_url('authgroupList');

        $where['id'] = $data['id'];

        return $this->dataEdit($data, $where, true, $url, '权限组编辑成功');
    }

    /**
     * 权限组删除
     */
    public function groupDel($where = [])
    {

        return $this->dataDel($where, '权限组删除成功', true);
    }

    /**
     * 权限组批量删除
     */
    public function groupAlldel($ids)
    {


        return $this->dataDel(['id' => $ids], '权限组删除成功', true);
    }

    /**
     * 获取权限组信息
     */
    public function getGroupInfo($where = [], $field = true)
    {

        return $this->getDataInfo($where, $field);
    }

    /**
     * 设置用户组权限节点
     */
    public function setGroupRules($data = [])
    {

        $data['rules'] = !empty($data['rules']) ? implode(',', array_unique($data['rules'])) : '';

        $url = url('groupList');

        return $this->dataEdit($data, '', false, $url, '权限设置成功');
    }

    /**
     * 选择权限组
     */
    public function selectAuthGroupList($group_list = [], $member_group_list = [])
    {

        $member_group_ids = array_extract($member_group_list, 'group_id');

        foreach ($group_list as &$info) {

            in_array($info['id'], $member_group_ids) ? $info['tag'] = 'active' : $info['tag'] = '';
        }

        return $group_list;
    }

}
