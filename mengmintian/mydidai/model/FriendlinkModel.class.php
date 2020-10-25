<?php
class FriendlinkModel extends Model{
	private $id;
	private $title;
	private $url;
	private $sort;
	private $logo_url;
	private $show_type;

	public function __set($_key, $_value) {
			$this->$_key = $_value;
		}
		
	//拦截器(__get)
	public function __get($_key) {
		return $this->$_key;
	}

	public function addFriendlink($title,$url,$logo_url,$sort){
		$_sql = "INSERT INTO my_friendlink (title,url,logo_url,sort,show_type) VALUES ('{$this->title}','{$this->url}','{$this->logo_url}','{$this->sort}','{$this->show_type}')";
		return parent::query($_sql);
	}

	public function updateFriendlink(){
		$_sql = "UPDATE my_friendlink SET  title='{$this->title}',url='{$this->url}',logo_url='{$this->logo_url}',sort='{$this->sort}',show_type='{$this->show_type}' WHERE id={$this->id}";
		echo $_sql;
		return parent::query($_sql);
	}


	public function showFriendlink(){
		$_sql = "SELECT * FROM my_friendlink";
		return $this->getAll($_sql);
	}

	public function deleteFriendlink(){
		$_sql = "DELETE FROM my_friendlink WHERE id='$this->id' LIMIT 1";
		return $this->query($_sql);
	}
    
    public function oneFriendlink(){
		$_sql = "SELECT * FROM my_friendlink WHERE id={$this->id} LIMIT 1";
		return $this->getRow($_sql);
	}


	public function textFriendlink(){
		$_sql = "SELECT * FROM my_friendlink WHERE show_type=0 LIMIT 10";
		return $this->getAll($_sql);
	}

	public function picFriendlink(){
		$_sql = "SELECT * FROM my_friendlink WHERE show_type=1 LIMIT 9";
		return $this->getAll($_sql);
	}
}
?>
