<?php
namespace app\home\controller;
use think\Db;

class Article extends Site{
    public function index(){
        $class_id=input('class_id');
        //获取栏目信息
        if (!empty($class_id)){
            $category_info=model('CategoryArticle')->getInfo($class_id);
        }else{
            return $this->error404();
        }
        //位置导航
        $crumb = model('Category')->loadCrumb($class_id);
        //设置查询条件
        $where=array();
        if ($category_info['type'] == 0) {
            $class_ids = model('Category')->getSubClassId($class_id);
        }
        if(empty($class_ids)){
            $class_ids = $category_info['class_id'];
        }
        $where['A.status'] = 1;
        $where['C.class_id'] = ['in',$class_ids];
        //分页参数
        $size = intval($category_info['page']);
        if (empty($size)) {
            $listRows = 20;
        } else {
            $listRows = $size;
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['class_id'] = $class_id;
        //查询内容数据
        if(!empty($category_info['content_order'])){

            $category_info['content_order'] = $category_info['content_order'].',';
        }
        $list=model('ContentArticle')->loadList($where,$listRows,$category_info['content_order'].'A.time desc,A.content_id desc',$category_info['fieldset_id']);;
        //查询上级栏目信息
        $parent_category_info = model('Category')->getInfo($category_info['parent_id']);
        //获取顶级栏目信息
        $top_category_info = model('Category')->getInfo($crumb[0]['class_id']);
        //MEDIA信息
        $media = $this->getMedia($category_info['name'],$category_info['keywords'],$category_info['description']);
        //模板赋值
        $this->assign('category_info', $category_info);
        $this->assign('parent_category_info', $parent_category_info);
        $this->assign('top_category_info', $top_category_info);
        $this->assign('crumb', $crumb);
        $this->assign('list', $list);
        $this->assign('media', $media);
        $this->assign('pageMaps', $pageMaps);
        $this->assign('_page',$list->render());
        return $this->siteFetch($category_info['class_tpl']);
    }

    public function detail(){
        $content_id=input('content_id');
        //更新访问计数
        $where = array();
        $where['content_id'] = $content_id;
        Db::name('content')->where($where)->setInc('views');
        //获取信息
        if (!empty($content_id)){
            $content_info=model('ContentArticle')->getInfo($content_id);
        }else{
            return $this->error404();
        }
        if ($content_info['status']!=1){
            return $this->error404();
        }
        //获取栏目信息
        $category_info=model('CategoryArticle')->getInfo($content_info['class_id']);
        if (empty($category_info)){
            return $this->error404();
        }
        //位置导航
        $crumb = model('Category')->loadCrumb($content_info['class_id']);
        //查询上级栏目信息
        $parent_category_info = model('Category')->getInfo($category_info['parent_id']);
        //获取顶级栏目信息
        $top_category_info = model('Category')->getInfo($crumb[0]['class_id']);

        //内容处理
        $content_info['content'] = html_out($content_info['content']);
        //扩展模型
        /*if($category_info['fieldset_id']){
            $extInfo = model('FieldsetExpand')->getDataInfo($category_info['fieldset_id'],$content_info);
            $contentInfo = array_merge($content_info , (array)$extInfo);
        }*/
        //MEDIA信息
        $media = $this->getMedia($content_info['title'],$content_info['keywords'],$content_info['description']);
        //模板赋值
        $this->assign('content_info', $content_info);
        $this->assign('category_info', $category_info);
        $this->assign('parent_category_info', $parent_category_info);
        $this->assign('top_category_info', $top_category_info);
        $this->assign('crumb', $crumb);
        $this->assign('media', $media);
        if($content_info['tpl']){
            return $this->siteFetch($content_info['tpl']);
        }else{
            return $this->siteFetch($category_info['content_tpl']);
        }
    }
}
