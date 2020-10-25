<?php
namespace app\admin\model;
use think\Model;
/**
 * 表操作
 */
class HomeUrl extends Model {
    /**
     * 获取列表
     * @return array 列表
     */
    public function allList($where=array(),$field='*',$limit=0){
        return  $this->field($field)->where($where)->order('id Desc')->limit($limit)->select();
    }
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where=array(),$field='*',$limit='15'){
        return  $this->field($field)->where($where)->order('id Desc')->paginate($limit);
    }

    /**
     * 获取统计
     * @return int 数量
     */
    public function countList(){
        return  $this->count();
    }

    /**
     * 获取信息
     * @param int $id ID
     * @return array 信息
     */
    public function getInfo($id)
    {
        $map = array();
        $map['id'] = $id;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
    }


    /**
     * 新增
     */
    public function add(){
        return $this->allowField(true)->save($_POST);
    }
    /**
     * 更新
     */
    public function edit(){
        $where['id']=input('post.id');
        return $this->allowField(true)->save($_POST,$where);
    }

    /**
     * 删除信息
     * @param int $id ID
     * @return bool 删除状态
     */
    public function del($id)
    {
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }

}
