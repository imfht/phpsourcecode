<?php

class ConfigModel extends PT_Model {

    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $param['create_user_id']=$_SESSION['admin']['userid'];
        $param['create_time']=NOW_TIME;
        $res=$this->insert($param);
        $this->createConfigFile();
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
        $res=$this->update($param);
        $this->createConfigFile();
        return $res;

    }

    /**
     * 删除数据
     *
     * @param $where
     */
    public function del($where) {
        $this->where($where)->delete();
        $this->createConfigFile();
    }

    public function getlist() {
        $config_group=$this->config->get('config_group');
        $list = $this->select();
        foreach ($list as &$v) {
            //后台
            $v['groupname']=$config_group[$v['group']];
            $v['create_username'] = $this->get('user', $v['create_user_id'], 'name');
            $v['update_username'] = $this->get('user', $v['update_user_id'], 'name');
            $v['url_edit'] = U('admin.config.edit', array('id' => $v['id']));
            $v['create_time'] = $v['create_time'] ? date('Y-m-d H:i', $v['create_time']) : '';
            $v['update_time'] = $v['update_time'] ? date('Y-m-d H:i', $v['update_time']) : '';
        }
        return $list;
    }

    public function createConfigFile() {
        $list=$this->field('key,value,type')->where(array('status'=>1,'level'=>1))->select();
        $config=array();
        foreach($list as $v){
            if ($v['type']=='array'){
                $config[$v['key']]=json_decode($v['value'],true);
            }else{
                $config[$v['key']]=$v['value'];
            }
        }
        F(APP_PATH.'/common/config.php',$config);
        F(CACHE_PATH.'/pt_runtime.php',null);
    }
}