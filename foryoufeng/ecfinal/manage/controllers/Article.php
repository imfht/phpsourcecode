<?php
/**
 * 品牌
 * Created by PhpStorm.
 * User: root
 * Date: 7/15/16
 * Time: 11:12 AM
 */
class Article extends Admin{
    /**
     * 获取数据
     * @return mixed
     */
    public function get_list(){
        //获取分类搜索菜单
        $catname = $this->model->get_article_name();
        $this->assign('catname',$catname);
        return $this->model->all();
    }

    public function add(){
        //获取分类搜索菜单
        $catname = $this->model->get_article_name();
        if($_POST) {
            //获取数据
            $data['title'] = trim(I('title', ''));//文章标题
            $data['article_type'] = trim(I('article_type', '0'));//文章的重要性
            $data['is_open'] = trim(I('is_open', ''));//是否显示
            $data['author'] = trim(I('author', ''));//作者
            $data['author_email'] = trim(I('author_email', ''));//作者Email
            $data['keywords'] = trim(I('keywords', ''));//关键字
            $data['description'] = trim(I('description', ''));//网页描述
            $data['link'] = trim(I('link', ''));//外部链接
            $data['add_time'] = strtotime(I('add_time'));//会议时间
            $data['address'] = trim(I('address', ''));//地区
            $data['xxdz'] = trim(I('xxdz', ''));//详细地址
            $data['description'] = trim(I('description', ''));//网页描述
            $data['cat_id'] = trim(I('cat_id',''));//分类ID
            //验证数据是否存在  标题、作者、分类、关键字
            if(!$data['title']||!$data['author']||!$data['cat_id']||!$data['keywords']){
                $this->message('数据不全');
            }
            $tmp = $this->model->show_name($data['title']);
            if($tmp){
                $this->message('文章已存在');
            }

            //获取图片路径 /uploads/brands/2016-10-19/5807452e398f6.jpg
            $info = $_FILES;
            if ($info) {
                //上传图片有文件，上传文件没有
                if ($info['file_url']['name'] && !$info['file_url1']['name']) {
                    unset($info['file_url1']);
                    $file_url = $this->imgUpload('article_img');
                    $data['file_url'] = $file_url['file_url'];
                    //上传图片没有，上传文件有文件
                } else if (!$info['file_url']['name'] && $info['file_url1']['name']) {
                    unset($info['file_url']);
                    $file_url = $this->imgUpload('article_file');
                    $data['file_url1'] = $file_url['file_url1'];
                    //上传文件和图片都有
                } else if ($info['file_url']['name'] && $info['file_url1']['name']) {
                    $file_url = $this->imgUpload('article_img');
                    $data['file_url'] = $file_url['file_url'];
                    str_replace('article_img','article_file',$file_url['file_url1']);
                    $data['file_url1'] = str_replace('article_img','article_file',$file_url['file_url1']);
                }
            }
            $result = $this->model->add($data);

            if($result){
                $this->redirect("?c=article");
            }else{
                $this->message('添加失败');
            }
        }else{
            $this->assign('catname',$catname);
            $this->display('article/article_form.html');//如果不是POST提交则只加载界面
        }
    }

    public function edit(){
        $catname = $this->model->get_article_name();
        $id = intval(I('article_id',''));//接受ID，用来修改数据
        if($_POST){
            //获取数据
            $data['article_id'] = $id;
            $data['title'] = trim(I('title', ''));//文章标题
            $data['article_type'] = trim(I('article_type', '0'));//文章的重要性
            $data['is_open'] = trim(I('is_open', ''));//是否显示
            $data['author'] = trim(I('author', ''));//作者
            $data['author_email'] = trim(I('author_email', ''));//作者Email
            $data['keywords'] = trim(I('keywords', ''));//关键字
            $data['description'] = trim(I('description', ''));//网页描述
            $data['link'] = trim(I('link', ''));//外部链接
            $data['add_time'] = strtotime(I('add_time'));//会议时间
            $data['address'] = trim(I('address', ''));//地区
            $data['xxdz'] = trim(I('xxdz', ''));//详细地址
            $data['description'] = trim(I('description', ''));//网页描述
            $data['cat_id'] = trim(I('cat_id',''));//分类ID
            //验证数据是否存在  标题、作者、分类、关键字、ID
            if(!$data['title']||!$data['author']||!$data['cat_id']||!$data['keywords']||!$data['article_id']){
                $this->message('数据不全');
            }
            $tmp = $this->model->show_edit_name($data);
            if($tmp){
                $this->message('文章已存在');
            }
            //获取图片路径 /uploads/brands/2016-10-19/5807452e398f6.jpg
            $info = $_FILES;
            if ($info) {
                //上传图片有文件，上传文件没有
                if ($info['file_url']['name'] && !$info['file_url1']['name']) {
                    unset($info['file_url1']);
                    $file_url = $this->imgUpload('article_img');
                    $data['file_url'] = $file_url['file_url'];
                    //上传图片没有，上传文件有文件
                } else if (!$info['file_url']['name'] && $info['file_url1']['name']) {
                    unset($info['file_url']);
                    $file_url = $this->imgUpload('article_file');
                    $data['file_url1'] = $file_url['file_url1'];
                    //上传文件和图片都有
                } else if ($info['file_url']['name'] && $info['file_url1']['name']) {
                    $file_url = $this->imgUpload('article_img');
                    $data['file_url'] = $file_url['file_url'];
                    str_replace('article_img','article_file',$file_url['file_url1']);
                    $data['file_url1'] = str_replace('article_img','article_file',$file_url['file_url1']);
                }
            }
            $result = $this->model->save($data);
            if($result !== false){
                $this->redirect("?c=article");
            }else{
                $this->message('修改失败');
            }
        }else{
            $data=$this->model->show($id);//查找数据库是否存在这条数据
            if(!$data){
                $this->message('没有数据');
            }
            $this->assign('data',    $data);
            $this->assign('catname',$catname);
            $this->display('article/article_form.html');
        }
    }
    //是否显示
    public function is_open(){
        $this->model->is_open();
    }
}
