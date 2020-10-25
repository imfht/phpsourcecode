<?php 
/**
接口类
 */
class CommFront extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {

	}	
	public function get_images_url($imgurl=null){
		if(empty($imgurl)){
			$img="<img src='/upload/images/defaultimg.jpg'>";
		}else{
			$img="<img src='$imgurl'>";//返回img
		}
		return $img;
	}
	
}//end class
?>
