<?php
namespace app\admin\model;
use app\base\model\BaseModel;
/**
 * 上传配置表操作
 */
class ConfigUploadModel extends BaseModel {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        return  $this->select();
    }

    /**
     * 获取统计
     * @return int 数量
     */
    public function countList(){
        return  $this->count();
    }

    /**
     * 获取信息
     * @param int $id ID
     * @return array 信息
     */
    public function getInfo($id)
    {
        $map = array();
        $map['id'] = $id;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
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
            if(empty($data['id'])){
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
     * 删除信息
     * @param int $id ID
     * @return bool 删除状态
     */
    public function delData($id)
    {
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }

}
