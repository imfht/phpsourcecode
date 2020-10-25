<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\Articles as M;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 新闻控制器
 */
class News extends Base{
	/**
	 * 列表查询
	 */
    public function view(){
        $m = new M();
        $data = $m->getChildInfos();
        $catId = $data['0']['catId'];
        $articleId = (int)input('articleId');
        $this->assign('articleId',$articleId);
        $this->assign('catInfo',$data);
        $this->assign('catId',$catId);
        return $this->fetch('news_list');
    }
    /**
    * 获取商城快讯列表
    */
    public function getNewsList(){
    	$m = new M();
    	$data = $m->getArticles();
    	foreach($data['data'] as $k=>$v){
    		$data['data'][$k]['articleContent'] = strip_tags(html_entity_decode($v['articleContent']));
            $data['data'][$k]['createTime'] = date('Y-m-d',strtotime($v['createTime']));
            $data['data'][$k]['coverImg'] = str_replace("_thumb.", ".",  $v['coverImg']);
    	}
    	return $data;
    }
    /**
    * 查看详情
    */
    public function getNews(){
    	$m = new M();
    	$data = $m->getNewsById();
        $data['articleContent']=htmlspecialchars_decode($data['articleContent']);
        $data['createTime'] = date('Y-m-d',strtotime($data['createTime']));
        $this->assign('data',$data);
        return $this->fetch('news_detail');
    }
    /**
     * 点赞
     */
    public function like(){
        $m = new M();
        $data = $m->like();
        return $data;
    }
}
