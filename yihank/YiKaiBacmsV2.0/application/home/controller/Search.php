<?php
namespace app\home\controller;
class Search extends Site
{
    public function index(){
        $keyword = input('keyword');
        //解析关键词
        $keyword = len($keyword,0,20);
        $keywords = preg_replace ('/\s+/',' ',$keyword);
        $keywords=explode(" ",$keywords);
        if(empty($keywords[0])){
            return $this->error('没有输入关键词！',url('index/index'));
        }
        $where = array();
        $where['A.status'] = 1;
        //获取搜索类型
        $model=input('model',0,'intval');
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['model'] = $model;
        //分页参数
        $size = input('pageNum',0,'intval');
        if (empty($size)) {
            $limit = 20;
        } else {
            $limit = $size;
        }
        $where['A.title']=['like','%'.$keyword.'%'];
        $pageList = model('ContentArticle')->loadList($where, $limit);
        if(!empty($pageList)){
            foreach ($pageList as $key=>$value) {
                $pageList[$key]['curl']=model('Category')->getUrl($value);
                $pageList[$key]['aurl']=model('Content')->getUrl($value);
            }
        }
        //位置导航
        $crumb = array(array('name'=>'文章搜索 - ' . $keyword,'url'=>url('index',$pageMaps)));
        //MEDIA信息
        $media = $this->getMedia('文章搜索 - '.$keyword);
        //模板赋值
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('list',$pageList);
        $this->assign('_page',$pageList->render());
        $this->assign('keyword', $keyword);
        return $this->siteFetch(get_site('tpl_search').'_article');
    }
}
