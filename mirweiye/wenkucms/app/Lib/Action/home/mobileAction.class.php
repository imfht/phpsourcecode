<?php
class mobileAction extends docbaseAction {

    public function _initialize() {
        parent::_initialize();

        $barename = ACTION_NAME;

    }

     public function cate() {
        $cate = D('doc_cate')->where(array('pid' => 0, 'status' => 1))->order('ordid')->select();
        foreach ($cate as $key => $value) {
            $mapcate['pid'] = array('eq', $value['id']);
            $mapcate['status'] = 1;
            $cate[$key]['tcate'] = D('doc_cate')->where($mapcate)->order('ordid')->select();
            foreach ($cate[$key]['tcate'] as $key1 => $value1) {
                $mapcate1['pid'] = array('eq', $value1['id']);
                $mapcate1['status'] = 1;
                $cate[$key]['tcate'][$key1]['scate'] = D('doc_cate')->where($mapcate1)->order('ordid')->select();
            }
        }
        $this->assign('cate', $cate);
        $this->display();
     }

     
}
