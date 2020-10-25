<?php
class AdminGroupModel extends PT_Model{
    
    protected $table='admin_group';
    /**
     * 插入数据
     * @param $param
     * @return mixed
     */
    public function add($param) {
        return $this->insert($param);
    }

    /**
     * 修改
     * @param $param
     * @return mixed
     */
    public function edit($param) {
        //更新缓存
        $res=$this->update($param);
        $this->rm('admin_group',$param['id']);
        return $res;
    }

    /**
     * 删除数据
     * @param $where
     */
    public function del($where) {
        $list=$this->where($where)->field('id')->select();
        foreach($list as $v){
            $this->rm('admin_group',$v['id']);
        }
        $this->where($where)->delete();
    }

    // 获取列表
    public function getlist() {
        $list=(array)$this->select();
        foreach($list as &$v){
            $v['create_username']=$this->get('user',$v['create_user_id'],'name');
            $v['update_username']=$this->get('user',$v['update_user_id'],'name');
            $v['url_edit']=U('admin.group.edit',array('id'=>$v['id']));
            $v['create_time']=$v['create_time']?date('Y-m-d H:i',$v['create_time']):'';
            $v['update_time']=$v['update_time']?date('Y-m-d H:i',$v['update_time']):'';
        }
        return $list;
    }
}