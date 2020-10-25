<?php
	if(!isset($links)){
		$links = new Links();
	}
	$hm_links = $links->getBy('*', "state=1");
	if($hm_links[0]){
		$data['hm_links'] = $hm_links[1];
	}else{
		$data['hm_links'] = array();
	}
?>