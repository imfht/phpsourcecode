<?php
class ModelToolUrlAlias extends Model {
	public function addUrlAlias($data){
		$sql="insert into ".$this->db->table('url_alias')
		." set query='".$this->db->escape($data['query'])
		."',keyword='".$this->db->escape($data['keyword'])
		."',category='".$this->db->escape($data['category'])."'";
		
		$this->db->query($sql);
	}
	
	public function editAlias($url_alias_id,$data){
		$sql="update ".$this->db->table('url_alias')
		." set query='".$this->db->escape($data['query'])
		."',keyword='".$this->db->escape($data['keyword'])
		."',category='".$this->db->escape($data['category'])
		."' where url_alias_id=".(int)$url_alias_id;
		
		$this->db->query($sql);
	}
	
	public function deleteAlias($url_alias_id){
		$sql="delete from ".$this->db->table('url_alias')
		." where url_alias_id=".(int)$url_alias_id;
		
		$this->db->query($sql);
	}
	
	public function deleteAliasByQuery($query){
		$sql="delete from ".$this->db->table('url_alias')
		." where query='".$this->db->escape($query)."'";
		
		$this->db->query($sql);
	}
	
	public function deleteAliasByCategory($category){
		$sql="delete from ".$this->db->table('url_alias')
		." where category='".$this->db->escape($category)."'";
		
		$this->db->query($sql);
	}
	
	public function getUrlAlias($url_alias_id){
		$sql="select * from ".$this->db->table('url_alias')
		." where url_alias_id=".(int)$url_alias_id;
		
		$query=$this->db->query($sql);
		
		return $query->row;
	}
	
	public function getTotalAliasUrls($data=array()){
		$sql="select Count(*) as total from ".$this->db->table('url_alias');
		
		$implode = array();
		
		if(isset($data['category'])){
			$implode[]=" category='".$this->db->escape($data['category'])."'";
			
		}
		
		if ($implode) {
   			$sql .= " WHERE " . implode(" AND ", $implode);
  		}
  		
  		$query=$this->db->query($sql);
  		
  		return $query->row['total'];
		
	}
	
	public function getAliasUrls($data=array()){
		$sql="select * from ".$this->db->table('url_alias');
		
		$implode = array();
		
		if(isset($data['category'])){
			$implode[]=" category='".$this->db->escape($data['category'])."'";
			
		}
		
		if ($implode) {
   			$sql .= " WHERE " . implode(" AND ", $implode);
  		}
  		
  		$sql.=" ORDER BY url_alias_id DESC";
  		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
  		
  		$query=$this->db->query($sql);
  		
  		return $query->rows;
	}
	
	
}
?>