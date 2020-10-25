<?php
/**
 * Created by PhpStorm.
 * User: 11093
 * Date: 2019/2/26
 * Time: 10:18
 */

namespace app\home\controller;

use think\Db;
class Tags extends Common{
    public function index(){
        @$keyword=!empty(input('keyword')) ? input('keyword') : "";
        Db::name('tags')->where('name',$keyword)->setInc('hits');
        $tag = Db::name('tags')->where('name',$keyword)->find();
        $list = Db::name('article_tags')->alias('pt')
            ->join('article p', 'pt.article_id = p.id', 'left')
            ->join('category c', 'p.catid = c.id', 'left')
            ->field('p.*,c.catname,c.catdir')
            ->where('pt.tag_id',$tag['id'])
            ->order('p.createtime desc')
            ->paginate(10)->each(function($item, $key){
                $item['time'] = toDate($item['createtime']);
                $item['url'] = url('home/'.$item['catdir'].'/info',array('id'=>$item['id'],'catId'=>$item['catid']));
                if(isset($item['thumb'])){
                    $item['thumb'] = $item['thumb']?$item['thumb']:'/static/home/images/logo.png';
                }else{
                    $item['thumb'] = '/static/home/images/logo.png';
                }
                $item['title_style'] = isset($item['title_style'])?isset($item['title_style']):'';
                return $item;
            });
        $page = $list->render();
        $list = $list->toArray();
        $this->assign('page',$page);
        $this->assign('lists',$list['data']);
        $this->assign('title','tag-'.$keyword);
        return $this->fetch();
    }
}