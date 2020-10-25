<?php
namespace app\kbcms\model;
use think\Model;
/**
 * 表单字段操作
 */
class FieldForm extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(),$limit=0){
        $data = $this->name("field")
                    ->alias('A')
                    ->join('field_form B','A.field_id = B.field_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->order('A.sequence asc , A.field_id desc')
                    ->limit($limit)
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
        return $this->name("field")
                    ->alias('A')
                    ->join('field_form B','A.field_id = B.field_id')
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
        $model = model('kbcms/Field');
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
            $status = $this->where($where)->save();
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
