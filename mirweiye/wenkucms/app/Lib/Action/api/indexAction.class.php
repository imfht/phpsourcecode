<?php
//
class indexAction extends frontendAction {
    public function _initialize() {
        parent::_initialize();
        $seoconfig = $this->_config_seo(C('wkcms_seo_config.index'));
        $this->assign('seoconfig', $seoconfig);
        import("@.ORG.convert.Core");
        $this->convert = new ORG\Convert\Core();
    }

    //首页文档列表
    public function doclist() {
        $page = $this->_request('page','trim');
        $mobileMap['status'] = 2;
        $mobileMap['convert_status'] = 2;
        $list = D('doc_con')->where($mobileMap)->order('id')->limit($page,15)->select();
        foreach ($list as $k => $v) {
           $list[$k]['uname'] = getusername($v['uid']);
           $list[$k]['url'] = U("doc/doccon",array("id"=>$v['id']));
           $list[$k]['add_time'] = fdate($v['add_time']);
           $list[$k]['img'] = docimg($v['model'],$v['id']);
        }
        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);

    }
    // 首页幻灯片
    public function slide() {
        $mapslide['status'] = 1;
        $list = D('slide')->where($mapslide)->limit('6')->order('ordid')->select();
        foreach ($list as $k => $v) {
           $list[$k]['img'] = C('wkcms_site_url').upload($v['img'], 'slide');
        }
        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);
    }

    // 搜索
    public function search() {
        $keyword = $this->_request('keyword','trim');
        $page = $this->_request('page','trim');
        $mobileMap['status'] = 2;
        $mobileMap['convert_status'] = 2;
        $mobileMap['title'] = array('like', '%'.$keyword.'%');;
        $list = D('doc_con')->where($mobileMap)->order('id')->limit($page,15)->select();
        foreach ($list as $k => $v) {
           $list[$k]['uname'] = getusername($v['uid']);
           $list[$k]['url'] = U("doc/doccon",array("id"=>$v['id']));
           $list[$k]['add_time'] = fdate($v['add_time']);
           $list[$k]['img'] = docimg($v['model'],$v['id']);
        }
        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);
    }
    //热门搜索
    public function tags() {
        //热门标签
     
        $list=D('tag')->order('count desc')->limit(15)->select();
       
        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);
        

    }
    // 专辑列表
    public function zjlist() {
        $page = $this->_request('page','trim');
        $list=D('zj')->where(array('status'=>'1'))->order('id')->limit($page,15)->select();
        foreach ($list as $k => $v) {
           $list[$k]['img'] = C('wkcms_site_url').upload($v['img'], 'zj');
           $list[$k]['add_time'] = fdate($v['add_time']);
        }
        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);
    }

    // 专辑详情
    public function zjinfo() {
        $id = $this->_request('id', 'trim');

        $list=D('zj')->where(array('id'=>$id))->find();
        $list['add_time'] = fdate($list['add_time']);
        $list['doccount'] = doccount($id);
        $list['img'] = C('wkcms_site_url').upload($list['img'], 'zj');

        $where['zhuanji'] = $id;
        $where['status']  = array('gt',0);
        $where['convert_status'] = 2;
        $list['doclist'] = D('doc_con')->where($where)->select();
        foreach ($list['doclist'] as $k => $v) {
           $list['doclist'][$k]['uname'] = getusername($v['uid']);
           $list['doclist'][$k]['url'] = U("doc/doccon",array("id"=>$v['id']));
           $list['doclist'][$k]['add_time'] = fdate($v['add_time']);
           $list['doclist'][$k]['img'] = docimg($v['model'],$v['id']);
        }

        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);
    }

    //分类列表
    public function doccate() {
        $list = D('doc_cate')->where(array('pid' => 0, 'status' => 1))->order('ordid')->select();
        foreach ($list as $key => $value) {
            $mapcate['pid'] = array('eq', $value['id']);
            $mapcate['status'] = 1;
            $list[$key]['tcate'] = D('doc_cate')->where($mapcate)->order('ordid')->select();
            foreach ($list[$key]['tcate'] as $key1 => $value1) {
                $list[$key]['tcate'][$key1]['catecount'] = catecount($value1['id']);
                
            }
        }
      
        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);
     }

     //分类文档列表
    public function catedoclist() {
        $page = $this->_request('page','trim');
        $cateid = $this->_request('cateid','trim');
        $mobileMap['status'] = 2;
        $mobileMap['cateid'] = $cateid;
        $mobileMap['convert_status'] = 2;
        $list = D('doc_con')->where($mobileMap)->order('id')->limit($page,15)->select();
        foreach ($list as $k => $v) {
           $list[$k]['uname'] = getusername($v['uid']);
           $list[$k]['url'] = U("doc/doccon",array("id"=>$v['id']));
           $list[$k]['add_time'] = fdate($v['add_time']);
           $list[$k]['img'] = docimg($v['model'],$v['id']);
        }
        ($list) ? $this->ajaxReturn(1,"获取成功",$list) : $this->ajaxReturn(0,"获取失败",0);
     }

    // 文档详情页
    public function doccon() {

        $view = $this->_request('view');
        
        //点击增加1
        $id = $this->_request('id', 'trim');
        D('doc_con')->where(array('id' => $id))->setInc('hits', 1); 

        //获得文档信息
        global $userinfo;
        $uid = $userinfo['uid'];

        $score = getuserscore($uid);
        $this->assign('score', $score); 

        $map['id'] = $id;
        $info = D('doc_con')->where($map)->find(); 

        if ($view) {
            header('location:' . $this->convert->get($info, true));die;
        }

        //是显示购买还是下载 ;1为下载2为购买
        $download = downorbuy($info['score'], $uid, $info['uid'], $id);
        $this->assign('download', $download); 

        if (!$info) {
            $this->error(L('operation_failure'));
        }
        if ($uid != 1 && $info['status'] < 2) {
            $this->error('该文档已关闭或者未审核');
        }

        $similar = similardoc($id, 10);
        $samecatedoc = samecatedoc($id);
        $mapraty['itemid'] = $id;
        $mapraty['typeid'] = 1;
        $ratydata = D('raty')->where($mapraty)->find();
        $raty = getraty($id, 1); 
        $info['ratystar'] = getratyint($raty['raty']/10);
        $commentcount = D('comment')->where($mapraty)->count(); //评论数量
        $mapratyuser['ratyid'] = $ratydata['id'];
        $ratyuser = D('raty_user')->where($mapratyuser)->select(); //所有用户的评分详细信息
        $info['tags'] = explode(',', $info['tags']);
        foreach ($info['tags'] as $key => $value) {
            $maptag['name'] = $value;
            $tagarr[] = D('tag')->where($maptag)->find();
        } 

        //获得文档的相关信息
        # modify by rabin
        $ipstr = $this->convert->get($info);
        
        $realstrcount = substr_count($ipstr, 'stl_01');
       
        //求得文档总页数，如果没有生成预览，则显示未1页
        if ($download == 2) { //如果需要购买，未生成预览和已经生成预览的都是取第一页
            $expstr = "<div class=\"stl_01 mod reader-page complex reader-page-\">";
            $ipstrsarr = explode($expstr, $ipstr);
            //$ipstr = $ipstrsarr[0] . $expstr . $ipstrsarr[1];
            $strcount = 1;
        } else {
            $strcount = $realstrcount;
            //如果可以随意下载的，则显示页数为所有页数，不限制
            
        }
        $intro = $info['intro'];
        
        # 举报
        $jubao = D('jubao')->where(array('itemid' => $info['id'], 'uid' => $uid, 'typeid' => 1))->find();
        $this->assign('jubao', $jubao); 


        $this->assign('intro', $intro);
        $this->assign('strcount', $strcount);
        $this->assign('realstrcount', $realstrcount);
        $this->assign('ipstr', $ipstr);
        $seoconfig = $this->_config_seo(C('wkcms_seo_config.doccon'));
        $seoconfig['title'] = $info['title'] . '|' . $seoconfig['title'];
        $seoconfig['description'] = $intro . '|' . $seoconfig['description'];
        $this->assign('seoconfig', $seoconfig);
        $this->assign('info', $info);
        $this->assign('raty', $raty);
        $this->assign('tagarr', $tagarr);
        $this->assign('commentcount', $commentcount);

        $this->assign('ratydata', $ratydata);
        $this->assign('ratyuser', $ratyuser);
        $this->assign('similar', $similar);
        $this->assign('uid', $userinfo['uid']);
        //谁下载过
        $downnum = D('itemlog')->where(array('itemid' => $id, 'type' => 1, 'typeid' => 1))->count();
        $tuijiannum = D('itemlog')->where(array('itemid' => $id, 'type' => 3, 'typeid' => 1))->count();
        $xhnum = D('itemlog')->where(array('itemid' => $id, 'type' => 2, 'typeid' => 1))->count();
        if (D('itemlog')->where(array('itemid' => $id, 'type' => 2, 'typeid' => 1, 'uid' => $uid))->find()) {
            $hasxh = 1;
        }
        if (D('itemlog')->where(array('itemid' => $id, 'type' => 3, 'typeid' => 1, 'uid' => $uid))->find()) {
            $hastj = 1;
        }
        $this->assign('hasxh', $hasxh);
        $this->assign('hastj', $hastj);
         
        $this->assign('downnum', $downnum);
        $this->assign('tuijiannum', $tuijiannum);
        $this->assign('xhnum', $xhnum);
         
        
        $mapraty['itemid'] = $id;
        $mapraty['typeid'] = 1;
        
        $this->assign('id', $id);//文档的ID
        
         
        $info['doccon'] = $ipstr;
        $this->ajaxReturn(1,"获取成功",$info); 
        // $this->ajaxReturn(1,"获取成功",$ipstr);


    }

     
 
 
}
