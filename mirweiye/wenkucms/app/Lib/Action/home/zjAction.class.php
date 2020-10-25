<?php

class zjAction extends frontendAction {

    public function _initialize() {
        parent::_initialize();


        //文档分类，自动查询，以便列表页和其他页面调用
        $this->_cate_mod = D('doc_cate');
        $cate = $this->_cate_mod->where(array('pid' => 0, 'status' => 1))->order('ordid')->select();
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
        $this->assign('cate', $cate); //所有分类，首页只取前八个大类
    }
    

    // 首页控制器
    public function index() {
        global $userinfo;
       
        // 取seo信息
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.zj'));
        $this->assign('seoconfig',$seoconfig);

        // 取专辑列表
        $mod=D('zj');
        $where['status']  = '1';
        $count=$mod->where($where)->count();
        $page=new Page($count,20);
        $show=$page->show();

        $search_order = $this->_get('search_order', 'trim');
        if (!$search_order) {
            $search_order = 1;
        }
        $order = 'a.zhiding desc,a.tuijian desc,a.id desc';
        if ($search_order == 3) {
            # 文档数量
            $order = 'count desc, a.id desc';
        } elseif ($search_order == 2) {
            # 时间
            $order = 'a.id desc';
        }

        $sql = 'select a.*, (select count(1) from wk_doc_con where zhuanji = a.id) as count from wk_zj as a where a.status = 1 order by ' . $order . ' limit ' . $page->firstRow.','.$page->listRows;
        $zjlist=$mod->query($sql);

        //$zjlist=$mod->where($where)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
 
        $this->assign('search_order',$search_order);
        $this->assign('zjlist',$zjlist);
        $this->assign('page',$show);
        $this->display();
       
    }
    // 专辑文档页面
    public function zjinfo() {
        // 取seo信息
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.zj'));
        $this->assign('seoconfig',$seoconfig);

        // 获取前台get过来的专辑id
        $id=$this->_request('id','trim');

        // 获取当前专辑信息
        $zjinfo = D('zj')->where(array('id'=>$id))->find();
        
        //获取专辑下文档
        $mod=D('doc_con');
        $where['zhuanji'] = $id;
        $where['status']  = array('gt',0);
        $count=$mod->where($where)->count();
        $page=new Page($count,18);
        $show=$page->show();
        $zjdoc=$mod->where($where)->limit($page->firstRow.','.$page->listRows)->select();

        // 获取专辑下文档浏览总数
        $zjhits = D('doc_con')->where($where)->sum('hits');

        // print_r($zjdoc);
        $this->assign('id',$id);
        $this->assign('zjinfo',$zjinfo);
        $this->assign('zjdoc',$zjdoc);
        $this->assign('page',$show);
        $this->assign('zjhits',$zjhits);
        $this->display();
         
    }
     
}