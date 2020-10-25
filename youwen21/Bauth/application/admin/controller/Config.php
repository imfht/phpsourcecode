<?php
namespace app\admin\controller;

use app\admin\model\Config as Cmodel;

class Config extends Base
{
    public function index()
    {
        $this->assign('defaultSide', 75);
        return $this->fetch('base/index');
    }

    /**
     * 显示分组配置
     * @author baiyouwen
     */
    public function group()
    {
        // \think\Loader::import('@.functions');
        // parse_config_attr();
        $group_id = $this->request->param('id', 1);
        $list = $this->_getConfig($group_id);
        $this->assign('list', $list);
        $this->assign('id', $group_id);
        return $this->fetch('group');
    }

    /**
     * 保存分组配置
     * @author baiyouwen
     */
    public function save($config)
    {
        if ($config && is_array($config)) {
            $db = db('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $db->where($map)->setField('value', $value);
            }
        }
        return $this->success('保存成功！');
    }

    // 获取某分组下的记录
    private function _getConfig($group_id)
    {
        $ret = db('config')->where(['group'=> $group_id, 'status'=>1])->order('sort')->select();
        return $ret;
    }

    public function list_show()
    {
        $this->lists('config', ['status'=>1]);
        return $this->fetch();
    }

    /**
     * 添加一条记录
     * @author baiyouwen
     */
    public function edit_config()
    {
        $id = input('id', 0);
        if($this->request->isPost()){
            if($this->validate($this->request->post(false), $this->_configRule)){
                if($id){
                    $model = Cmodel::get($id);
                    // $model->data = $this->request->post(false);
                    $model->update_time = time();
                    $ret = $model->update($this->request->post(false), ['id'=>$id]);
                }else{
                    $model = new Cmodel();
                    $data = $this->request->post(false);
                    $data['create_time'] = time();
                    $data['update_time'] = $data['create_time'];
                    $data['status'] = 1;
                    $ret = $model->insert($data);
                }
                $typeMsg = $id?'编辑':'添加';
                if($ret){
                    return $this->success($typeMsg.'成功', url('list_show'));
                }else{
                    $this->error($typeMsg.'失败');
                }
            }else{
                $this->error('验证失败');
            }
        }else{
            if($id){
                $info = Cmodel::get($id)->toArray();
                $this->assign('info', $info);
            }
            return $this->fetch('edit_config');
        }
    }

    private $_configRule = [
        'name' => 'required',
        'title' => 'required',
    ];

}
