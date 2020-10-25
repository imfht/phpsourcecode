<?php
class ShowAction extends Action {
	// 获取某个时间半个小时的之前的支付数量
	// 获取某天的支付数量
	// 获取某个月的支付数量
	public function getgroup($start_time, $data_interval = 'day',$tb,$db,$str,$order_str) {
		// 获取时间间隔
		if (! isdate ( $start_time )) {
			echo $start_time;
			$this->error ( '传入时间格式不正确.<br/>For example:2014-08-04 16:30:00' );
		}
		switch ($data_interval) {
			case "day" :
				$half_time = mdate_arr ( $start_time );
				//由于结束时间不是很精确到00所以结束时间+59s 如 < 2014-09-03 16:00:59
				$start_time=date('Y-m-d H:i:s',strtotime($start_time) + 59);
				break;
			case "week" :
				$half_time = mdate_arr ( $start_time, 'week' );
				break;
			case "month" :
// 				由于数据库采集的原因不是按照count计数所以需要计算每个月每天的总和==
// 		    	输入日期的月份所在的1号的最大值{<23:59:59}+... + 输入日期所在时间的最大值 
				$half_time = mdate_arr ( $start_time, 'month' );
				$Ins_multi_quote = M ( $tb, null, $db );
				$map ["$order_str"] = array (
						array (
								'gt',
								$half_time
						),
						array (
								'lt',
								$start_time
						)
				);
// 				$this->error('修正中...','echartofday');
				$string="$order_str,$str";
				$res_arr = $Ins_multi_quote->where ( $map )->field($string)->order('time')->select();
				if (is_array($res_arr)){
// 					echo "<br/>start-time:".$half_time ."||end-time: ".$start_time ."||===>Return-value".p($res_arr) ;
					$Jisun_sumof_month=Jisun_sumof_month($res_arr,$order_str,$str);
					$res=$Jisun_sumof_month;
				}
				// 		echo "<br/>结束时间:".$start_time;
// 				echo "<br/>start-time:".$half_time ."||end-time: ".$start_time ."||===>Return-value".p($res_arr) ;
				if ($res_arr) {
					return $res;
					// $this->success('查询成功');
				} else {
					return false;
					/*
					 * echo "Query In table ins_multi_quote Faild."; exit ();
					*/
				}
				
				break;
		}
		// 查询的某个时间半小时内的支付数量,(包含流程走完和没走完的)
		$Ins_multi_quote = M ( $tb, null, $db );
		$map ["$order_str"] = array (
				array (
						'gt',
						$half_time 
				),
				array (
						'lt',
						$start_time 
				) 
		);
	
// 		$res_arr = $Ins_multi_quote->where ( $map )->field($str)->select();
		$res_arr = $Ins_multi_quote->where ( $map )->field($str)->select();
		$end_arr =end($res_arr);
		$res=$end_arr["$str"];
// 		echo "<br/>结束时间:".$start_time;
// 		echo "<br/>start-time:".$half_time ."||end-time: ".$start_time ."===>Return-value".p($res_arr) ;
		// $res = $Ins_multi_quote->where ( $map )->buildSql();
		if ($res_arr) {
			return $res;
			// $this->success('查询成功');
		} else {
			return false;
			/*
			 * echo "Query In table ins_multi_quote Faild."; exit ();
			 */
		}
		
		/*
		 * // 将查询的值插入自由数据库中 $Server_detail = M ( 'status_ins_multi_quote', null, 'DB_CONFIG' ); // $resvalue=$Server_detail->where('desc status_ins_multi_quote')->select(); $resvalue = $Server_detail->query ( 'desc status_ins_multi_quote' ); if ($resvalue) { // dump($resvalue); // $this->success('查询成功'); } else { echo "Query In table status_ins_multi_quote Faild."; exit (); }
		 */
	}
	// 每天显示的数据表
	// 取从0点开始到当前时间的支付数量,默认的时间间隔为半小时&&打印图表
	public function show_ins_multi_quote($end_date,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit) {
// 		$end_date = I ( 'end_date', '2014-08-06 21:30:00', 'htmlspecialchars' );
		// $end_date=I('end_date',date('Y-m-d H:i:s'),'htmlspecialchars');//默认取当前时间
		$date_array = get_dates ( $end_date );
		$res_array = array ();
		$xvalue = array ();
		$yvalue = array ();
		foreach ( $date_array as $key => $val ) {
			$count = $this->getgroup($val, $data_interval = 'day',$tb,$db,$str,$order_str);
			if ($count) {
				$res_array ["$val"] = $count;
				// echo $val."=". $count."<br/>";
				$xvalue [] = date ( 'H:i', strtotime ( $val ) ); // x轴上只需要显示时间
				$yvalue [] = $count;
			}
		}
		$total = end( $yvalue );
		$Getdate = date ( 'Y-m-d', strtotime ( $end_date ) );
		$xnum = array_sum ( $xvalue );
		if ($xnum == 0) {
			$xvalue [0] = $end_date;
		}
		if ($total == 0) {
			$yvalue [0] = $total;
		}
		// 打印图表
		$receive = array (
				'title' => I ( 'title', $title  .'['. $Getdate . ']' . '     总量: ' . $total, 'htmlspecialchars' ), // 'htmlspecialchars'实体化为字符串,防止攻击等
				'unit' => I ( 'unit', $unit, 'htmlspecialchars' ),
				'type' => I ( 'type', 'line', 'htmlspecialchars' ),
				'xname' => json_encode ( $xvalue ),
				'value' => json_encode ( $yvalue ) 
		);
		return $receive;
		/*
		 * $this->receive = $receive; $this->display ( 'showecharts' );
		 */
	}
	// 每周显示的数据表
	public function show_week_ins_multi_quote($end_date,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit) {
		// $end_date = I ( 'end_date', '2014-08-10 21:30:00', 'htmlspecialchars' );
		// $end_date=I('end_date',date('Y-m-d H:i:s'),'htmlspecialchars');//默认取当前时间
		$date_array = get_weeks ( $end_date );
		$res_array = array ();
		$xvalue = array ();
		$yvalue = array ();
		foreach ( $date_array as $key => $val ) {
			$count = $this->getgroup ( $val, $data_interval = 'week',$tb,$db,$str,$order_str);
			if ($count) {
				$res_array ["$val"] = $count;
				// echo $val."=". $count."<br/>";
				$xvalue [] = transition ( $val ); // x轴上只需要显示星期
				                                  // $xvalue [] = date ( 'Y-m-d H:i:s', strtotime ( $val ) ); // x轴上只需要显示时间
				$yvalue [] = $count;
			}
		}
		$total = array_sum( $yvalue );
		$Getdate = date ( 'Y-m-d', strtotime ( $end_date ) );
		// 打印图表
		$receive_every = array (
				'title' => I ( 'title', $title  .'['. $Getdate . ']' . '     总量: ' . $total, 'htmlspecialchars' ), // 'htmlspecialchars'实体化为字符串,防止攻击等
				'unit' => I ( 'unit', $unit, 'htmlspecialchars' ),
				'type' => I ( 'type', 'line', 'htmlspecialchars' ),
				'xname' => json_encode ( $xvalue ),
				'value' => json_encode ( $yvalue ) 
		);
		return $receive_every;
		
		/*
		 * $this->receive_every = $receive_every; $this->display ( 'showecharts' );
		 */
	}
	
	// 每月显示的数据表
	public function show_month_ins_multi_quote($end_date,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit) {
// 		$end_date = I ( 'end_date', '2014-08-10 21:30:00', 'htmlspecialchars' );
		// $end_date=I('end_date',date('Y-m-d H:i:s'),'htmlspecialchars');//默认取当前时间
		$date_array = get_months ( $end_date );
		$res_array = array ();
		$xvalue = array ();
		$yvalue = array ();
		foreach ( $date_array as $key => $val ) {
			$count = $this->getgroup ( $val, $data_interval = 'month',$tb,$db,$str,$order_str );
			if ($count) {
				$res_array ["$val"] = $count;
				// echo $val."=". $count."<br/>";
				$month_num = date('m', strtotime($val)) + 0;
				$xvalue [] = $month_num."月"; // x轴上显示月份
				                     // $xvalue [] = date ( 'Y-m-d H:i:s', strtotime ( $val ) ); // x轴上只需要显示时间
				$yvalue [] = $count;
			}
		}
		$total = array_sum ( $yvalue );
		$Getdate = date ( 'Y-m-d', strtotime ( $end_date ) );
		// 打印图表
		$receive_every = array (
				'title' => I ( 'title', $title  .'[' . $Getdate . ']' . '     总量: ' . $total, 'htmlspecialchars' ), // 'htmlspecialchars'实体化为字符串,防止攻击等
				'unit' => I ( 'unit', $unit, 'htmlspecialchars' ),
				'type' => I ( 'type', 'line', 'htmlspecialchars' ),
				'xname' => json_encode ( $xvalue ),
				'value' => json_encode ( $yvalue ) 
		);
		return $receive_every;
		/* 
		$this->receive_month = $receive_every;
		$this->display ( 'showecharts' ); */
	}
	
	//打印日、周、月图表
	public function show_all_ins_multi_quote() {
		$time = I ( 'end_date', '2014-08-06 21:30:00', 'htmlspecialchars' );
		$receive = $this->show_ins_multi_quote ( $time );
		$receive_every = $this->show_week_ins_multi_quote ( $time );
		$receive_month = $this->show_month_ins_multi_quote ($time);
		$this->receive = $receive;
		$this->receive_every = $receive_every;
		$this->receive_month = $receive_month ;
		$this->display ( 'showecharts' );
	}
	
	//日图表 、 动态输出图表模版
	 public  function  echartofday(){
	 	//$end_date,$tb,$db='DB_COLLECT',$str,$order_str='time'
	 	$time = I ( 'end_date', date('Y-m-d H:i:s'), 'htmlspecialchars' );
	 	//默认是对五区的初审变量赋值
	 	$tb=I('tb','yunwei_data_part_five');
	 	$maintitle=I('maintitle','[生产五区]');
	 	
// 	 	$receive = $this->show_ins_multi_quote ( $time );
	 	$url=U('Admin/Show/echartofday','','','',true);
	 	$weekurl=U('Admin/Show/echartofweek','','','',true);
	 	$monthurl=U('Admin/Show/echartofmonth','','','',true);
	 	//初审--start
	 	$str=I('str','verify');
	 	$title=I('title',$maintitle.' 每天初审表');
	 	$unit=I('unit','已经初审数量');
	 	$receive = R('Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));  //初审
		//报价--start
	 	$str=I('str','quote');
	 	$title=I('title',$maintitle.' 每天报价表');
	 	$unit=I('unit','已经报价数量');
	 	$quote = R('Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_every=$quote;
	 	//报价--end
	 	//核保-start
	 	$str=I('str','insure');
	 	$title=I('title',$maintitle.' 每天核保表');
	 	$unit=I('unit','已经核保数量');
	 	$insure = R('Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_month=$insure;
	 	//核保--end
	 	//支付--start
	 	$str=I('str','pay');
	 	$title=I('title',$maintitle.' 每天支付表');
	 	$unit=I('unit','已经支付数量');
	 	$pay = R('Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_pay=$pay;
	 	//支付--end
	    $this->url=$url;
	 	$this->weekurl=$weekurl;
	 	$this->monthurl=$monthurl;
	 	$this->receive = $receive;
// 	 	$this->display ( 'show_day_charts' );
	 	$this->display ( 'showcharts' );
	 }
	 
	 //周图表 、 动态输出图表模版
	 public  function  echartofweek(){
	 	$time = I ( 'end_date', date('Y-m-d H:i:s'), 'htmlspecialchars' );
	 	//默认是对五区的初审变量赋值
	 	$tb=I('tb','yunwei_data_part_five');
	 	$str=I('str','verify');
	 	$maintitle=I('maintitle','[生产五区]');
	 	$title=I('title', $maintitle.' 每周初审表');
	 	$unit=I('unit','已经初审数量');
	 	// 	 	$receive = $this->show_ins_multi_quote ( $time );
	 	$url=U('Admin/Show/echartofday','','','',true);
	 	$weekurl=U('Admin/Show/echartofweek','','','',true);
	 	$monthurl=U('Admin/Show/echartofmonth','','','',true);
	 	$receive = R('Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	//报价--start
	 	$str=I('str','quote');
	 	$title=I('title',$maintitle.' 每周报价表');
	 	$unit=I('unit','已经报价数量');
	 	$quote = R('Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_every=$quote;
	 	//报价--end
	 	//核保-start
	 	$str=I('str','insure');
	 	$title=I('title',$maintitle.' 每周核保表');
	 	$unit=I('unit','已经核保数量');
	 	$insure = R('Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_month=$insure;
	 	//核保--end
	 	//支付--start
	 	$str=I('str','pay');
	 	$title=I('title',$maintitle.' 每周支付表');
	 	$unit=I('unit','已经支付数量');
	 	$pay = R('Show/show_week_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_pay=$pay;
	 	//支付--end
	 	$this->url=$url;
	 	$this->weekurl=$weekurl;
	 	$this->monthurl=$monthurl;
	 	$this->receive = $receive;
// 	 	$this->display ( 'show_week_charts' );
	 	$this->display ( 'showcharts' );
	 	
	 }
	 
	 //月图表 、 动态输出图表模版
	 public  function  echartofmonth(){
	 	$time = I ( 'end_date', date('Y-m-d H:i:s'), 'htmlspecialchars' );
	 	//默认是对五区的初审变量赋值
	 	$tb=I('tb','yunwei_data_part_five');
	 	$str=I('str','verify');
	 	$maintitle=I('maintitle','[生产五区]');
	 	$title=I('title',$maintitle.' 每月初审表');
	 	// 	 	$receive = $this->show_ins_multi_quote ( $time );
	 	$url=U('Admin/Show/echartofday','','','',true);
	 	$weekurl=U('Admin/Show/echartofweek','','','',true);
	 	$monthurl=U('Admin/Show/echartofmonth','','','',true);
	 	$receive = R('Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	//报价--start
	 	$str=I('str','quote');
	 	$title=I('title',$maintitle.' 每月报价表');
	 	$unit=I('unit','已经报价数量');
	 	$quote = R('Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_every=$quote;
	 	//报价--end
	 	//核保-start
	 	$str=I('str','insure');
	 	$title=I('title',$maintitle.' 每月核保表');
	 	$unit=I('unit','已经核保数量');
	 	$insure = R('Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_month=$insure;
	 	//核保--end
	 	//支付--start
	 	$str=I('str','pay');
	 	$title=I('title',$maintitle.' 每月支付表');
	 	$unit=I('unit','已经支付数量');
	 	$pay = R('Show/show_month_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time',$title,$unit));
	 	$this->receive_pay=$pay;
	 	//支付--end
	 	$this->url=$url;
	 	$this->weekurl=$weekurl;
	 	$this->monthurl=$monthurl;
	 	$this->receive = $receive;
	 	$this->display ( 'showcharts' );
// 	 	$this->display ( 'show_month_charts' );
	 }
	
	public  function  echart(){
		$time = I ( 'end_date', date('Y-m-d H:i:s'), 'htmlspecialchars' );
		// 	 	$receive = $this->show_ins_multi_quote ( $time );
		//默认是对五区的初审变量赋值
		$tb=I('tb','yunwei_data_part_five');
		$str=I('str','verify');
		$url=U('Admin/Show/echartofday','','','',true);
		$weekurl=U('Admin/Show/echartofweek','','','',true);
		$monthurl=U('Admin/Show/echartofmonth','','','',true);
		$receive = R('Show/show_ins_multi_quote',array($time,$tb,$db='DB_COLLECT',$str,$order_str='time'));
		$this->url=$url;
		$this->weekurl=$weekurl;
		$this->monthurl=$monthurl;
		$this->receive = $receive;
		$this->display ( 'showcharts' );
	}
	
	// 调试页面
	public function EchartIndex() {
		$test = 'this is a test' ;
		$this->test = $test;
		$this->display ('test');
	}
	
	
}
  




