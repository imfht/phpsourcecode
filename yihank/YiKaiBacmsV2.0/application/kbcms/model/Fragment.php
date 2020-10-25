<?php
namespace app\kbcms\model;
use think\Model;
/**
 * 碎片表操作
 */
class Fragment extends Model {
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        return  $this->paginate();
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
     * @param int $fragmentId ID
     * @return array 信息
     */
    public function getInfo($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
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
        $where['fragment_id']=input('post.fragment_id');
        return $this->allowField(true)->save($_POST,$where);
    }

    /**
     * 删除信息
     * @param int $fragmentId ID
     * @return bool 删除状态
     */
    public function del($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
        return $this->where($map)->delete();
    }

}
