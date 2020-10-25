<?php
namespace App\Model;

use Kernel\Loader;
use Kernel\Page;

use App\Model\ArticleCategory as CategoryModel;
use App\Model\Tag as TagModel;

class Article extends Model
{
    public function _initialize()
    {

    }
    public function getListPage($where='',$limit=6,$page=1,$url='')
    {
        $offset=($page-1)*$limit;
        $query="SELECT id,cid,title,create_time,description,v.view FROM article LEFT JOIN article_view AS v ON id = v.article_id WHERE 1=1 {$where} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset} /* 获取文章列表 */";
        $data=$this->db()->query($query)->fetchAll();
        $list=$this->formatList($data);

        // 分页
        $pageHtml='';
        $count_sql="SELECT count(id) FROM article LEFT JOIN article_view AS v ON id = v.article_id WHERE 1=1 {$where} /* 获取文章分页统计 */";
        $count_arr=$this->db()->query($count_sql)->fetch();
        $count=current($count_arr);

        if($count && $page > ceil($count/$limit)) show_404();

        if($count){
            $pageHtml=(new Page($count, $limit, $page, $url, 2))->myde_write();
        }else{
            $pageHtml='';
        }

        return ['list'=>$list,'page'=>$pageHtml];
    }
    public function formatList($data=[])
    {
        if(empty($data)) return [];

        //文章分类
        $category_title = Loader::singleton(CategoryModel::class)->getTitles();

        //文章标签
        $article_ids = array_column($data,'id');
        $article_tags = Loader::singleton(TagModel::class)->getListByArticleIds($article_ids);
    
        foreach ($data as $k => $v) {
            // if(mb_strlen($data[$k]['title'],'utf8') > 20){
            //     $data[$k]['title'] = mb_substr($data[$k]['title'], 0, 20).'...';
            // }
            if(isset($v['create_time'])) $data[$k]['create_time']=date('Y/m/d',strtotime($v['create_time']));
            if(isset($v['id'])) $data[$k]['url']='/article/detail-'.$v['id'].'.html';
            if(isset($v['cid'])) $data[$k]['category']=$category_title[$v['cid']];
            
            $data[$k]['view']=isset($v['view']) ? $v['view'] : 0;

            if(isset($article_tags[$v['id']])) $data[$k]['tags']=$article_tags[$v['id']];
            else $data[$k]['tags']=[];
        }
        return $data;
    }

    public function getDetail($id)
    {
        $query="SELECT *,v.view FROM article INNER JOIN article_view AS v ON id=v.article_id WHERE 1=1 AND id={$id} LIMIT 1";
        $data=$this->db()->query($query)->fetch();
        if(!$data) return [];
        return $this->formatDetail($data);
    }
    public function formatDetail($data)
    {
        return $data;
    }

    public function getCountByCategory()
    {
        $query = 'SELECT cid,count(id) as total FROM article GROUP BY cid';
        $data=$this->db()->query($query)->fetchAll();
        $list=[];
        foreach ($data as $v) {
            $list[$v['cid']]=$v['total'];
        }
        return $list;
    }
    public function getListByDate()
    {
        $query = "SELECT DATE_FORMAT(create_time,'%Y年') as create_year, DATE_FORMAT(create_time,'%m月%d日') as create_day, id, title FROM article ORDER BY create_time DESC LIMIT 100";
        $data=$this->db()->query($query)->fetchAll();
        $list=$this->formatList($data);
        return $list;
    }
    public function increaseView($article_id)
    {
        $query = "UPDATE article_view SET view = view+1 WHERE article_id ={$article_id}";
        $status=$this->db()->exec($query);
        return $status;
    }

    // public function getViewList()
    // {
    //     $data_sql="SELECT a.id,a.cid,a.create_time,a.title,v.view FROM article a INNER JOIN article_view v ON a.id=v.article_id ORDER BY v.view DESC LIMIT 10";
    //     $data=$this->db()->query($data_sql)->fetchAll();
    //     return $this->formatList($data);
    // }
}