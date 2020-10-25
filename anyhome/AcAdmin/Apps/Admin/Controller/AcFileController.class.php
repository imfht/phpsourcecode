<?php
namespace Admin\Controller;
use Think\Controller;
class AcFileController extends CommonController {
    public function index($curPage =1,$pageSize = 10){
        if (IS_POST) {
            $m = D('ApiCloud');
            $map['class'] = 'file';

            $ret = $m->getPage($map,$curPage,$pageSize);
            if (!$ret){
                $ret['volist'] = '';
                $ret['count'] = 0;
            } 
            $data['success'] = true;
            $data['data'] = $ret['volist'];
            $data['totalRows'] = $ret['count'];
            $data['curPage'] = $curPage;
            $this->ajaxReturn($data);
        }

        $tpl = T($this->con.'/index');
        $tpl = str_replace("./", "", $tpl);

        if (!file_exists($tpl)) {
            $this->display('Common/index');
        }else{
            $this->display();
        }
    }

    public function delete($id ='')
    {
        $m = D('ApiCloud');
        $map['class'] = 'file';
        $ret = $m->where($map)->delete($id);
        if ($ret !== FALSE) {
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}