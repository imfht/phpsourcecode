<?php

class ColumnModel extends Model {

    private $id;
    private $name;
    private $info;
    private $sort;
    private $is_show;
    private $pid;

    public function __set($_key, $_value) {
        $this->$_key = $_value;
    }

    //拦截器(__get)
    public function __get($_key) {
        return $this->$_key;
    }

    public function addColumn() {
        $_sql = "INSERT INTO  my_column(
							 			name,
							 			info,
							 			sort,
							 			is_show,
							 			pid
							 	)VALUES(
							 			'$this->name',
							 			'$this->info',
							 			'$this->sort',
							 			'$this->is_show',
							 			'$this->pid'
		)";
        return parent::query($_sql);
    }

    public function updateColumn() {
        $_sql = "UPDATE my_column SET
									 name='$this->name',
									 info='$this->info',
									 sort='$this->sort',
									 is_show='$this->is_show', 
									 pid='$this->pid' 
							    WHERE 
							    	 id='$this->id'";
        return parent::query($_sql);
    }

    //显示全部栏目
    public function showColumn() {
        $_sql = "SELECT id,name,info,is_show,sort,pid FROM my_column";
        return $this->getAll($_sql);
    }

    public function oneColumn() {
        $_sql = "SELECT id,name,info,is_show,sort,pid FROM my_column WHERE id='$this->id' LIMIT 1";
        return $this->getRow($_sql);
    }

    public function deleteColumn() {
        $_sql = "DELETE FROM my_column WHERE id='$this->id' LIMIT 1";
        return $this->query($_sql);
    }

    //显示导航栏的栏目
    public function showNav() {
        $_sql = "SELECT id,name,info,is_show,sort,pid FROM my_column WHERE is_show=1";
        return $this->getAll($_sql);
    }

    //获取指定栏目的名称
    public function getNavTitle($c_id) {
        $_sql = "SELECT name FROM my_column WHERE id='$c_id'";
        return $this->getOne($_sql);
    }

    //获取网络资源导航
    public function NetNav() {
        $_sql = "SELECT id,name FROM my_column WHERE pid=40";
        return $this->getAll($_sql);
    }

}

?>
