<?php
namespace app\admin\model;
use think\Model;

/**
 * 用户组操作
 */
class UserType extends Model {
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        $data   = $this->select();
        return $data;
    }

    /**
     * 获取信息
     * @param int $groupId ID
     * @return array 信息
     */
    public function getInfo($groupId = 1)
    {
        $map = array();
        $map['type_id'] = $groupId;
        return $this->where($map)->find();
    }
    /**
     * 新增
     */
    public function add(){
        return $this->allowField(true)->save($_POST);
    }
    /**
     * 更新
     */
    public function edit(){
        $where['type_id']=input('post.type_id');
        return $this->allowField(true)->save($_POST,$where);
    }
    /**
     * 删除信息
     * @param int $groupId ID
     * @return bool 删除状态
     */
    public function del($groupId){
        $map = array();
        $map['type_id'] = $groupId;
        return $this->where($map)->delete();
    }

}
