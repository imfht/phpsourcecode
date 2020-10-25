<?php
namespace app\home\model;
use think\Model;

/**
 * Class Category 栏目基础信息模型
 * hongkai.wang 20161203  QQ：529988248
 */
class CategoryPage extends Model
{
    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId){
        $map = array();
        $map['A.class_id'] = $classId;
        return $this->getWhereInfo($map);
    }
    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where){
        $info = $this->name('category')
                    ->alias('A')
                    ->join('category_page B','A.class_id = B.class_id','left')
                    ->where($where)->find();
        if(!empty($info)){
            $info['app'] = strtolower($info['app']);
        }
        return $info;
    }
}
