<?php
namespace app\kbcms\model;
use think\Model;
/**
 * 扩展模型字段操作
 */
class FieldExpand extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array()){
        $data = $this->name("field")
                    ->alias('A')
                    ->join('field_expand B','A.field_id = B.field_id', 'INNER')
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
    public function getInfo($FieldId)
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
        return $this->name("field")
                    ->alias('A')
                    ->join('field_expand B','A.field_id = B.field_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->find();
    }

    /**
     * 新增
     */
    public function add(){
        //事务处理
        Model::startTrans();
        $model = model('kbcms/Field');
        $fieldId = $model->add();
        if(!$fieldId){
            Model::rollback();
            return false;
        }
        //写入数据
        $_POST['field_id'] = $fieldId;
        $status = $this->allowField(true)->save($_POST);
        if($status){
            Model::commit();
        }else{
            Model::rollback();
        }
        return $status;
    }
    /**
     * 修改
     */
    public function edit(){
        //事务处理
        Model::startTrans();
        $model = model('kbcms/Field');
        $fieldId = $model->edit();
        if(!$fieldId){
            Model::rollback();
            return false;
        }
        //修改数据
        $where = array();
        $where['field_id'] = input('post.field_id');
        $status = $this->allowField(true)->save($_POST,$where);
        if($status === false){
            Model::rollback();
            return false;
        }
        Model::commit();
        return true;
    }
    /**
     * 删除信息
     * @param int $FieldId ID
     * @return bool 删除状态
     */
    public function del($fieldId){
        //事务处理
        Model::startTrans();
        $model = model('kbcms/Field');
        $status = $model->del($fieldId);
        if(!$status){
            Model::rollback();
            return false;
        }
        //删除数据
        $map = array();
        $map['field_id'] = $fieldId;
        $status = $this->where($map)->delete();
        if($status){
            Model::commit();
        }else{
            Model::rollback();
        }
        return $status;
    }

}
