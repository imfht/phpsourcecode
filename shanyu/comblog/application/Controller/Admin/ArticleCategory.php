<?php
namespace App\Controller\Admin;

use Kernel\Loader;
use Kernel\Db;

use App\Controller\Controller;
use App\Model\Article as ArticleModel;
use App\Model\ArticleCategory as CategoryModel;

class ArticleCategory extends Controller
{
    public function index()
    {
        
        $query = "SELECT * FROM article_category WHERE 1=1 ORDER BY id DESC";
        $list= Db::instance()->query($query)->fetchAll();

        $this->assign(compact('list'));
        return $this->fetch('article_category_index');
    }
    public function create()
    {
        $category = Loader::singleton(CategoryModel::class)->getTitles();
        $this->assign(compact('category'));
        return $this->fetch('article_category_create');
    }
    public function store()
    {
        $data = array_filter($_POST);
        $insert_keys = implode(',',array_keys($data));
        $insert_values = "'".implode("','",array_values($data))."'";

        $query = "INSERT INTO article_category ({$insert_keys}) VALUES ({$insert_values})";
        $status= Db::instance()->exec($query);

        if($status){
            return $this->success('添加成功','/admin?c=ArticleCategory&a=index');
        }else{
            return $this->error('添加失败');
        }
    }

    public function edit()
    {
        $id=intval($_GET['id']);

        $query = "SELECT * FROM article_category WHERE id={$id}";
        $info = Db::instance()->query($query)->fetch();

        $category = Loader::singleton(CategoryModel::class)->getTitles();

        $this->assign(compact('info','category'));
        return $this->fetch('article_category_edit');
    }
    public function update()
    {
        $id=$_GET['id'];
        $data = array_filter($_POST);
        $update = '';
        foreach ($data as $k => $v) {
            if(is_numeric($v)){
                $update .= "{$k} = {$v},";
            }else{
                $update .= "{$k} = '{$v}',";
            }
        }
        $update = trim($update,',');

        $query = "UPDATE article_category SET {$update} WHERE id={$id}";
        $status= Db::instance()->exec($query);

        if($status){
            return $this->success('修改成功','/admin?c=ArticleCategory&a=index');
        }else{
            return $this->error('修改失败');
        }
    }
    public function destroy()
    {
        $id = intval($_GET['id']);

        $query = "DELETE FROM article_category WHERE id={$id}";
        $status= Db::instance()->exec($query);

        if($status){
            return $this->success('删除成功','/admin?c=ArticleCategory&a=index');
        }else{
            return $this->error('删除失败');
        }
    }

}