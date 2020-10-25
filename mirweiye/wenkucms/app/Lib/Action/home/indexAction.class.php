<?php
//
class indexAction extends frontendAction {
    public function _initialize() {
        parent::_initialize();
        $seoconfig = $this->_config_seo(C('wkcms_seo_config.index'));
        $this->assign('seoconfig', $seoconfig);
    }
    public function index() {
        // 获取已登录后的用户信息
        global $userinfo;
        $uid = $userinfo['uid'];
        $info = D('user_scoresum')->where(array('uid' => $uid))->find();
        $info['system'] = $info['login'] + $info['register'];
        $this->assign('info', $info);

        // 获取分类
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

        //获取最新新闻列表
        $newsmaps['status'] = 1;
        $newslist = D('article')->where($newsmaps)->limit('6')->order('id desc')->select();

        foreach ($newslist as $key => $value) {
            $newslist[$key]['catename'] = D('article_cate')->where(array('id'=>$value['cateid']))->getField('name');
            $newslist[$key]['cateid'] = D('article_cate')->where(array('id'=>$value['cateid']))->getField('id');

            //获取文章内容第一张图片为缩略图
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]))[\'|\"].*?[\/]?>/"; 
            preg_match_all($pattern,$value['content'],$matchContent); 
            $newslist[$key]['img'] = $matchContent[1][0];
             
        }
        $this->assign('newslist', $newslist);

        //热门标签
        $mod = D('tag');
        $taglist = $this->_list(0, $mod, $map, 'count', 'desc', '', '');
        $this->assign('taglist', $taglist['list']);
      
        //获取最热新闻列表
        $newsmaps['status'] = 1;
        $newshits = D('article')->where($newsmaps)->limit('6')->order('hits desc')->select();

        foreach ($newshits as $key => $value) {
            $newshits[$key]['catename'] = D('article_cate')->where(array('id'=>$value['cateid']))->getField('name');
            $newshits[$key]['cateid'] = D('article_cate')->where(array('id'=>$value['cateid']))->getField('id');

            //获取文章内容第一张图片为缩略图
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]))[\'|\"].*?[\/]?>/"; 
            preg_match_all($pattern,$value['content'],$matchContent); 
            $newshits[$key]['img'] = $matchContent[1][0];
             
        }
         
        $this->assign('newshits', $newshits);


        $this->assign('cate', $cate); //所有分类，首页只取前八个大类
        

        $totaldocnum = D('doc_con')->count('id');
        $this->assign('totaldocnum', $totaldocnum);

        //友情链接
        $maplink['status'] = 1;
        $flink= D('flink')->where($maplink)->order('ordid')->select();
        foreach ($flink as $k => $v) {
            if (strpos($flink[$k]['url'], 'http')===false) {
                $flink[$k]['url']='http://'.$flink[$k]['url'];
            }
        }
        $this->assign('flink', $flink);

        //首页推荐专辑
        $tjzj=D('zj')->where(array('zhiding'=>'1','status'=>'1'))->select();
        //获取专辑下3个最新文档
        foreach ($tjzj as $k => $v) {
           $tjzj[$k]['zhuanji'] = D('doc_con')->where(array('zhuanji'=> $tjzj[$k]['id'],'convert_status'=> 2))->limit('3')->order('add_time DESC')->select();
        }
        $this->assign('tjzj', $tjzj);

        //重写首页最新文档
        $docMap['status'] = 2;
        $docMap['convert_status'] = 2;
        $indexDocList = D('doc_con')->where($docMap)->limit('12')->order('add_time')->select();
        $this->assign('indexDocList', $indexDocList);

        //微信端用到
        $mtotal = D('doc_con')->where($docMap)->count();
        $this->assign('mtotal', $mtotal);

        //重写首页热门文档
        $dochitsMap['status'] = 2;
        $dochitsMap['convert_status'] = 2;
        $indexhitsDocList = D('doc_con')->where($dochitsMap)->limit('12')->order('hits desc')->select();
        $this->assign('indexhitsDocList', $indexhitsDocList);
         
        # 获取幻灯片
        $mapslide['status'] = 1;
        $slide = D('slide')->where($mapslide)->limit('6')->order('ordid')->select();
        $this->assign('slide', $slide);


        $this->display();
    }

    //刷新请求数据
    public function data($start)
    {
        $start = $this->_request('start','trim');
        
        $mobileMap['status'] = 2;
        $mobileMap['convert_status'] = 2;
        $list = D('doc_con')->where($mobileMap)->order('id')->limit($start,15)->select();
        foreach ($list as $k => $v) {
           $list[$k]['uname'] = getusername($v['uid']);
           $list[$k]['url'] = U("doc/doccon",array("id"=>$v['id']));
           $list[$k]['add_time'] = fdate($v['add_time']);
        }
        // print_r($list);
        $this->ajaxReturn(1, $list);
        // return (array( 'result'=>$list,'status'=>1, 'msg'=>'获取成功！'));
    }
 
}
