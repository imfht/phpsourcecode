<?php

class newsAction extends frontendAction {

    public function _initialize() {
        parent::_initialize();
        //友情链接
        $maplink['status'] = 1;
        $flink= D('flink')->where($maplink)->order('ordid')->select();
        foreach ($flink as $k => $v) {
            if (strpos($flink[$k]['url'], 'http')===false) {
                $flink[$k]['url']='http://'.$flink[$k]['url'];
            }
        }
        $this->assign('flink', $flink);
        
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
        //获取热门文章 : 按浏览量倒叙排列
        $hotnews = D('article')->where(array('status'=>'1'))->order('hits desc')->select();
        $this->assign('hotnews',$hotnews);
        
        $this->assign('cate', $cate); //所有分类，首页只取前八个大类
    }
    

    // 首页控制器
    public function index() {
        // 取seo信息
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.news'));
        $this->assign('seoconfig',$seoconfig);

        //获取新闻栏目id
        $id=$this->_request('id','trim');

        //获取文档分类
        $newscate = D('article_cate')->where(array('status'=>'1'))->select();

        // 获取文章
        $count = D('article')->where(array('status'=>'1'))->count();
        $page=new Page($count,15);
        $show=$page->show();
        
        if ($id != '') {
            $newslist = D('article')->where(array('status'=>'1' , 'cateid'=>$id))->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else {
            $newslist = D('article')->where(array('status'=>'1'))->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        }
        
        //自动获取第一张图片为缩略图
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]))[\'|\"].*?[\/]?>/"; 
        foreach ($newslist as $k => $v) {
            preg_match_all($pattern,$v['content'],$matchContent); 
            $newslist[$k]['img'] = $matchContent[1][0];
            //自动获取新闻简介
            if ($newslist[$k]['intro'] == '') {
                $newslist[$k]['intro'] = strip_tags(mb_substr($v['content'], 0, 130,'utf-8'));
            }
        }
        
        $this->assign('id',$id); 
        $this->assign('newscate',$newscate); 
        $this->assign('newslist',$newslist); 
        $this->assign('page',$show);
        $this->display();
       
    }
    // 内容页面
    public function content() {
        // 取seo信息
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.newscon'));
        $this->assign('seoconfig',$seoconfig);

        //获取新闻id
        $id=$this->_request('id','trim');

        // 每次访问加1浏览
        D('article')->where(array('id' => $id))->setInc('hits',1);

        //获取新闻内容
        $info = D('article')->find($id);

        //上一篇
        $front=M('article')->where("id>".$id)->order('id asc')->limit('1')->find();
        $this->assign('front',$front);
        //下一篇
        $after=M('article')->where("id<".$id)->order('id desc')->limit('1')->find();
        $this->assign('after',$after);
        $this->assign('id',$id);
        $this->assign('info',$info);
        $this->display();
         
    }

     
}