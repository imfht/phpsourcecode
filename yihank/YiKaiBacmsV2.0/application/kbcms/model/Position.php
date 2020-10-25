<?php
namespace app\kbcms\model;
use think\Model;
/**
 * 推荐位表操作
 */
class Position extends Model {
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        return  $this->select();
    }

    /**
     * 获取信息
     * @param int $positionId ID
     * @return array 信息
     */
    public function getInfo($positionId = 1)
    {
        $map = array();
        $map['position_id'] = $positionId;
        return $this->where($map)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        $data = $this->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            return $this->add();
        }
        if($type == 'edit'){
            if(empty($data['position_id'])){
                return false;
            }
            $status = $this->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
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
        $where['position_id']=input('post.position_id');
        return $this->allowField(true)->save($_POST,$where);
    }

    /**
     * 删除信息
     * @param int $positionId ID
     * @return bool 删除状态
     */
    public function del($positionId)
    {
        $map = array();
        $map['position_id'] = $positionId;
        return $this->where($map)->delete();
    }

}
