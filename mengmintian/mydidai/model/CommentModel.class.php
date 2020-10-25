<?php

class CommentModel extends Model {

    private $id;
    private $aid;
    private $content;
    private $addtime;
    private $uid;
    private $type;
    private $is_show;

    public function __set($_key, $_value) {
        $this->$_key = $_value;
    }

    //拦截器(__get)
    public function __get($_key) {
        return $this->$_key;
    }

    public function PostComment($aid,$content,$uid,$type = 1,$is_show = 1){
    	$_sql = "INSERT INTO my_comment (aid,content,addtime,uid,type,is_show) VALUES ({$aid},'{$content}'," . time() .",{$uid},{$type},{$is_show})";
    	$this->query($_sql);
    }

    public function CommentList($aid,$start = 0){
        $_sql = "SELECT * FROM my_comment WHERE aid={$aid} LIMIT {$start},15";
        return $this->getAll($_sql);
    }
}