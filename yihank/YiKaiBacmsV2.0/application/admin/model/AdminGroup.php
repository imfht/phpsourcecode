<?php
namespace app\admin\model;
use think\Model;

/**
 * 用户组操作
 */
class AdminGroup extends Model {
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
        $map['group_id'] = $groupId;
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
        $where['group_id']=input('post.group_id');
        return $this->allowField(true)->save($_POST,$where);
    }
    /**
     * 更新权限
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function savePurviewData(){
        $where['group_id']=input('group_id');
        $menu_purview=input('post.menu_purview/a');

        $base_purview=input('post.base_purview/a');
        $data=array();
        if (!empty($menu_purview)){
            $data['menu_purview'] = implode(',',$menu_purview);
        }
        if (!empty($base_purview)){
            $data['base_purview'] = implode(',',$base_purview);
        }
        $status = $this->save($data,$where);
        if($status === false){
            return false;
        }
        return true;
    }

    /**
     * 删除信息
     * @param int $groupId ID
     * @return bool 删除状态
     */
    public function del($groupId)
    {
        $map = array();
        $map['group_id'] = $groupId;
        return $this->where($map)->delete();
    }

}
