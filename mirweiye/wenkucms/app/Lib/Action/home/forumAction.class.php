<?php
//
class forumAction extends frontendAction {
     
    public function _initialize() {
    	parent::_initialize();
    	global $userinfo;
        $userinfo = $this->visitor->info;
        $this->assign('uid', $userinfo['uid']);
    	 //友情链接
        $maplink['status'] = 1;
        $flink= D('flink')->where($maplink)->order('ordid')->select();
        foreach ($flink as $k => $v) {
            if (strpos($flink[$k]['url'], 'http')===false) {
                $flink[$k]['url']='http://'.$flink[$k]['url'];
            }
        }
        $this->assign('flink', $flink);

        //发帖总榜
        $fBang =  D('forum')->field('uid,count(uid)')->group('uid')->limit('12')->select();
        $this->assign('fBang', $fBang);

        //本周热议
        $fBang =  D('forum')->field('uid,count(uid)')->group('uid')->limit('12')->select();
        $this->assign('fBang', $fBang);

         //本月热帖
        $start = date('Y-m-01 00:00:00'); 
        $end = date('Y-m-d H:i:s');
        $map['add_time']  = array('between',$start,$end);
        $zhoumap['status']  = array('gt',0);
        $zhoulist = D('forum')->where($zhoumap)->order('hits desc')->limit('10')->select();
        $this->assign('zhoulist', $zhoulist);

        //获取论坛版块
        $maplink['status'] = 1;
        $forum_cate= D('forum_cate')->where($maplink)->order('ordid')->select();
        $this->assign('forum_cate', $forum_cate);

        //来路
        $ret_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __APP__;
        $this->assign('ret_url', $ret_url);
        if ($refer) {
            $refer = base64_decode($refer);
            $ret_url = $refer;
        }

    }

    public function index() {
        // 自定义seo信息
        $seoconfig['title'] = "论坛 -" . C('wkcms_site_name');
        $seoconfig['description'] = C('wkcms_site_name') . "论坛";
        $seoconfig['keywords'] =  C('wkcms_site_name') . "论坛";
        $this->assign('seoconfig', $seoconfig);

        //获得板块ID
        $cid=$this->_request('cid','trim');
        $this->assign('cid',$cid);

        //置顶帖子
        $topmap['top']  = 1;
        $topmap['status']  = array('gt',0);
        $toplist= D('forum')->where($topmap)->order('id ASC')->select();
        foreach ($toplist as $k =>$v){
            $toplist[$k]['comcount'] = comcount($v['id'],2);
        }
        $this->assign('toplist', $toplist);

        // 帖子列表
        $ord = 'add_time desc';
        if ($this->_request('ord','trim')) {
            $this->assign('page',$show);
           if ($this->_request('ord','trim') == 'hottest') {
               $ord = 'hits desc';
           }
           if ($this->_request('ord','trim') == 'newest') {
               $ord = 'add_time desc';
           }
           if ($this->_request('ord','trim') == '') {
               $ord = 'add_time desc';
           }
        }
        $this->assign('ord', $ord);
        $forummap['status']  = array('eq',1);
        $forummap['top']  = array('eq',0);
        $count = D('forum')->where($forummap)->count();
        $page=new Page($count,20);
        $show=$page->show();
        $forumlist = D('forum')->where($forummap)->order($ord)->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($forumlist as $k =>$v){
            $forumlist[$k]['comcount'] = comcount($v['id'],2);
        }
        $this->assign('forumlist', $forumlist);
        $this->assign('page',$show);
        

        $this->display();
    }
    //列表
    public function forumdisplay() {
        //获得板块ID
        $cid=$this->_request('cid','trim');
        $this->assign('cid',$cid);

        // 自定义seo信息
        $seonav = D('forum_cate')->find($cid);;
        $seoconfig['title'] = $seonav['name'] . " - 论坛 - " . C('wkcms_site_name');
        $seoconfig['description'] = C('wkcms_site_name') . "论坛";
        $seoconfig['keywords'] =  C('wkcms_site_name') . "论坛";
        $this->assign('seoconfig', $seoconfig);
        
        // 帖子列表
        $ord = 'add_time desc';
        if ($this->_request('ord','trim')) {
            $this->assign('page',$show);
           if ($this->_request('ord','trim') == 'hottest') {
               $ord = 'hits desc';
           }
           if ($this->_request('ord','trim') == 'newest') {
               $ord = 'add_time desc';
           }
           if ($this->_request('ord','trim') == '') {
               $ord = 'add_time desc';
           }
        }
        $this->assign('ord', $ord);
        $forummap['status']  = array('gt',0);
        $forummap['cateid']  = $cid;
        $count = D('forum')->where($forummap)->count();
        $page=new Page($count,20);
        $show=$page->show();
        $forumlist = D('forum')->where($forummap)->order($ord)->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($forumlist as $k =>$v){
            $forumlist[$k]['comcount'] = comcount($v['id'],2);
        }
        $this->assign('forumlist', $forumlist);
        $this->assign('page',$show);
        

        $this->display();

    }

    // 内容页面
    public function viewthread() {
        global $userinfo;
        $userinfo = $this->visitor->info;
        $this->assign('uid', $userinfo['uid']);

        //获得板块ID
        $cid=$this->_request('cid','trim');
        $this->assign('cid',$cid);
       
        //获取id
        $id=$this->_request('id','trim');

        //评论数量
        $mapraty['typeid']=2;
        $mapraty['itemid']=$id;
        $commentcount= D('comment')->where($mapraty)->count();
        $this->assign('commentcount',$commentcount);

        // 每次访问加1浏览
        D('forum')->where(array('id' => $id))->setInc('hits',1);

        $commentdata=R('home/common/comment',array($id,2, 'caina','desc'));
        $this->assign('commentdata',$commentdata);

        //获取内容
        $info = D('forum')->find($id);
        if (!$info) {
            $this->redirect('forum/index');
        }
     
        // 自定义seo信息
        $seoconfig['title'] = $info['title'] . " - 论坛 - " . C('wkcms_site_name');
        $seoconfig['description'] = C('wkcms_site_name') . "论坛";
        $seoconfig['keywords'] =  C('wkcms_site_name') . "论坛";
        $this->assign('seoconfig', $seoconfig);


        $this->assign('id',$id);
        $this->assign('info',$info);
        $this->display();
         
    }

    //管理员操作
    public function del($id) {
        global $userinfo;
        $userinfo = $this->visitor->info;
        if (getisadmin($userinfo['uid']) == true) {

            $info = D('forum')->where(array('id'=>$id))->find();
            if (!$info) {
                $this->error('操作失败！');
            }
            if ($info && $info['score'] > 0 && $info['caina'] > 0) {
                $this->error('操作失败！');
            }
            if (D('forum')->delete($id)) {
                if ($info && $info['score'] > 0) {
                    # 把积分还给用户
                    opuserscore($info['uid'], 1, 'score', $info['score']);
                }
                $this->success(L('删除成功！'));
            }else{
                $this->error('操作失败！');
            }
        }else {
            $this->error('没有权限！');
        }
    }
    public function caina($id,$v) {
        global $userinfo;
        
        # 获取id和v是否存在
        $info = D('forum')->where(array('id'=>$id))->find();
        $rule = false;

        # 权限验证
        if ($info && $userinfo['uid'] == $info['uid'] && $info['score'] > 0) {
            $comment = D('comment')->where(array('id'=>$v))->find();
            if ($comment) {
                # 不能自己给自己打分
                if ($comment['uid'] != $userinfo['uid']) {
                    $rule = true;
                }
                # 测试
                $rule = true;
            }
        }

        if ($rule == true) {
            if (D('forum')->where(array('id'=>$id))->setField('caina',$v)) {
                # 同时增加对方的积分
                opuserscore($comment['uid'], 1, 'score', $info['score']);

                D('comment')->where(array('id'=>$v))->setField('caina',1);
                $this->success(L('更新成功'));
            }else {
                $this->error('操作失败！');
            }
        }else {
            $this->error('没有权限！');
        }
    }
    public function zhiding($id,$v) {
        global $userinfo;
        $userinfo = $this->visitor->info;
        if (getisadmin($userinfo['uid']) == true) {
            if (D('forum')->where(array('id'=>$id))->setField('top',$v)) {
                $this->success(L('更新成功'));
            }else {
                $this->error('操作失败！');
            }
        }else {
            $this->error('没有权限！');
        }
    }
    public function jing($id,$v) {
        global $userinfo;
        $userinfo = $this->visitor->info;
        if (getisadmin($userinfo['uid']) == true) {
            if (D('forum')->where(array('id'=>$id))->setField('jing',$v)) {
                $this->success(L('更新成功'));
            }else {
                $this->error('操作失败！');
            }
        }else {
            $this->error('没有权限！');
        }
    }
    
    
}
