<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 扩展模型字段操作
 */
class FieldExpandModel extends BaseModel {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array()){
        $data = $this->table("field as A")
                    ->join('{pre}field_expand as B ON A.field_id = B.field_id', 'INNER')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->select();
        return $data;
    }

    /**
     * 获取信息
     * @param int $FieldId ID
     * @return array 信息
     */
    public function getInfo($FieldId = 1)
    {
        $map = array();
        $map['A.field_id'] = $FieldId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->table("field as A")
                    ->join('{pre}field_expand as B ON A.field_id = B.field_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        
        //事务处理
        $this->beginTransaction();
        $model = target('duxcms/Field');
        $fieldId = $model->saveData($type);
        if(!$fieldId){
            $this->error = $model->getError();
            $this->rollBack();
            return false;
        }
        //分表处理
        $data = $this->create();
        if(!$data){
            $this->rollBack();
            return false;
        }
        if($type == 'add'){
            //写入数据
            $data['field_id'] = $fieldId;
            $status = $this->add($data);
            if($status){
                $this->commit();
            }else{
                $this->rollBack();
            }
            return $status;
        }
        if($type == 'edit'){
            //修改数据
            $where = array();
            $where['field_id'] = $data['field_id'];
            $status = $this->where($where)->save($data);
            if($status === false){
                $this->rollBack();
                return false;
            }
            $this->commit();
            return true;
        }
        $this->rollBack();
        return false;
    }

    /**
     * 删除信息
     * @param int $FieldId ID
     * @return bool 删除状态
     */
    public function delData($fieldId)
    {
        $this->beginTransaction();
        $model = target('duxcms/Field');
        $status = $model->delData($fieldId);
        if(!$status){
            $this->error = $model->getError();
            $this->rollBack();
            return false;
        }
        //删除数据
        $map = array();
        $map['field_id'] = $fieldId;
        $status = $this->where($map)->delete();
        if($status){
            $this->commit();
        }else{
            $this->rollBack();
        }
        return $status;
    }

}
