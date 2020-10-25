<?php

/*
 * Copyright (C) xiuno.com
 */

class stat extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'stat';
		$this->primarykey = array('year', 'month', 'day');
		
		// hook stat_construct_end.php
	}

	// 取时间段
	public function get_list_by_date_range($startdate, $enddate) {
		
	}
}
?>