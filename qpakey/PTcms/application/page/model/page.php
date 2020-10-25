<?php

class PageModel extends PT_Model {

    protected $imgext = array('jpg', 'png', 'gif', 'bmp', 'jpeg');

    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $param['create_user_id']=$_SESSION['admin']['userid'];
        $param['create_time']=NOW_TIME;
        $res = $this->insert($param);
        return $res;
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
        $res = $this->update($param);
        return $res;
    }

    /**
     * 删除数据
     *
     * @param $where
     * @return mixed
     */
    public function del($where) {
        $info = $this->where($where)->find();
        if ($info) {
            $res = $this->where($where)->delete();
            return $res;
        }
        return false;
    }

    //获取列表
    public function getlist() {
        $list=(array)$this->select();
        foreach($list as &$v){
            if (isset($v['create_user_id'])){
                //后台
                $v['create_username']=$this->model->get('user',$v['create_user_id'],'name');
                $v['update_username']=$this->model->get('user',$v['update_user_id'],'name');
                $v['url_edit']=U('page.manage.edit',array('id'=>$v['id']));
                $v['create_time']=$v['create_time']?date('Y-m-d H:i',$v['create_time']):'';
                $v['update_time']=$v['update_time']?date('Y-m-d H:i',$v['update_time']):'';
                unset($v['content']);
            }
        }
        return $list;
    }


}