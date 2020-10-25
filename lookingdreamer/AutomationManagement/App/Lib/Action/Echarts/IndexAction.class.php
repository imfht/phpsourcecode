<?php
class IndexAction extends Action {
	// API直接调用和显示
	// GET方式传参,并显示图表数据
	// URL调用方式: http://192.168.16.88/App/index.php/Admin/Index/index/title/一区一周走单支付表/type/line/unit/已经支付数量/xname/['周一','周二','周三','周四','周五']/value/[235 ,168 ,159 ,190 ,172]
	// title图表标题,unit单位/点击时现实的提示 ,type显示图的类型如line则是折线 bar是柱状 ,xname是x轴上的显示,value是y轴上的显示
	public function index() {
		echo "11";
		$receive = array (
				'title' => I ( 'title', '', 'htmlspecialchars' ), // 'htmlspecialchars'实体化为字符串,防止攻击等
				'unit' => I ( 'unit', '', 'htmlspecialchars' ),
				'type' => I ( 'type', '', 'htmlspecialchars' ),
				'xname' => I ( 'xname', '', 'htmlspecialchars' ),
				'value' => I ( 'value', '', 'htmlspecialchars' ) 
		);
		$this->receive = $receive;
		$this->display ( 'showecharts' );
	}
	
	// 获取某个时间半个小时的之前的支付数量
	// 获取某天的支付数量
	// 获取某个月的支付数量
	public function getgroup($start_time, $data_interval = 'day') {
		// 获取时间间隔
		if (! isdate ( $start_time )) {
			echo $start_time;
			die ();
			$this->error ( '传入时间格式不正确.<br/>For example:2014-08-04 16:30:00' );
		}
		switch ($data_interval) {
			case "day" :
				$half_time = mdate_arr ( $start_time );
				break;
			case "week" :
				$half_time = mdate_arr ( $start_time, 'week' );
				break;
			case "month" :
				$half_time = mdate_arr ( $start_time, 'month' );
				break;
		}
		// 查询的某个时间半小时内的支付数量,(包含流程走完和没走完的)
		$Ins_multi_quote = M ( 'ins_multi_quote', null, 'DB_Z1' );
		$map ['date_created'] = array (
				array (
						'gt',
						$half_time 
				),
				array (
						'elt',
						$start_time 
				) 
		);
		$map ['status'] = array (
				'in',
				'Payed,Finished' 
		);
		$res = $Ins_multi_quote->where ( $map )->count ();
// 		p($res);
		// $res = $Ins_multi_quote->where ( $map )->buildSql();
		if ($res) {
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
	public function show_ins_multi_quote($end_date) {
		// $end_date = I ( 'end_date', '2014-08-06 21:30:00', 'htmlspecialchars' );
		// $end_date=I('end_date',date('Y-m-d H:i:s'),'htmlspecialchars');//默认取当前时间
		$date_array = get_dates ( $end_date );
		$res_array = array ();
		$xvalue = array ();
		$yvalue = array ();
		foreach ( $date_array as $key => $val ) {
			$count = $this->getgroup ( $val );
			if ($count) {
				$res_array ["$val"] = $count;
				// echo $val."=". $count."<br/>";
				$xvalue [] = date ( 'H:i', strtotime ( $val ) ); // x轴上只需要显示时间
				$yvalue [] = $count;
			}
		}
		$total = array_sum ( $yvalue );
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
				'title' => I ( 'title', '每日支付表  [' . $Getdate . ']' . '     总量: ' . $total, 'htmlspecialchars' ), // 'htmlspecialchars'实体化为字符串,防止攻击等
				'unit' => I ( 'unit', '已经支付数量', 'htmlspecialchars' ),
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
	public function show_week_ins_multi_quote($end_date) {
		// $end_date = I ( 'end_date', '2014-08-10 21:30:00', 'htmlspecialchars' );
		// $end_date=I('end_date',date('Y-m-d H:i:s'),'htmlspecialchars');//默认取当前时间
		$date_array = get_weeks ( $end_date );
		$res_array = array ();
		$xvalue = array ();
		$yvalue = array ();
		foreach ( $date_array as $key => $val ) {
			$count = $this->getgroup ( $val, 'week' );
			if ($count) {
				$res_array ["$val"] = $count;
				// echo $val."=". $count."<br/>";
				$xvalue [] = transition ( $val ); // x轴上只需要显示星期
				                                  // $xvalue [] = date ( 'Y-m-d H:i:s', strtotime ( $val ) ); // x轴上只需要显示时间
				$yvalue [] = $count;
			}
		}
		$total = array_sum ( $yvalue );
		$Getdate = date ( 'Y-m-d', strtotime ( $end_date ) );
		// 打印图表
		$receive_every = array (
				'title' => I ( 'title', '每周支付表  [' . $Getdate . ']' . '     总量: ' . $total, 'htmlspecialchars' ), // 'htmlspecialchars'实体化为字符串,防止攻击等
				'unit' => I ( 'unit', '已经支付数量', 'htmlspecialchars' ),
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
	public function show_month_ins_multi_quote($end_date) {
// 		$end_date = I ( 'end_date', '2014-08-10 21:30:00', 'htmlspecialchars' );
		// $end_date=I('end_date',date('Y-m-d H:i:s'),'htmlspecialchars');//默认取当前时间
		$date_array = get_months ( $end_date );
		$res_array = array ();
		$xvalue = array ();
		$yvalue = array ();
		foreach ( $date_array as $key => $val ) {
			$count = $this->getgroup ( $val, 'month' );
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
				'title' => I ( 'title', '每月支付表  [' . $Getdate . ']' . '     总量: ' . $total, 'htmlspecialchars' ), // 'htmlspecialchars'实体化为字符串,防止攻击等
				'unit' => I ( 'unit', '已经支付数量', 'htmlspecialchars' ),
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
	
	// 调试页面
	public function test() {
		$test = get_months ( '2014-08-29 21:30:00' );
		$this->test = $test;
		$this->display ();
	}
}
  




