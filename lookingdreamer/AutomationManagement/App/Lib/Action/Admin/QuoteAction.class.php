<?php
class QuoteAction extends CommonAction {
	// 查询报价
	public function index() {
		// 接受从forwording转发来的参数
		$query_date = $this->_param ( 'query_date' );
		$query_date = urldecode ( $query_date );
		$query_type = $this->_param ( 'query_type' );
		$depart = $this->_param ( 'depart' ); // All为查询所有分区的数据
	
		/*
		 * 开始逻辑处理: $time取查询时间(各个分区都公用的变量),$tb取查询的各个分区的数据表,$str为各个数据表中查询的字段,$order_str为数据表中排序的字段(datetime的数据类型)
		 */
		// Verfy公用变量
		$time = $query_date;
		$order_str = $this->_param ( 'order_str','htmlspecialchars', 'time' );
		$str =$this->_param( 'str','htmlspecialchars', 'quote' ); // verify 报价
		$table_array = C ( 'Verify' );
		switch ($depart) {
			case "1" :
				switch ($query_type) {
					case "Day" :
						$title = "一区";
						$legend ="一";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param('unit');
						$receive = R('Admin/Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Week" :
						$title = "一区";
						$legend ="一";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Month" :
					    $title = "一区";
						$legend ="一";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
				}
				break;
			case "2" :
				switch ($query_type) {
					case "Day" :
						$title = "二区";
						$legend ="一";
						$tb=get_tablename($table_array, $title);
						$valtitle="二区报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Week" :
						$title = "二区";
						$legend ="一";
						$tb=get_tablename($table_array, $title);
						$valtitle="二区报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Month" :
						$title = "二区";
						$legend ="一";
						$tb=get_tablename($table_array, $title);
						$valtitle="二区报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
				}
				break;
			case "3" :
				switch ($query_type) {
					case "Day" :
						$title = "三区";
						$legend ="三";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');					
						$receive = R('Admin/Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Week" :
						$title = "三区";
						$legend ="三";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Month" :
						$title = "三区";
						$legend ="三";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
				}
				break;
			case "4" :
				switch ($query_type) {
					case "Day" :
						$title = "四区";
						$legend ="四";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Week" :
						$title = "四区";
						$legend ="四";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Month" :
						$title = "四区";
						$legend ="四";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
				}
				break;
			case "5" :
				switch ($query_type) {
					case "Day" :
						$title = "五区";
						$legend ="五";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Week" :
						$title = "五区";
						$legend ="五";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
				        $receive = R('Admin/Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Month" :
						$title = "五区";
						$legend ="五";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
				}
				break;
			case "6" :
				switch ($query_type) {
					case "Day" :
						$title = "六区";
						$legend ="六";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Week" :
						$title = "六区";
						$legend ="六";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
					case "Month" :
						$title = "六区";
						$legend ="六";
						$tb=get_tablename($table_array, $title);
						$valtitle=$title."报价 ";
						$unit=$this->_param ('unit');
						$receive = R('Admin/Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
						R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
						exit();
						break;
				}
				break;
				case "0" :
					switch ($query_type) {
						case "Day" :
							$action_name='Admin/Show/show_ins_multi_quote';
							R('Admin/Verfy/getdepartall',array($table_array,$time,$str,$order_str,$unit,$query_date,$action_name));
// 							R('Admin/Verfy/common',array($query_date,$receive,$title,$unit,$legend));
							exit();
							break;
						case "Week" :
							$action_name='Admin/Show/show_week_ins_multi_quote';
							R('Admin/Verfy/getdepartallweek',array($table_array,$time,$str,$order_str,$unit,$query_date,$action_name));
							exit();
							break;
						case "Month" :
					        $action_name='Admin/Show/show_month_ins_multi_quote';
							R('Admin/Verfy/getdepartallmonth',array($table_array,$time,$str,$order_str,$unit,$query_date,$action_name));
							exit();
							break;
					}
					break;
				default:
					switch ($query_type) {
						case "Day" :
							$title="一区";
							$tb=get_tablename($table_array, $title);
							$valtitle=$title."报价 ";
							$unit=$this->_param('unit','htmlspecialchars');
							$receive = R('Admin/Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
							$this->receive=$receive;
							$this->display('Echarts:index');
							exit();
							break;
						case "Week" :
							$title = "一区";
							$tb=get_tablename($table_array, $title);
							$valtitle=$title."报价 ";
							$unit=$this->_param ('unit');
							$receive = R('Admin/Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
							$this->receive=$receive;
							$this->display('Echarts/index');
							exit();
							break;
						case "Month" :
							$title = "一区";
							$tb=get_tablename($table_array, $title);
							$valtitle=$title."报价 ";
							$unit=$this->_param ('unit');
							$receive = R('Admin/Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$valtitle,$unit));
							$this->receive=$receive;
							$this->display('Echarts/index');
							exit();
							break;
					}
				break;
		}
}
	
	public function getdepartall($table_array,$time,$str,$order_str,$unit,$query_date,$action_name){
		   $series_array = array ();
			foreach ( $table_array as $part => $real_tb ) {
				$tb = $real_tb;
				$title = $part;
				$receive = R ( $action_name, array (
						$time,
						$tb,
						$db = 'DB_COLLECT',
						$str,
						$order_str,
						$title,
						$unit
				) );
				$series_array [] = array (
						name => $part,
						type => "line",
						stack => "总量",
						total => get_totalNum ( $receive ['title'] ),
						xvalue => json_decode ( $receive ['xname'], true ),
						data => json_decode ( $receive ['value'], true )
				);
			}
		
// 		dump($series_array);die();	
		// 由于需要将各个分区的数据放在一个图标里面实现,需要取相同时间的数据
		// $YearMonth = date ( 'Y-m-d', strtotime ( $query_date ) ); // 转换为时间戳=>转换为日期
		$new_array = array ();
		foreach ( $series_array as $key => $value ) {
			// 开始循环日期,取array的时间的最大值和最小值
			// $new_array['name']=$value['name'];
			$count_value = count ( $value ['xvalue'] ) - 1;
			$min_date = $value ['xvalue'] [0];
			$max_date = $value ['xvalue'] [$count_value];
			$new_array [] = array (
					'min_date' => $min_date,
					'max_date' => $max_date,
					'name' => $value ['name'],
					'key_max' => $count_value
			);
		}
		$min_arrray = array ();
		$max_arrray = array ();
		foreach ( $new_array as $new_key => $new_value ) {
			$min_arrray [] = strtotime ( $new_value ['min_date'] );
		}
		foreach ( $new_array as $new_key => $new_value ) {
			$max_arrray [] = strtotime ( $new_value ['max_date'] );
		}
		$minDate_Max = date ( 'H:i', max ( $min_arrray ) ); // 最小日期的最大值
		$maxDate_Min = date ( 'H:i', max ( $max_arrray ) ); // 最大日期的最小值
		// 重新组合新的series里的数组
		$real_series_arr = array ();
		foreach ( $series_array as $v ) {
			foreach ( $v ['xvalue'] as $date_key => $date ) {
				if ($date == "$minDate_Max") {
					$min_key = $date_key;
				}
				if ($date == "$maxDate_Min") {
					$max_key = $date_key;
				}
			}
			if (empty ( $min_key )) {
				$min_key = 0;
			}
			if (empty ( $max_key )) {
				$max_key = 0;
			}
			$xvalue_array = array ();
			foreach ( $v ['xvalue'] as $date_key => $date ) {
				if ($date_key >= $min_key) {
					if ($date_key <= $max_key) {
						$xvalue_array [] = $date;
					}
				}
			}
			$yvalue_array = array ();
			foreach ( $v ['data'] as $data_key => $data ) {
				if ($data_key >= $min_key) {
					if ($data_key <= $max_key) {
						$yvalue_array [] = $data;
					}
				}
			}
			$real_series_arr [] = array (
					'name' => $v ['name'],
					'type' => $v ['type'],
					'stack' => $v ['stack'],
					'total' => $v ['total'],
					'xvalue' => $xvalue_array,
					'data' => $yvalue_array
			);
		}
		// 剥离data的数据
		$series = array ();
		foreach ( $real_series_arr as $series_val ) {
			$series [] = array (
					'name' => $series_val ['name'],
					'type' => $series_val ['type'],
					'stack' => $series_val ['stack'],
					'data' => $series_val ['data']
			);
		}
		$data = array ();
		$Year_Month = date ( 'Y-m-d', strtotime ( $query_date ) );
		// $Year_Month=$query_date;
		$data ['date'] = $Year_Month;
		$data ['title'] = $this->_param  ( 'type' );
		$data ['xAxis'] = $real_series_arr [0] ['xvalue'];
		$data ['series'] = $series;
		$legend=array();
		foreach ($data ['series'] as $val){
			foreach ($val as $key1=>$val1){
				if ($key1 == "name"){
					$legend[]=$val1;
				}
			}
		}
		$data['legend']=$legend;
		$this->ajaxReturn ( $data, 'json' );
	}
	
	//每周所有分区的报价的查询
	public function getdepartallweek($table_array,$time,$str,$order_str,$unit,$query_date,$action_name){
		$series_array = array ();
		foreach ( $table_array as $part => $real_tb ) {
			$tb = $real_tb;
			$title = $part;
			$receive = R ( $action_name, array (
					$time,
					$tb,
					$db = 'DB_COLLECT',
					$str,
					$order_str,
					$title,
					$unit
			) );
			$series_array [] = array (
					name => $part,
					type => "line",
					stack => "总量",
					total => get_totalNum ( $receive ['title'] ),
					xvalue => json_decode ( $receive ['xname'], true ),
					data => json_decode ( $receive ['value'], true )
			);
		}
	
	   
		// 重新组合新的series里的数组
		$real_series_arr = array ();
		foreach ( $series_array as $v ) {

			$xvalue_array = $v['xvalue'];
		
			$yvalue_array = array ();
			foreach ( $v ['data'] as $data ) {
						$yvalue_array [] = $data;
					
			}
			$real_series_arr [] = array (
					'name' => $v ['name'],
					'type' => $v ['type'],
					'stack' => $v ['stack'],
					'total' => $v ['total'],
					'xvalue' => $xvalue_array,
					'data' => $yvalue_array
			);
		}
		//各个分区总的数量
		$total_array=array();
		foreach ($real_series_arr as $num){
			$total_array[]=$num['total'];
		}
		$total=array_sum($total_array);
		// 剥离data的数据
		$series = array ();
		foreach ( $real_series_arr as $series_val ) {
			$series [] = array (
					'name' => $series_val ['name'],
					'type' => $series_val ['type'],
					'stack' => $series_val ['stack'],
					'data' => $series_val ['data']
			);
		}
		
		
		$data = array ();
		$Year_Month = date ( 'Y-m-d', strtotime ( $query_date ) );
		// $Year_Month=$query_date;
		$data ['date'] = $Year_Month;
		$front_title=$this->_param ( 'type' );
		$data ['title'] =$front_title."\r\n[总:".$total."]" ;
		$data ['xAxis'] = $real_series_arr [0] ['xvalue'];
		$data ['series'] = $series;
		$legend=array();
		foreach ($data ['series'] as $val){
			foreach ($val as $key1=>$val1){
				if ($key1 == "name"){
					$legend[]=$val1;
				}
			}
		}
		$data['legend']=$legend;
		$this->ajaxReturn ( $data, 'json' );
	}
	
	
	//每月所有分区的报价的查询
	public function getdepartallmonth($table_array,$time,$str,$order_str,$unit,$query_date,$action_name){
		$series_array = array ();
		foreach ( $table_array as $part => $real_tb ) {
			$tb = $real_tb;
			$title = $part;
			$receive = R ( $action_name, array (
					$time,
					$tb,
					$db = 'DB_COLLECT',
					$str,
					$order_str,
					$title,
					$unit
			) );
			$series_array [] = array (
					name => $part,
					type => "line",
					stack => "总量",
					total => get_totalNum ( $receive ['title'] ),
					xvalue => json_decode ( $receive ['xname'], true ),
					data => json_decode ( $receive ['value'], true )
			);
		}
	
	
		// 重新组合新的series里的数组
		$real_series_arr = array ();
		foreach ( $series_array as $v ) {
	
			$xvalue_array = $v['xvalue'];
	
			$yvalue_array = array ();
			foreach ( $v ['data'] as $data ) {
				$yvalue_array [] = $data;
					
			}
			$real_series_arr [] = array (
					'name' => $v ['name'],
					'type' => $v ['type'],
					'stack' => $v ['stack'],
					'total' => $v ['total'],
					'xvalue' => $xvalue_array,
					'data' => $yvalue_array
			);
		}
		//各个分区总的数量
		$total_array=array();
		foreach ($real_series_arr as $num){
			$total_array[]=$num['total'];
		}
		$total=array_sum($total_array);
		// 剥离data的数据
		$series = array ();
		foreach ( $real_series_arr as $series_val ) {
			$series [] = array (
					'name' => $series_val ['name'],
					'type' => $series_val ['type'],
					'stack' => $series_val ['stack'],
					'data' => $series_val ['data']
			);
		}
	
	
		$data = array ();
		$Year_Month = date ( 'Y-m-d', strtotime ( $query_date ) );
		// $Year_Month=$query_date;
		$data ['date'] = $Year_Month;
		$front_title=$this->_param('type' );
		$data ['title'] =$front_title."\r\n[总:".$total."]" ;
		$data ['xAxis'] = $real_series_arr [0] ['xvalue'];
		$data ['series'] = $series;
		$legend=array();
		foreach ($data ['series'] as $val){
			foreach ($val as $key1=>$val1){
				if ($key1 == "name"){
					$legend[]=$val1;
				}
			}
		}
		$data['legend']=$legend;
		$this->ajaxReturn ( $data, 'json' );
	}
	public function common($query_date,$receive,$title,$unit,$legend){
		//ajax开始
		$data = array ();
		$Year_Month = date ( 'Y-m-d', strtotime ( $query_date ) );
		// $Year_Month=$query_date;
		$data ['date'] = $Year_Month;
		$data ['title'] = $receive['title'] ;
		$data ['xAxis'] =  json_decode($receive['xname']);
		$yv=json_decode($receive['value']);
		$series=array(
				array(
						"name"=>$title,
						"type"=>"line",
						"stack"=>$unit,
						"data"=>$yv
				));
		$data ['series'] = $series;
		$data['legend']= $legend;
		$this->ajaxReturn ( $data, 'json' );
		
	}
}