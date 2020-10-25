<?php

class AdminNodeModel extends PT_Model {

    protected $table='admin_node';

    // 顶部模块nodeid
    protected $moduleId = '30';
    // 左边模块分组 nodeid
    protected $moduleTopId = '41';


    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $param['create_user_id']=$_SESSION['admin']['userid'];
        $param['create_time']=NOW_TIME;
        return $this->db('admin_node')->insert($param);
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
        return $this->db('admin_node')->update($param);
    }

    /**
     * 删除数据
     *
     * @param $where
     */
    public function del($where) {
        return $this->db('admin_node')->where($where)->delete();
    }

    /**
     * 获取当前节点及父节点的信息
     *
     * @return mixed
     */
    public function getMenuInfo() {
        $info = $this->db('admin_node')->field('id,name,pid')->where(array('module' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => ACTION_NAME))->find();
        $parentinfo = $this->db('admin_node')->field('name,pid,module,controller,action')->where(array('id' => $info['pid']))->find();
        if (empty($parentinfo['module'])) {
            $res['menu']['name'] = $info['name'];
            $res['menu']['url'] = U(MODULE_NAME . '.' . CONTROLLER_NAME . '.' . ACTION_NAME);
            $res['submenu']['name'] = '';
            $res['submenu']['url'] = '';
        } else {
            $res['menu']['name'] = $parentinfo['name'];
            $res['menu']['url'] = U($parentinfo['module'] . '.' . $parentinfo['controller'] . '.' . $parentinfo['action']);
            $res['submenu']['name'] = $info['name'];
            $res['submenu']['url'] = U(MODULE_NAME . '.' . CONTROLLER_NAME . '.' . ACTION_NAME);
        }
        $res['nodeid'] = $info['id'];
        return $res;
    }

    // 获取父栏目列表信息
    public function getParentList($id) {
        $info = $this->db('admin_node')->where(array('id' => $id))->field('id,pid')->find();
        if ($info['pid'] == 0) return array();
        $res[] = $info['pid'];
        $res = array_merge($res, $this->getParentList($info['pid']));
        return $res;
    }

    //转换为权限数组
    public function toNodeAuth($arr) {
        foreach ($arr as $v) {
            $plist = $this->getParentList($v);
            foreach ($plist as $id) {
                if (!in_array($id, $arr)) $arr[] = $id;
            }
        }
        return $arr;
    }

    //安装
    public function install($config, $group, $module) {
        if ($group == '') {
            $pid=$topid = $this->moduleTopId;
        } else {
            //添加
            $param['name']=$group;
            $param['pid']=$this->moduleId;
            $param['module']='';
            $param['controller']='';
            $param['action']='';
            $param['status']=1;
            $param['ordernum']=50;
            $pid=$topid=$this->add($param);
        }
        $list = $this->parseNodeConfig($config);
        foreach($list as $v){
            foreach($v as $n=>$param){
                $param['status']=1;
                $param['ordernum']=50;
                $param['module']=$module;
                if ($n==0){
                    $param['pid']=$topid;
                    $pid=$this->add($param);
                }else{
                    $param['pid']=$pid;
                    $this->add($param);
                }
            }
        }
        return true;
    }

    //写在
    public function uninstall($config, $group, $module) {
        if ($group!=''){
            $this->del(array('name'=>$group,'pid'=>$this->moduleId));
        }
        $list = $this->parseNodeConfig($config);
        foreach($list as $v){
            foreach($v as $i){
                $i['module']=$module;
                $this->del($i);
            }
        }
        return true;
    }

    //解析node配置
    public function parseNodeConfig($config) {
        $arr = explode("\n", trim($config));
        $list = array();
        foreach ($arr as $k=>$v) {
            $tmp = explode('|', trim($v));
            foreach ($tmp as $i) {
                $t = explode(':', trim($i));
                if (count($t) == 3) {
                    $list[$k][] = array(
                        'name' => $t['0'],
                        'controller' => $t['1'],
                        'action' => $t['2'],
                    );
                }
            }
        }
        return $list;
    }
}