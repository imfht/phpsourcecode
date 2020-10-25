<?php
class EchartsAction extends Action {
	public function index() {
		U('forwording','','',true);
	}
	
	/*
	 * forwording: Echarts图表总的转发控制 $query:查询的方式 $type:查询的类型 根据查询的方式和类型去寻找相应的控制器
	 */
	public function forwording() {
		/*
		 * 如果查询的的方式为空,默认为每天. 如果查询的类型为空,默认为初审. 如果查询的日期为空,默认为当前日期.如果查询的分区空默认为第五分区
		 */
		$query_type = I ( 'query_type', 'Day' );
		$query_date = I ( 'query_date', date ( 'Y-m-d H:i:s' ) );
		$depart = I('depart','');
		$type = I ( 'type', 'Verfy' );
		
		/* 根据查询的类型($type)判断进入哪一个方法去查询 */
		switch ($type) {
			case 'Verfy' :
				U ( 'Admin/Verfy/index', array (
						'type' => '分区初审',
						'query_type' => $query_type,
						'query_date' => $query_date, 
						'depart' => $depart
				), '', true );
				break;
			case 'Quote' :
				U ( 'Admin/Quote/index', array (
				'type' => '分区报价',
				'query_type' => $query_type,
				'query_date' => $query_date,
				'depart' => $depart
				), '', true );
				break;
			case 'Insure' :
				U ( 'Admin/Insure/index', array (
				'type' => '分区核保',
				'query_type' => $query_type,
				'query_date' => $query_date,
				'depart' => $depart
				), '', true );
				break;
			case 'Pay' :
				U ( 'Admin/Pay/index', array (
				'type' => '分区报价',
				'query_type' => $query_type,
				'query_date' => $query_date,
				'depart' => $depart
				), '', true );
				break;
		}
		
		// $this->ajaxReturn ( $data, json );
	}
	
	public function test() {
		$this->display ( 'Public/doc/example/date_time' );
	}
}
