<?php
namespace App\Controllers;
class BaseController{
	const PAGE_SIZE = 10; // 分页时每页显示记录条数
	
	public $error = '';
    public $status = -1;

	function setError($msg = '',$status = -1){
		if (empty($msg)) {
			$msg = '操作失败';
		}
		$this->error = $msg;
        $this->status = $status;
		return -1;
	}	

	function getError(){
		if(!empty($this->error)){
			return ['message'=>$this->error,'status'=>$this->status];
		}else{

			return ['message'=>'操作成功','status'=>1];
		}
	}

	public function toArray($object)
	{
	    $new = [];
	    if (empty($object)) {
	        $object = [];
	    }
	    
	    foreach ($object as $key => $value) {
	        if (is_array($value) || is_object($value)) {
	            $new[$key] =  $this->toArray($value);
	        }else{
	            $new[$key] = (string) $value;
	        }
	    }
	    return $new;
	}
	/** 
    * @desc 根据两点间的经纬度计算距离 
    * @param float $lat 纬度值 
    * @param float $lng 经度值 
    */
    protected function getDistance($lat1, $lng1, $lat2, $lng2) 
    { 
        $earthRadius = 6367000; //approximate radius of earth in meters 
         
        $lat1 = ($lat1 * pi() ) / 180; 
        $lng1 = ($lng1 * pi() ) / 180; 
         
        $lat2 = ($lat2 * pi() ) / 180; 
        $lng2 = ($lng2 * pi() ) / 180; 
         
         
        $calcLongitude = $lng2 - $lng1; 
        $calcLatitude = $lat2 - $lat1; 
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2); 
        $stepTwo = 2 * asin(min(1, sqrt($stepOne))); 
        $calculatedDistance = $earthRadius * $stepTwo; 
         
        return round($calculatedDistance); 
    } 
	
}
?>