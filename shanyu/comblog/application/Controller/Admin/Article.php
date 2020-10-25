<?php
namespace App\Controller\Admin;

use Kernel\Loader;
use Kernel\Db;

use App\Controller\Controller;
use App\Model\Article as ArticleModel;
use App\Model\ArticleCategory as CategoryModel;
use App\Model\Tag as TagModel;

class Article extends Controller
{
    public function index()
    {
        $where='';
        $search_title = isset($_POST['title'])?$_POST['title']:'';
        if($search_title){
            $where.="AND title LIKE '%{$search_title}%' ";
        }
        
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $articles=Loader::singleton(ArticleModel::class)->getListPage($where,10,$page,'/admin?c=Article&a=index&page={page}');

        $this->assign(compact('articles'));
        return $this->fetch('article_index');
    }
    public function create()
    {
        $category = Loader::singleton(CategoryModel::class)->getTitles();
        $tag = Loader::singleton(TagModel::class)->getTitles();

        $this->assign(compact('category','tag'));
        return $this->fetch('article_create');
    }
    public function store()
    {
        $data = array_filter($_POST);
        if(isset($data['tags'])){
            $tags = $data['tags'];
            unset($data['tags']);
        }else{
            $tags = [];
        }

        if(!empty($data['markdown'])){
            $content = $this->parseMarkdown($data['markdown']);
            $data['content'] = addslashes(htmlspecialchars($content));
            
            $data['markdown'] = addslashes($data['markdown']);

            if(empty($data['description'])){
                $description = strip_tags($content);
                if(mb_strlen($description,'utf8') > 100){
                    $description=mb_substr($description, 0,100).'...';
                }
                $data['description']=addslashes($description);
            }
        }

        $insert_keys = implode(',',array_keys($data));
        $insert_values = "'".implode("','",array_values($data))."'";
        $query = "INSERT INTO article ({$insert_keys}) VALUES ({$insert_values})";
        $status= Db::instance()->exec($query);
        $insert_id=Db::instance()->lastInsertId();

        //添加关联的阅读量
        $query = "INSERT INTO article_view (article_id,view) VALUES ({$insert_id},0)";
        $status= Db::instance()->exec($query);

        $status_tag=$this->saveTag($insert_id, $tags);

        if($status || $status_tag){
            return $this->success('添加成功','/admin?c=Article&a=index');
        }else{
            return $this->error('添加失败');
        }
    }

    protected function saveTag($article_id, $tag_ids=[])
    {
        $exec = "DELETE FROM article_tag WHERE article_id = {$article_id}";
        $status_del = Db::instance()->exec($exec);

        $status_ins = false;
        if(!empty($tag_ids)){
            $insert_data_values = array_fill_keys($tag_ids, $article_id);
            $insert_data = '';
            foreach ($insert_data_values as $k => $v) {
                $insert_data .="({$k},{$v}),";
            }
            $insert_data = rtrim($insert_data,',');

            $exec = "INSERT INTO article_tag (tag_id,article_id) VALUES {$insert_data}";
            $status_ins = Db::instance()->exec($exec);
        }
        return ($status_ins || $status_del);
    }
    protected function parseMarkdown($text = '')
    {
        $markdown=new \Parsedown();
        $content = $markdown->text($text);
        return $content;
    }

    public function edit()
    {
        $id=$_GET['id'];
        $query = "SELECT * FROM article WHERE id={$id} LIMIT 1";
        $info = Db::instance()->query($query)->fetch();
        $info['markdown'] = stripslashes($info['markdown']);
        $info_tags = $this->getTag($info['id']);

        $category = Loader::singleton(CategoryModel::class)->getTitles();
        $tag = Loader::singleton(TagModel::class)->getTitles();

        $this->assign(compact('info','info_tags','category','tag'));
        return $this->fetch('article_edit');
    }
    protected function getTag($article_id)
    {
        $query = "SELECT tag_id FROM article_tag WHERE article_id={$article_id}";
        $info = Db::instance()->query($query)->fetchAll();

        return array_column($info,'tag_id');
    }
    public function update()
    {
        $id=$_GET['id'];
        $data = array_filter($_POST);
        $model = Db::instance();

        if(isset($data['tags'])){
            $tags = $data['tags'];
            unset($data['tags']);
        }else{
            $tags = [];
        }
        if(!empty($data['markdown'])){
            $content = $this->parseMarkdown($data['markdown']);
            $data['content'] = addslashes(htmlspecialchars($content));
            
            $data['markdown'] = addslashes($data['markdown']);

            if(empty($data['description'])){
                $description = strip_tags($content);
                if(mb_strlen($description,'utf8') > 150){
                    $description=mb_substr($description, 0,150).'...';
                }
                $data['description']=addslashes($description);
            }
        }

        $update = '';
        foreach ($data as $k => $v) {
            if(is_numeric($v)){
                $update .= "{$k} = {$v},";
            }else{
                $update .= "{$k} = '{$v}',";
            }
        }
        $update = trim($update,',');

        
        $query = "UPDATE article SET {$update} WHERE id={$id}";
        $status= $model->exec($query);

        $status_tag=$this->saveTag($id, $tags);

        if($status || $status_tag){
            return $this->success('修改成功','/admin?c=Article&a=index');
        }else{
            return $this->error('修改失败');
        }
    }
    public function destroy()
    {
        $id = $_GET['id'];
        $model = Loader::singleton(ArticleModel::class)->db();
        $query = "DELETE FROM article WHERE id={$id}";
        $status= $model->exec($query);

        if($status){
            return $this->success('删除成功','/admin?c=Article&a=index');
        }else{
            return $this->error('删除失败');
        }
    }
    // public function show()
    // {
    //     return $this->fetch('article_show');
    // }
}