<?php	 
class WxApi extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
	}	
	//每天执行之后就生成一条记录,在每晚24点前释放当天数据
	public function member_integral_day_add(){
		$dt	= date("Y-m-d H:i:s",time());
		
		//查询交换比例
		$day_sql="select * from fly_conf_day limit 0,1";
		$day_cfg=$this->C($this->cacheDir)->findOne($day_sql);
		
		$sql ="select * from fly_member";
		$list= $this->C($this->cacheDir)->findAll($sql);
		
		foreach($list as $key=>$row){
			$member_id		=$row['id'];
			$member_integral=$row['integral'];
			$day_integral  =$member_integral*$day_cfg['rate'];
			
			//判断当天是否释放了
			$sql="select * from fly_integral_day where member_id='$member_id' and TO_DAYS(adt) = TO_DAYS(NOW())";
			$one= $this->C($this->cacheDir)->findOne($sql);
			if(empty($one)){
				//插入兑换记录
				$sql="insert into fly_integral_day(member_id,balance,integral,adt) 
									values('$member_id','$day_integral','-$day_integral','$dt')";
				$rtn=$this->C($this->cacheDir)->update($sql);
			}
		}		
		return true;
	}	

}//
?>