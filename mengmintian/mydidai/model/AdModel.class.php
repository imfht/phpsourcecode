<?php
class AdModel extends Model{
	private $id;
	private $title;
	private $content;
	private $url;
	private $pic;
	private $pos;
	private $add_time;
	private $update_time;
	private $start_time;
	private $end_time;
	private $sort;

	public function __set($_key, $_value) {
			$this->$_key = $_value;
		}
		
	//拦截器(__get)
	public function __get($_key) {
		return $this->$_key;
	}

               //
	public function addAd(){
		$_sql = " INSERT INTO my_ad (title,content,url,pic,pos,sort,start_time,end_time,add_time,update_time)VALUES('{$this->title}','{$this->content}','{$this->url}','{$this->pic}','{$this->pos}','{$this->sort}',{$this->start_time},{$this->end_time}," . time() . ",".time() .")";
		return parent::query($_sql);
	}

	public function updateAd(){
		$_sql = "UPDATE my_ad SET title='{$this->title}',content='{$this->content}',url='{$this->url}',pos='{$this->pos}',pic='{$this->pic}',sort='{$this->sort}',start_time={$this->start_time},end_time={$this->end_time},update_time=".time()." WHERE id={$this->id} LIMIT 1";
		return parent::query($_sql);
	}


	public function showAd(){
		$_sql = "SELECT * FROM my_ad";
		return $this->getAll($_sql);
	}

	public function deleteAd(){
		$_sql = "DELETE FROM my_ad WHERE id='$this->id' LIMIT 1";
		return $this->query($_sql);
	}
    
    	public function oneAd(){
		$_sql = "SELECT * FROM my_ad WHERE id='$this->id' LIMIT 1";
		return $this->getRow($_sql);
	}
}
?>
