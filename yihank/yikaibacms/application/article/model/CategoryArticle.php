<?php
namespace app\article\model;
use think\Model;

/**
 * Class ArticleCategory 文章栏目信息模型
 * hongkai.wang 20161203  QQ：529988248
 */
class CategoryArticle extends Model
{
    /**
     * 获取数据
     * @param 栏目id $class_id
     * @return mixed
     */
    public function getInfo($class_id){
        $map = array();
        $map['A.class_id'] = $class_id;
        return $this->getWhereInfo($map);
    }
    /**
     * 获取where数据
     * @param 条件$where
     * @return 一维数组
     */
    public function getWhereInfo($where){
        $info = $this->name('category')->alias('A')
            ->field('B.*,A.*')
            ->join('category_article B','A.class_id=B.class_id')
            ->where($where)
            ->find();
        return $info;
    }
    /**
     * 新增
     * @return true|false
     */
    public function add(){
        $_POST['app']=request()->module();
        Model::startTrans();
        $class_id=model('kbcms/Category')->add();
        if(!$class_id){
            Model::rollback();
            return false;
        }
        $model=new CategoryArticle($_POST);
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
        $_POST['app']=request()->module();
        Model::startTrans();
        $status_cat=model('kbcms/Category')->edit();
        if(!$status_cat){
            Model::rollback();
            return false;
        }
        $model=new CategoryArticle();
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
     * 删除数据
     * @param 栏目id $class_id
     * @return 1|0
     */
    public function del($class_id){
        //总表
        model('kbcms/Category')->del($class_id);
        //分表
        $map = array();
        $map['class_id'] = $class_id;
        return $this->where($map)->delete();
    }
}
