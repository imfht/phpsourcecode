<?php

class ContentModel extends Model {

    private $id;
    private $title;
    private $column_id;
    private $sort;
    private $color;
    private $comment;
    private $tag;
    private $attribute;
    private $author;
    private $description;
    private $thumb;
    private $content;
    private $time;
    private $click;
    private $post;
    private $is_show;
    private $user;
    private $source;

    public function __set($_key, $_value) {
        $this->$_key = $_value;
    }

    //拦截器(__get)
    public function __get($_key) {
        return $this->$_key;
    }

    public function addContent() {
        $_sql = "INSERT INTO my_content VALUES()";
        return parent::query($_sql);
    }

    public function updateContent() {
        $_sql = "UPDATE my_content SET ";
        return parent::query($_sql);
    }

    public function showContent() {
        $_sql = "SELECT * FROM my_content";
        return $this->getAll($_sql);
    }

    public function oneContent($aid) {
        $_sql = "SELECT c.title,c.id,c.source,c.content,c.time,c.author,c.thumb,c.description,c.column_id,c.click,c.post,n.name as c_name FROM my_content c LEFT JOIN my_column n ON c.column_id = n.id WHERE c.id={$aid} LIMIT 1";
        return $this->getRow($_sql);
    }

    public function deleteContent() {
        $_sql = "DELETE FROM my_content WHERE id='$this->id' LIMIT 1";
        return $this->query($_sql);
    }

    //获取全部文章列表
    public function showAllList() {
        $_sql = "SELECT c.title,c.id,c.time,c.author,c.thumb,c.description,c.column_id,c.click,c.post,n.name as c_name FROM my_content c LEFT JOIN my_column n ON c.column_id = n.id  LIMIT 20";
        return $this->getAll($_sql);
    }

    //获取指定栏目的文章列表
    public function showList($cid) {
        $_sql = "SELECT c.title,c.id,c.time,c.author,c.thumb,c.description,c.column_id,c.click,c.post,n.name as c_name FROM my_content c LEFT JOIN my_column n ON c.column_id = n.id WHERE column_id='{$cid}' LIMIT 20";
        return $this->getAll($_sql);
    }

    //获取指定栏目的热点文章列表
    public function showHotList($column_id) {
        $_sql = "SELECT 
                        id,
                        title,
                        click
                   FROM
                        my_content
                  WHERE
                        column_id='$column_id'
                   ORDER BY
                        click DESC";
        return $this->getAll($_sql);
    }

    //获取指定栏目的推荐文章列表
    public function showRecommendList($column_id) {
        $_sql = "SELECT 
                        id,
                        title,
                        click
                   FROM
                        my_content
                  WHERE
                        column_id='$column_id'
                     AND
                        attribute LIKE '%推荐%'
                   ORDER BY
                        time DESC";
        return $this->getAll($_sql);
    }

    //获取首页图文推荐
    public function RecPicText() {
        $_sql = "SELECT id ,title,thumb FROM my_content LIMIT 5";
        return $this->getAll($_sql);
    }

    //获取指定栏目的下一篇文章列表
    public function showNextList($id,$column_id) {
        $_sql = "SELECT 
                        id,
                        title
                   FROM
                        my_content
                  WHERE
                        column_id={$column_id}
                    AND 
                        id > {$id}
                   LIMIT
                        1";
        return $this->getRow($_sql);
    }

    //获取指定栏目的上一篇文章列表
    public function showPrevList($id,$column_id) {
        $_sql = "SELECT 
                        id,
                        title
                   FROM
                        my_content
                  WHERE
                        column_id={$column_id}
                    AND 
                        id < {$id}
                   LIMIT
                        1";
        return $this->getRow($_sql);
    }

    public function RecBook() {
        $_sql = "SELECT 
                        id,
                        title,
						thumb,
						description
                   FROM
                        my_content
                  WHERE
                        column_id=33
                   LIMIT
                        3";
        return $this->getAll($_sql);
    }

    public function NewNews() {
        $_sql = "SELECT   c.id,c.title,c.column_id,n.name as c_name  FROM  my_content c left join my_column n on c.column_id = n.id ORDER BY time DESC limit 1,11";
        return $this->getAll($_sql);
    }

    public function OneTopNews() {
        $_sql = "SELECT   c.id,c.title,c.column_id,c.description ,n.name as c_name  FROM  my_content c left join my_column n on c.column_id = n.id ORDER BY time DESC limit 1,11";
        return $this->getRow($_sql);
    }
    
    
    public function addClick($id){
        $_sql = "UPDATE my_content SET click=click+1 WHERE id={$id} LIMIT 1";
        $this->query($_sql);
    }
}
