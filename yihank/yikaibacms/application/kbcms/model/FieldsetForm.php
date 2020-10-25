<?php
namespace app\kbcms\model;
use think\Model;
/**
 * 表单操作
 */
class FieldsetForm extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(),$limit=15){
        $data = $this->name("fieldset")
                    ->alias('A')
                    ->join('fieldset_form B','A.fieldset_id = B.fieldset_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->limit($limit)
                    ->select();
        return $data;
    }

    /**
     * 获取信息
     * @param int $fieldsetId ID
     * @return array 信息
     */
    public function getInfo($fieldsetId){
        $map = array();
        $map['A.fieldset_id'] = $fieldsetId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where){
        return $this->name("fieldset")
                    ->alias('A')
                    ->join('fieldset_form B','A.fieldset_id = B.fieldset_id')
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
        $model = model('kbcms/Fieldset');
        $fieldsetId = $model->saveData($type,true);
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
        $model = model('kbcms/Fieldset');
        $fieldsetId = $model->add(true);
        if(!$fieldsetId){
            Model::rollback();
            return false;
        }
        //写入数据
        $_POST['fieldset_id'] = $fieldsetId;
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
        $model = model('kbcms/Fieldset');
        $fieldsetId = $model->edit();
        if(!$fieldsetId){
            Model::rollback();
            return false;
        }
        //修改数据
        $where = array();
        $where['fieldset_id'] = input('post.fieldset_id');
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
     * @param int $fieldsetId ID
     * @return bool 删除状态
     */
    public function del($fieldsetId)
    {
        Model::startTrans();
        $model = model('kbcms/Fieldset');
        $status = $model->del($fieldsetId);
        if(!$status){
            Model::rollback();
            return false;
        }
        //删除数据
        $map = array();
        $map['fieldset_id'] = $fieldsetId;
        $status = $this->where($map)->delete();
        if($status){
            Model::commit();
        }else{
            Model::rollback();
        }
        return $status;
    }

    /**
     * 验证表单TOKEN
     * @param string $table 表单名
     * @param string $token 验证信息
     * @return bool 状态
     */
    public function validToken($table, $token)
    {
        $token = session('form_'.$table);
        if(empty($token)){
            return false;
        }
        $formToken = trim($token);
        if($token<>$formToken){
            return false;
        }
        session('form_'.$table,' ');
        return true;
    }

    /**
     * 设置表单TOKEN
     * @param int $fieldsetId ID
     * @return bool 删除状态
     */
    public function setToken($table)
    {
        $token = md5(microtime(true));
        session('form_'.$table,$token);
        return $token;
    }

}
