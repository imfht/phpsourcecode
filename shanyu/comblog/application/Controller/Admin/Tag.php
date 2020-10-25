<?php
namespace App\Controller\Admin;

use Kernel\Loader;
use Kernel\Db;

use App\Controller\Controller;
use App\Model\Article as ArticleModel;
use App\Model\ArticleCategory as CategoryModel;
use App\Model\Tag as TagModel;

class Tag extends Controller
{

    public function index()
    {
        
        $query = "SELECT * FROM tag WHERE 1=1 ORDER BY id DESC";
        $list= Db::instance()->query($query)->fetchAll();

        $this->assign(compact('list'));
        return $this->fetch('tag_index');
    }
    public function create()
    {
        return $this->fetch('tag_create');
    }
    public function store()
    {
        $data = array_filter($_POST);
        $insert_keys = implode(',',array_keys($data));
        $insert_values = "'".implode("','",array_values($data))."'";

        $query = "INSERT INTO tag ({$insert_keys}) VALUES ({$insert_values})";
        $status= Db::instance()->exec($query);

        if($status){
            return $this->success('添加成功','/admin?c=Tag&a=index');
        }else{
            return $this->error('添加失败');
        }
    }

    public function edit()
    {
        $id=intval($_GET['id']);

        $query = "SELECT * FROM tag WHERE id={$id}";
        $info = Db::instance()->query($query)->fetch();

        $this->assign(compact('info'));
        return $this->fetch('tag_edit');
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

        $query = "UPDATE tag SET {$update} WHERE id={$id}";
        $status= Db::instance()->exec($query);

        if($status){
            return $this->success('修改成功','/admin?c=Tag&a=index');
        }else{
            return $this->error('修改失败');
        }
    }
    public function destroy()
    {
        $id = intval($_GET['id']);

        $query = "DELETE FROM tag WHERE id={$id}";
        $status= Db::instance()->exec($query);

        if($status){
            return $this->success('删除成功','/admin?c=Tag&a=index');
        }else{
            return $this->error('删除失败');
        }
    }

}