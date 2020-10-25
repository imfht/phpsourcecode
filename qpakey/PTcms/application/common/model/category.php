<?php
class CategoryModel extends PT_Model{
    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $param['create_user_id']=$_SESSION['admin']['userid'];
        $param['create_time']=NOW_TIME;
        return $this->insert($param);
    }

    /**
     * 修改
     *
     * @param $param
     * @return mixed
     */
    public function edit($param) {
        $param['update_user_id']=$_SESSION['admin']['userid'];
        $param['update_time']=NOW_TIME;
        return $this->update($param);
    }

    /**
     * 删除数据
     *
     * @param $where
     * @return mixed
     */
    public function del($where) {
        return $this->where($where)->delete();
    }

    /**
     * 附加
     * @param $info
     * @return mixed
     */
    public function dataAppend($info) {
        $info['parent']=$this->getParentList($info['id']);
        $info['son']=(array)$this->where(array('pid'=>$info['id']))->field('id,name,key')->select();
        $info['sibling']=(array)$this->where(array('pid'=>$info['pid']))->field('id,name,key')->select();
        return $info;
    }

    // 获取父栏目列表信息
    public function getParentList($id) {
        $info = $this->where(array('id' => $id))->field('id,pid,name,key')->find();
        if ($info['pid'] == '0') return array($info);
        $res[] = $info;
        $res = array_merge($res, $this->getParentList($info['pid']));
        return $res;
    }

    public function getinfo() {

    }

    public function getlist() {

    }
}