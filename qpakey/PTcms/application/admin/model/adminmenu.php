<?php
class adminMenuModel extends PT_Model{

    /**
     * 获取用户菜单
     */
    public function getusermenu() {

        $tree=new Tree($this->db('admin_node'));
        $where=array('status'=>1);
        if ($hide=$this->db('module')->where('status=0')->getfield('key',true)){
            $where['module']=array('not in',$hide);
        }
        if($this->session->get('admin.userid')!='1'){
            //其他
            $where['id']=array('in',$this->model->get('admin_group',$_SESSION['admin']['groupid'],'node'));
        }
        return $tree->getSonList(0,'id,name,module,controller,action',$where);
    }
}