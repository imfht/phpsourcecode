<?php
namespace app\admin\model;
use think\Model;
/**
 * 碎片表操作
 */
class Lang extends Model {
    /**
     * 获取列表
     * @return array 列表
     */
    public function allList(){
        return  $this->select();
    }
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
     * @param int $langId ID
     * @return array 信息
     */
    public function getInfo($langId)
    {
        $map = array();
        $map['lang_id'] = $langId;
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
        $file=APP_PATH.'/lang/'.input('post.lang').'.php';
        write_lang_file($file);//创建文件
        return $this->allowField(true)->save($_POST);
    }
    /**
     * 更新
     */
    public function edit(){
        $where['lang_id']=input('post.lang_id');
        $info=$this->getInfo(input('post.lang_id'));
        $old_file=APP_PATH.'/lang/'.$info['lang'].'.php';
        $new_file=APP_PATH.'/lang/'.input('post.lang').'.php';
        rename($old_file,$new_file);
        return $this->allowField(true)->save($_POST,$where);
    }

    /**
     * 删除信息
     * @param int $langId ID
     * @return bool 删除状态
     */
    public function del($langId)
    {
        $map = array();
        $map['lang_id'] = $langId;
        $info=$this->getInfo($langId);
        $file=APP_PATH.'/lang/'.$info['lang'].'.php';
        @unlink ($file);
        return $this->where($map)->delete();
    }

}
