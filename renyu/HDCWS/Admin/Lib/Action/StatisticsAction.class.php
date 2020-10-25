<?php

//统计类
class StatisticsAction extends GlobalAction {

	public function getList(){
		
		$start = intval($_GET['start']);
		
		$limit = intval($_GET['limit']);
		
		if(empty($start)) $start = 0;
		
		if(empty($limit)) $limit = 20;
		
		$sta = D('statistics');
		
		$count = $sta -> query('select count(*) counts from ' . C('DB_PREFIX') . 'statistics s');
		
		$count = empty($count) ? 0 : $count[0]['counts'];
		
		$sql = 'select s.* from ' . C('DB_PREFIX') . 'statistics s order by time desc limit '. $start . ',' . $limit;
		
		$list = $sta -> query($sql);
		
		echo json_encode(array('list' => $list, 'total' => $count, 'success' => true));
		
	}
	
	public function getChartList(){
		
		$pre = C('DB_PREFIX');
	
		$key = $_GET['key'];
		
		$sta = D('statistics');

		if($key == 2){//近一周
			
			$startDate = date('Y-m-d' , strtotime('-7 day'));
			
			$endDate = date('Y-m-d');

			$sql = 'select date_format(s.time, "%Y-%m-%d") date,count(*) num from ' . $pre . 'statistics s where s.time < "' . $endDate . '" and s.time > "' . $startDate . '" group by date';

			$list = $sta -> query($sql);
			
			$dateArray = array();
			
			for($i = 7; $i > 0; $i--){
			
				$dateArray[] = date('Y-m-d' , strtotime('-' . $i . ' day'));
			
			}

			$list = $this -> fullArrayData($dateArray, $list);

		}else if($key == 3){//近一月
				
			$startDate = date('Y-m-d' , strtotime('-31 day'));
				
			$endDate = date('Y-m-d');
			
			$sql = 'select date_format(s.time, "%d") date,count(*) num from ' . $pre . 'statistics s where s.time < "' . $endDate . '" and s.time > "' . $startDate . '" group by date';
			
			$list = $sta -> query($sql);
			
			$dateArray = array();
			
			for($i = 30; $i > 0; $i--){
			
				$dateArray[] = date('d' , strtotime('-' . $i . ' day'));
			
			}

			$list = $this -> fullArrayData($dateArray, $list);
				
		}else if($key == 4){//近一年

			$startDate = date('Y-m' , strtotime('-365 day'));

			$endDate = date('Y-m');
			
			$sql = 'select date_format(s.time, "%Y-%m") date,count(*) num from ' . $pre . 'statistics s where s.time < "' . $endDate . '" and s.time > "' . $startDate . '" group by date';
			
			$list = $sta -> query($sql);

			$dateArray = array();

			for($i = 365; $i > 0; $i -= 27){
				
				$temp = date('Y-m' , strtotime('-' . $i . ' day'));
				
				if($dateArray[count($dateArray) - 1] == $temp) continue;
			
				else $dateArray[] = $temp;
			
			}

			$list = $this -> fullArrayData($dateArray, $list);
				
		}else{

			if($key == 1)  $date = date('Y-m-d' , strtotime('-1 day'));//昨天
				
			else $date = date('Y-m-d');//今天
			
			$sql = 'select date_format(s.time, "%H") date,count(*) num from ' . $pre . 'statistics s where s.time like "%' . $date . '%" group by date';
			
			$list = $sta -> query($sql);
			
			$list = $this -> fullData(0, 23, $list);
			
		}
	
		echo json_encode(array('list' => $list, 'success' => true));
	
	}
	
	protected function fullData($min = 0, $max = 23, $list){
		
		if(empty($list)) $list = array();
		
		$curMin = $min;
		
		for($i = 0; $i <= ($max - $min); $i++){
		
			if(empty($list[$i])){
					
				array_splice($list, $i, 0, array(array('date' => substr('0' . $curMin, -2), 'num' => 0)));
		
			}else if($list[$i]['date'] != substr('0' . $curMin, -2)){
					
				array_splice($list, $i, 0, array(array('date' => substr('0' . $curMin, -2), 'num' => 0)));
		
			}
			
			$curMin++;
		
		}
		
		return $list;
		
	}
	
	protected function fullArrayData($array, $list){
	
		if(empty($list)){
			
			$list = array();
			
			$empty = true;
			
		}

		$data = array();

		foreach($array as $key => $val){
			
			$data[] = array('date' => $val, 'num' => 0);
			
			foreach($list as $index => $row){
			
				if($row['date'] == $val) $data[count($data) - 1] = array('date' => $val, 'num' => $row['num']);
			
			}
		
		}
		
		return $data;
	
	}
	
	//删除
	public function del(){
	
		$id = $_POST['id'];
	
		$result = 1;
	
		$prefix = C('DB_PREFIX');
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$sta = D('statistics');
	
			$result = $sta -> delete($id);
	
			$result = $result > 0 ? 1 : 0;
	
		}else $result = 0;
	
		echo $result;
	
	}

}