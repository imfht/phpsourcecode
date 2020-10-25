<?php
namespace Admin\Controller;
use Think\Controller;
class AcFieldController extends CommonController {
    public function index($table = ''){
        if (IS_POST) {
            $app_cfg = F($this->code.'/cfg');
            $fields = $app_cfg['model'][$table]['field'];
            // print_r($app_cfg['model']['field']);
            $d = array();
            if ($fields) {
                foreach ($fields as $k) {
                    $k['table'] = $table;
                    $d[] = $k;
                }
            }
            $data['success'] = true;
            $data['data'] = $d;
            $data['totalRows'] = count($d);
            $data['curPage'] = 1;
            $this->ajaxReturn($data);
        }
    }

    public function edit($field='',$table = '')
    {
        $app_cfg = F($this->code.'/cfg');
        $vo = $app_cfg['model'][$table]['field'][$field];
        $this->assign('table', $table);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function update($field='',$table = '')
    {
        if (!$field || !$table) {
            $this->error('参数错误1');
        }
        $app_cfg = F($this->code.'/cfg');
        $vo = $app_cfg['model'][$table]['field'][$field];
        if (!$vo) {
            $this->error('参数错误');
        }
        $vo=$_POST;
        $app_cfg['model'][$table]['field'][$field] = $vo;
        F($this->code.'/cfg',$app_cfg);
        $this->success('修改成功');
    }
}