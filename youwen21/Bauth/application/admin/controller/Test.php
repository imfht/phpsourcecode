<?php
namespace app\admin\controller;

use app\admin\model\UserBehaviorLog as Ulog;

class Test extends Base
{

    public function iplocation($ip='')
    {
        if($ip==''){
            $ip = '180.91.179.145';
        }
        $location = new \app\lib\IpLocation();
        $info = $location->getlocation($ip);
        echo '<pre>';
        print_r( $info );
        exit('</pre>');
    }
    public function ruleAdd()
    {
        if($this->request->isPost()){
            $data = array();
            $data['id'] = '';
            $data['name'] = $this->inputOrError('name');
            $data['title'] = $this->inputOrError('title');
            $data['status'] = $this->inputOrError('status');
            $data['condition'] = $this->request->param('condition');
            $ret = db('auth_rule')->insert($data);
            if($ret !== false){
                return $this->success('添加成功', url('index'));
            }else{
                $this->error('添加失败');
            }
        }else{
            return $this->fetch();
        }
    }

    public function ruleEdit()
    {
        $id = $this->request->param('id');
        $info = db('auth_rule')->where(array('id'=>$id))->find();
        $this->assign('_info', $info);
        return $this->fetch();
    }

    public function updateRule()
    {
        $id = $this->request->param('id');
        $data = array();
        $data['name'] = $this->request->param('name');
        $data['title'] = $this->request->param('title');
        $data['status'] = $this->request->param('status');
        $data['condition'] = $this->request->param('condition');
        $ret = db('auth_rule')->where(array('id'=>$id))->update($data);
        if($ret !== false){
            return $this->success('编辑成功', url('index'));
        }else{
            $this->error('编辑失败');
        }
    }

    public function createMenuRule()
    {
        $menu = db('menu')->select();

        $data = array();
        foreach($menu as $k=>$v){
            $data[$k]['name'] = $v['module'].'/'.$v['url'];
            $data[$k]['title'] = $v['title'];
            $data[$k]['status'] = 1;
            $data[$k]['condition'] = '';

            $data[$k]['module'] = $v['module'];
            $data[$k]['menu_id'] = $v['id'];
            $data[$k]['menu_pid'] = $v['pid'];
        }
        $ret = $this->_insertAll($data);
        if($ret){
            return $this->success('创建菜单规则成功', url('AuthRule/index'));
        }else{
            $this->error('创建权限规则失败');
        }
    }

    private function _insertAll(&$data)
    {
        $sql = $this->_createSql($data);
        $ret = db()->execute($sql);
        return $ret;
    }
    private function _createSql($data)
    {
        $table = config('database.prefix').'auth_rule';
        $sql = 'INSERT INTO '.$table.' (`name`, `title`, `status`, `condition`, `module`, `menu_id`, `menu_pid`) VALUES %values%';
        $values = '';
        foreach($data as $v){
            $values .= '(';
            $values .= "'{$v['name']}',";
            $values .= "'{$v['title']}',";
            $values .= "'{$v['status']}',";
            $values .= "'{$v['condition']}',";
            
            $values .= "'{$v['module']}',";
            $values .= "'{$v['menu_id']}',";
            $values .= "'{$v['menu_pid']}'";

            $values .= '),';
        }
        $values = rtrim($values, ',');

        $sql = str_replace('%values%', $values, $sql);
        return $sql;
    }



}
