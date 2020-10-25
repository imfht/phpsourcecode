<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:33
 */

namespace Home\Controller;


class ArticleController extends CommonController
{

    public function index()
    {
        $categoryService = D('Category', 'Service');
        session("cate_id",null);
        $where = null;
        $cate_id = $_GET["category_id"];
        if(!empty($cate_id)){
           //$_SESSION["cate_id"] = $cate_id;
            $category = $categoryService->getById($cate_id);
            if(!empty($category['relation_model'])&&$category['relation_model']!=='Article'){
                $this->redirect($category['relation_model'].'/index');
            }
            $this->assign('category', $category);
            session("cate_id",$cate_id);
            $where ="category_id in (select id from qt_category where id = ".$cate_id." or pid = ".$cate_id.") ";

        }
        $title = $_GET["title"];
        if(!empty($title)){
            if(!empty($where)){
                $where .="and ";
            }
            $where .="title like '%".$title."%' ";
            $this->assign('title', $title);
        }
        $result = $this->getPagination('Article',$where,null,"public_time desc");
        $this->assign('articles', $result['data']);
        $this->assign('rows_count', $result['total_rows']);
        $this->assign('page', $result['show']);
        //是否是单页面类型
        if($categoryService->isSinglePage($cate_id)){
            $isHavedOnlyOne =  D('Article', 'Service')->isHavedOnlyOne($cate_id);
            $this->assign('isHavedOnlyOne', $isHavedOnlyOne);
        }
        $this->display();
    }

    public function add()
    {
        $this->assign('options', D('Article', 'Service')->genCategorySelectOptions(session("cate_id")));
        $this->display();
    }

    public function edit()
    {
        $articleService = D('Article', 'Service');
        if (!isset($_GET['id']) || !$articleService->existById($_GET['id'])) {
            return $this->error('需要编辑的文章信息不存在！');
        }
        $article = $articleService->getById($_GET['id']);
        $this->assign('article', $article);
        $this->assign('options', $articleService->genCategorySelectOptions($article['category_id'],true));
        $this->display();
    }

    /**
     * 更新文章
     * @return
     */
    public function update() {
        $articleService = D('Article', 'Service');
        $article = $_POST['article'];
        if (!isset($article) || !$articleService->existById($article['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        $this->isSlider($articleService,$article);
        $article = $this->setArticleImage($articleService,$article);
        $result = $articleService->update($article);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        return $this->successReturn('更新文章信息成功！', $this->returnUrl());
    }


    /**
     * 创建文章
     * @return
     */
    public function create() {
        $article = $_POST['article'];
        if (!isset($article)) {
            return $this->errorReturn('无效的操作！');
        }
        $articleService = D('Article', 'Service');
        $this->isSlider($articleService,$article);
        $article = $this->setArticleImage($articleService,$article);
        $result = $articleService->add($article);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        return $this->successReturn('添加文章成功！', $this->returnUrl());
    }

    /**
     * 删除文章
     * @return
     */
    public function delete() {
        $articleService = D('Article', 'Service');
        if (!isset($_GET['id']) || !$articleService->existById($_GET['id'])) {
            return $this->error('需要删除的文章信息不存在！');
        }
        $result = D('Article', 'Service')->delete($_GET['id']);
        if (false === $result['status']) {
            return $this->errorReturn('系统出错了！');
        }
        $this->successReturn("删除文章成功！");
    }

    private function returnUrl(){
        $url = U('Article/index');
        $cate_id = session("cate_id");
        if(!empty($cate_id)){
            $url = U('Article/index',array('category_id' => $cate_id));
        }
        return $url;
    }

    //选择幻灯片
    private function isSlider($articleService,$article){
        $is_slide = $article['is_slide'];
        if($is_slide=="1"){//判断是否有图片
            if(!$articleService->existImages($article['content'])){
                return $this->errorReturn('文章内容中没有图片可以用作首页幻灯片！');
            }
        }
    }

    //判断内容是否有图片
    private function setArticleImage($articleService,$article){
        if($articleService->existImages($article['content'])){
            $article['has_img'] = 1;
        }else{
            $article['has_img'] = 2;
        }
        return $article;
    }

} 