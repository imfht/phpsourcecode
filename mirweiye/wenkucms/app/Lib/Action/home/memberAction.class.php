<?php
class memberAction extends frontendAction {
    public function _initialize() {
        parent::_initialize();
        global $userinfo;
        $userinfo = $this->visitor->info;
        $this->assign('uid', $userinfo['uid']);
        global $hisuid;
        $hisuid = $this->_get('uid', 'trim');
        if ($hisuid < 1 || $hisuid == '') {
            $this->error('非法操作');
        }
        $this->assign('hisuid', $this->_get('uid', 'trim'));
        if (ACTION_NAME == 'guanzhu') {
            $actionname = 'fensi';
        } else {
            $actionname = ACTION_NAME;
        }
        $this->assign('actionname', $actionname);
        $memberfocusid = getfocusstatus($userinfo['uid'], $hisuid);
        $this->assign('memberfocusid', $memberfocusid);
        //dump(ACTION_NAME);
        $action = $this->_get('action', 'trim');
        $seoconfig = $this->_config_seo(C('wkcms_seo_config.member'));
        $this->assign('seoconfig', $seoconfig);
        $this->_mod1 = D('doc_con');
        $hottag = D('tag')->order('count desc')->select();
        $this->assign('hottag', $hottag); //热门标签
        $tjdoc = tjdoc();
        $this->assign('tjdoc', $tjdoc); //编辑推荐文档
        $this->assign('action', $action);

        //文档分类，自动查询，个人空间页面也可以调用
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
        $this->assign('cate', $cate); //所有分类，首页只取前八个大类
        
    }

    public function mydoclist() {
        global $hisuid;
        $this->assign('hisuid', $hisuid);
         // 他的帖子
        $mod = D('forum');
        $forummap['status']  = array('gt',0);
        $forummap['uid']  = $hisuid;
        $count = $mod->where($forummap)->count();
        $page=new Page($count,20);
        $show=$page->show();
        $myforumlist = $mod->where($forummap)->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($myforumlist as $k =>$v){
            $myforumlist[$k]['comcount'] = comcount($v['id'],2);
        }
        $this->assign('myforumlist', $myforumlist);
        $this->assign('page',$show);
        $this->display();
    }

    public function guanzhu() {
        global $userinfo, $hisuid;
        $uid = $userinfo['uid'];
        $mod = D('focus');
        $map['uid'] = $hisuid;
        $count = $mod->where($map)->count();
        $page = new Page($count, 15);
        $show = $page->show();
        $data = $mod->where($map)->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($data as $key => $value) {
            $data[$key]['focusstatus'] = getfocusstatus($uid, $value['focusuid']);
        }
        $this->assign('data', $data);
        $this->assign('page', $show);
        $this->display();
        $this->display();
    }
    
    public function fensi() {
        global $userinfo, $hisuid;
        $uid = $userinfo['uid'];
        $mod = D('focus');
        $map['focusuid'] = $hisuid;
        $count = $mod->where($map)->count();
        $page = new Page($count, 15);
        $show = $page->show();
        $data = $mod->where($map)->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($data as $key => $value) {
            $data[$key]['focusstatus'] = getfocusstatus($uid, $value['uid']);
        }
        $this->assign('data', $data);
        $this->assign('page', $show);
        $this->display();
    }
    public function xhdoclist() {
        global $hisuid;
        $this->assign('hisuid', $hisuid);
        $this->display();
    }
}
