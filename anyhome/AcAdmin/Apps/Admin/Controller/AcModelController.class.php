<?php
namespace Admin\Controller;
use Think\Controller;
class AcModelController extends CommonController {
    public function index(){
        if (IS_POST) {
            $app_cfg = F($this->code.'/cfg');

            $models = $app_cfg['model'];
            $d = array();
            if ($models) {
                foreach ($models as $k) {
                    $d[] = $k;
                }
            }
            $data['success'] = true;
            $data['data'] = $d;
            $data['totalRows'] = count($d);
            $data['curPage'] = 1;
            $this->ajaxReturn($data);
        }
        $this->display();
    }

    public function edit($name ='')
    {
        $app_cfg = F($this->code.'/cfg');
        $models = $app_cfg['model'];
        $vo = $models[$name];
        $this->assign('vo', $vo);
        $this->display();
    }

    public function update($name='')
    {
        $app_cfg = F($this->code.'/cfg');
        $models = $app_cfg['model'];
        $vo = $models[$name];
        $vo['title'] = I('title');
        $vo['intro'] = I('intro');
        $vo['type'] = I('type');
        $vo['is_nav'] = I('is_nav');
        $app_cfg['model'][$name] = $vo;
        F($this->code.'/cfg',$app_cfg);
        $this->success('更新成功');
    }

    public function insert($name = '')
    {
        if(!$name) $this->error('模型名称不能为空');
        $map['class'] = $name;
        $ApiCloud = D('ApiCloud');
        $ret = $ApiCloud->where($map)->limit(1)->find();
        if (!$ret) {
            $this->error('模型不存在');
        }
        $app_cfg = F($this->code.'/cfg');
        $model = $app_cfg['model'];
        $model[$name]['name'] = $name;
        $field = array();
        foreach ($ret as $k => $val) {
            if($k == 'id') continue;
            if($k == 'createdAt') continue;
            if($k == 'updatedAt') continue;
            $vo['name'] = $k;
            $field[$k] = $vo;
        }
        $model[$name]['field'] = $field;
        $app_cfg['model'] = $model;
        F($this->code.'/cfg',$app_cfg);
        $this->success('更新成功');
    }

    public function delete($name ='')
    {
        if(!$name) $this->error('模型名称不能为空');
        $app_cfg = F($this->code.'/cfg');
        unset($app_cfg['model'][$name]);
        F($this->code.'/cfg',$app_cfg);
        $this->success('删除成功');
    }
}