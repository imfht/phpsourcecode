<?php
namespace Home\Model;
use Think\Model;
class ArticleModel extends Model {

    public static function I() {
        return new ArticleModel();
    }

    public function getArticle($aid) {
        $art = M('article')->where("aid='{$aid}'")->find();
        return $art;
    }

    public function getArticleList($sort, $limit) {
        $where = $sort ? "sort={$sort}" : "1";
        $artList = M('article')->where($where)->order("`order` desc,aid desc")->limit($limit)->select();
        return $artList;
    }
    
    public function getZhiding($limit=10){
        $where = "state=1 and zhiding=1";
        $order = "`order` desc";
        return $this->where($where)->limit("0,{$limit}")->order($order)->select();
    }
    
    public function getTuijian($limit=10){
        $where = "state=1 and tuijian=1";
        $order = "`order` desc";
        return $this->where($where)->limit("0,{$limit}")->order($order)->select();
    }
}
