<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 扩展模型操作
 */
class FieldsetExpandModel extends BaseModel {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array()){
        $data = $this->table("fieldset as A")
                    ->join('{pre}fieldset_expand as B ON A.fieldset_id = B.fieldset_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->select();
        return $data;
    }

    /**
     * 获取信息
     * @param int $fieldsetId ID
     * @return array 信息
     */
    public function getInfo($fieldsetId)
    {
        $map = array();
        $map['A.fieldset_id'] = $fieldsetId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->table("fieldset as A")
                    ->join('{pre}fieldset_expand as B ON A.fieldset_id = B.fieldset_id')
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
        $model = target('duxcms/Fieldset');
        $fieldsetId = $model->saveData($type);
        if(!$fieldsetId){
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
            $data['fieldset_id'] = $fieldsetId;
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
            $where['fieldset_id'] = $data['fieldset_id'];
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
     * @param int $fieldsetId ID
     * @return bool 删除状态
     */
    public function delData($fieldsetId)
    {
        $this->beginTransaction();
        $model = target('duxcms/Fieldset');
        $status = $model->delData($fieldsetId);
        if(!$status){
            $this->error = $model->getError();
            $this->rollBack();
            return false;
        }
        //删除数据
        $map = array();
        $map['fieldset_id'] = $fieldsetId;
        $status = $this->where($map)->delete();
        if($status){
            $this->commit();
        }else{
            $this->rollBack();
        }
        return $status;
    }

    /**
     * 获取还原后扩展数据
     * @param int $fieldsetId 字段集ID
     * @param int $dataId 数据ID
     * @return array 数据
     */
    public function getDataInfo($fieldsetId,$dataId)
    {
        //获取模型信息
        $fieldsetInfo = target('duxcms/FieldsetExpand')->getInfo($fieldsetId);
        if(empty($fieldsetInfo)){
            return ;
        }
        //获取字段内容
        target('duxcms/FieldData')->setTable('ext_'.$fieldsetInfo['table']);
        $extInfo = target('duxcms/FieldData')->getInfo($dataId);
        if(empty($extInfo)){
            return ;
        }
        //获取字段列表
        $where = array();
        $where['A.fieldset_id'] = $fieldsetId;
        $fieldList = target('duxcms/FieldExpand')->loadList($where); 
        if(empty($fieldList)){
            return ;
        }
        $extArray = array();
        foreach ($fieldList as $value) {
            $extArray[$value['field']] = target('duxcms/FieldData')->revertField($extInfo[$value['field']],$value['type'],$value['config']);
        }
        return $extArray;

    }

}
