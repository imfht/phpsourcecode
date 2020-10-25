<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 表单操作
 */
class FieldsetFormModel extends BaseModel {

    //完成
    protected $_auto = array (
        array('show_list','intval',3,'function'),
        array('show_info','intval',3,'function'),
        array('list_page','intval',3,'function'),
        array('post_status','intval',3,'function'),
    );

    //验证
    protected $_validate = array(
        array('list_order','require', '请填写内容排序'),
        array('post_msg','require', '请填写提交成功消息'),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array()){
        $data = $this->table("fieldset as A")
                    ->join('{pre}fieldset_form as B ON A.fieldset_id = B.fieldset_id')
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
                    ->join('{pre}fieldset_form as B ON A.fieldset_id = B.fieldset_id')
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
