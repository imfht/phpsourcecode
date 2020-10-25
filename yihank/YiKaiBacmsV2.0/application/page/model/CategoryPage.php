<?php
namespace app\page\model;
use think\Model;

/**
 * 栏目操作
 */
class CategoryPage extends Model {
    //验证

    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId)
    {
        $map = array();
        $map['A.class_id'] = $classId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        $info = $this->name("category")
                    ->alias("A")
                    ->join('category_page B','A.class_id = B.class_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->find();
        if(!empty($info)){
            $info['app'] = strtolower($info['app']);
        }
        return $info;
    }

    /**
     * 新增
     * @return true|false
     */
    public function add(){
        Model::startTrans();
        $class_id=model('kbcms/Category')->add();
        if(!$class_id){
            Model::rollback();
            return false;
        }
        $model=new CategoryPage($_POST);
        $model->class_id=$class_id;
        $rs=$model->allowField(true)->save();
        if ($rs>0){
            Model::commit();
            return true;
        }else{
            Model::rollback();
            return false;
        }
    }
    /**
     * 修改
     * @return true|false
     */
    public function edit(){
        Model::startTrans();
        $status_cat=model('kbcms/Category')->edit();
        if(!$status_cat){
            Model::rollback();
            return false;
        }
        $model=new CategoryPage();
        $where = array();
        $where['class_id'] = input('post.class_id');
        $status = $model->allowField(true)->save($_POST,$where);
        if($status === false){
            Model::rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 删除信息
     * @param int $classId ID
     * @return bool 删除状态
     */
    public function del($classId)
    {
        //总表
        model('kbcms/Category')->del($classId);
        //分表
        $map = array();
        $map['class_id'] = $classId;
        return $this->where($map)->delete();
    }

}
