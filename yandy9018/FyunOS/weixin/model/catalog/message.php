<?php
class ModelCatalogMessage extends Model {
	
	
	public function editMessage($message_id,$reply) {
		$this->db->query("UPDATE " . DB_PREFIX . "message SET  status = '1' , reply = '" . $this->db->escape($reply) . "'  WHERE message_id = '" . (int)$message_id . "'");
	}
	
	public function deleteMessage($message_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "message WHERE message_id = '" . (int)$message_id . "'");
	
	}	

	public function getMessage($message_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "message  WHERE message_id = '" . (int)$message_id . "' ORDER BY date_added DESC ");
		
		return $query->row;
	}
		
	public function getMessages($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "message ";
		
		$sort_data = array(
				'author',
				'email',
			    'status'
			);		
		
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY date_added DESC," . $data['sort'];	
			} else {
				$sql .= " ORDER BY date_added";	
			}
			
			$sql .= " DESC";
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}		

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
	
	}
	
	public function getTotalMessages() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "message");
		
		return $query->row['total'];
	}
}
?>