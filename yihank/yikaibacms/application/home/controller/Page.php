<?php
namespace app\home\controller;
class Page extends Site
{
    public function index(){
        $class_id=input('class_id');
        //获取栏目信息
        if (!empty($class_id)){
            $category_info=model('CategoryPage')->getInfo($class_id);
        }else{
            return $this->error404();
        }
        if (empty($category_info)){
            return $this->error404();
        }
        //位置导航
        $crumb = model('Category')->loadCrumb($class_id);
        //内容处理
        $category_info['content'] = html_out($category_info['content']);
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
        $this->assign('media', $media);
        return $this->siteFetch($category_info['class_tpl']);
    }
}
