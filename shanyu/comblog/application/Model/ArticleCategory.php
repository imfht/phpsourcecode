<?php
namespace App\Model;

use Kernel\Loader;
use Kernel\Page;

use App\Model\Article as ArticleModel;

class ArticleCategory extends Model
{
    public function getList()
    {
        $query="SELECT id,pid,title FROM article_category ORDER BY id ASC /* 获取文章分类列表 */";
        $data=$this->db()->query($query)->fetchAll();
        $list=$this->formatList($data);
        return $list;
    }
    public function getParentList($pids){
        $query="SELECT id,title FROM article_category WHERE id IN ({$pids}) /* 获取文章分类的父级分类列表 */";
        $data=$this->db()->query($query)->fetchAll();
        $list=$this->formatList($data);
        return $list;
    }
    public function formatList($data)
    {
        foreach ($data as $k => $v) {
            $data[$k]['url']='/article/index-'.$v['id'].'.html';
        }
        return $data;
    }
    public function getDetail($cid)
    {
        $query="SELECT * FROM article_category WHERE 1=1 AND id={$cid} LIMIT 1 /* 获取文章分类数据 */";
        $data=$this->db()->query($query)->fetch();
        if(!$data) return [];
        $data['url']='/article/index-'.$cid.'.html';
        return $data;
    }

    public function getChildId($cid){
        $query="SELECT id FROM article_category WHERE FIND_IN_SET({$cid},pids) /* 获取文章分类的子级分类编号列表 */";
        $data=$this->db()->query($query)->fetchAll();
        if($data){
            return array_column($data, 'id');
        }else{
            return [];
        }
    }
    public function getChildFilter($cid){
        $cids = $this->getChildId($cid);
        if($cids){
            $filter = "cid IN ({$cid},".implode(',', $cids).")";
        }else{
            $filter = "cid = {$cid}";
        }
        return $filter;
    }

    public function getTitles($count=false)
    {
        $sql="SELECT id,title FROM article_category /* 获取文章分类编号与名称的列表 */";
        $data=$this->db()->query($sql)->fetchAll();
        $list=[];
        foreach ($data as $v) {
            $list[$v['id']]=[
                'title'=>$v['title'],
                'url'=>'/article/index-'.$v['id'].'.html',
            ];
        }
        return $list;
    }
}