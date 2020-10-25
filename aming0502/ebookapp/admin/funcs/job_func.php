<?php 
	
	function get_job_article($job_tpye){
		$db =  new mysql();
		$sql_select="select article_id from t_job where status= 0 and job_type= ".$job_tpye." limit 0,1";
		$query = $db->query($sql_select);
		if($db->num_rows($query)==0){
			return -1;
		}else{
			$artile = $db->fetch_row_array($query);
			return $artile["article_id"];
		}
	}
	
	function update_job_article($article_id,$job_type){
		$db =  new mysql();
		$update_select="update t_job set status =1  where article_id =".$article_id." and job_type = ".$job_type;
		$query = $db->query($update_select);
	}
	
?>