<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;


class SearchController extends HomeBaseController {

    public function _initialize() {
        parent::_initialize();
        $meta=array(
            'meta_title'=>'搜索',
        );
        $this->assign($meta);
    }

    public function index($key='') {
        if(!$key) $this->error('关键词不能为空');
        $this->assign('keywords',$key);

        $count=M('Article')->where('status=1 AND title like \'%'.$key.'%\'')->count();
        $Page =new \Lib\Page($count,6);
        $Page->url='search/key/'.$key;
        $page =$Page->show();
        $this->assign('page',$page);

        $list_search=M('article a')
            ->join('__CATEGORY__ c ON a.cid=c.id')
            ->where('a.status=1 AND a.title like \'%'.$key.'%\'')
            ->field('a.id,a.cid,a.thumb,a.title,a.keywords,a.description,a.add_time,a.view,c.title as cate_title,c.name as cate_name')
            ->limit('0,6')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.id DESC')
            ->select();

        $this->assign('list_search',$list_search);
        $this->display('index');
    }

    public function month($month=''){
        if(!$month) $this->error('月份不能为空');
        $this->assign('month',$month);

        $count=M('Article')->where('status=1 AND DATE_FORMAT(add_time,\'%Y-%m\')=\''.$month.'\'')->count();

        $Page =new \Lib\Page($count,6);
        $Page->url='search/month/'.$month;
        $page =$Page->show();
        $this->assign('page',$page);

        $list_search=M('article a')
            ->join('__CATEGORY__ c ON a.cid=c.id')
            ->where('a.status=1 AND DATE_FORMAT(add_time,\'%Y-%m\')=\''.$month.'\'')
            ->field('a.id,a.cid,a.thumb,a.title,a.keywords,a.description,a.add_time,a.view,c.title as cate_title,c.name as cate_name')
            ->limit('0,6')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.id DESC')
            ->select();

        $this->assign('list_search',$list_search);
        $this->display('index');
    }

    public function tag($tag=''){
        if(!$tag) $this->error('标签不能为空');
        $this->assign('tag',$tag);
        //增加标签点击量
        M('Tag')->where("id={$tag}")->setInc('view');

        $count=M('Article')->where('status=1 AND FIND_IN_SET('.$tag.',tag)')->count();

        $Page =new \Lib\Page($count,6);
        $Page->url='search/tag/'.$tag;
        $page =$Page->show();
        $this->assign('page',$page);

        $list_search=M('article a')
            ->join('__CATEGORY__ c ON a.cid=c.id')
            ->where('a.status=1 AND FIND_IN_SET('.$tag.',a.tag)')
            ->field('a.id,a.cid,a.thumb,a.title,a.keywords,a.description,a.add_time,a.view,c.title as cate_title,c.name as cate_name')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.id DESC')
            ->select();
        $this->assign('list_search',$list_search);
        $this->display('index');
    }
    	
}
