<?php
namespace app\home\model;
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
}
