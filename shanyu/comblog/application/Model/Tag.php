<?php
namespace App\Model;

use Kernel\Loader;
use Kernel\Page;

use App\Model\Article as ArticleModel;

class Tag extends Model
{
    public function getTitleCountList()
    {
        $query = 'SELECT id,title,(SELECT count(*) FROM article_tag WHERE article_tag.tag_id = tag.id ) as total FROM tag ORDER BY total DESC LIMIT 100';
        $data=$this->db()->query($query)->fetchAll();
        return $this->formatList($data);
    }
    public function getTitle($tag_id)
    {
        $query = "SELECT title FROM tag WHERE id = {$tag_id} LIMIT 1";
        $value=$this->db()->query($query)->fetchColumn(0);
        return $value;
    }
    public function getTitles()
    {
        $query = "SELECT id,title FROM tag";
        $value=$this->db()->query($query)->fetchAll();
        return $value;
    }
    public function formatList($data=[])
    {
        foreach ($data as $k => &$v) {
            if(isset($v['id'])){
                $data[$k]['url']='/search?tag='.$v['id'];
                unset($v['id']);
            }
        }
        return $data;
    }

    public function getListByArticleIds($ids)
    {
        if(is_array($ids)){
            $ids = implode(',',$ids);
        }
        $query = "SELECT article_id,tag_id as id,t.title FROM article_tag INNER JOIN tag t ON t.id = tag_id WHERE 1=1 AND article_id IN ({$ids}) /* 获取当前文章列表的关联标签 */";
        $data=$this->db()->query($query)->fetchAll();
        $list=$this->formatList($data);
        $list=$this->setGroupByArticleId($list);
        return $list;
    }
    protected function setGroupByArticleId($list)
    {
        $data = [];
        foreach ($list as $k => $v) {
            $data[$v['article_id']][]=[
                'url'=>$v['url'],
                'title'=>$v['title'],
            ];
        }
        return $data;
    }
    public function getListByArticleId($id)
    {
        $query = "SELECT tag_id as id,t.title FROM article_tag INNER JOIN tag t ON t.id = tag_id WHERE 1=1 AND article_id = {$id} /* 获取当前文章列表的关联标签 */";
        $data=$this->db()->query($query)->fetchAll();
        $list=$this->formatList($data);
        return $list;
    }
}