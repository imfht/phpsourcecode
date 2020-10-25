<?php	 
/*
 * 前台判断是否登录管理
 */	 
class WxAuth extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->check_login();
	}	
	//检查是否有登录
	public function check_login() {
		if(isset($_COOKIE['member_acct'])){
			$member_acct	=$_COOKIE['member_acct'];
			$sesseion_txt	=$_COOKIE['sesseion_txt'];
			$member_id		=$_COOKIE['member_id'];
			@$member_name	=$_COOKIE['member_name'];

			if( empty($member_acct) || empty( $member_id ) ){
				header("location:/index.php/home/Login/main/");
				exit;
				//$this->location( "未登录", '/home/Login/main/', 0 );
			}else{
					$sql = "select sesseion_txt from fly_member where account='$member_acct';";	
					$one = $this->C($this->cacheDir)->findOne($sql);
/*				echo $sql;
				print_r($one);
					//print_r($_COOKIE);
				echo "Cook:<br>";
				echo $sesseion_txt."<br>";
				echo "SQL=<br>";
				echo $one['sesseion_txt'];*/
				
					if($sesseion_txt!=$one['sesseion_txt']){
						header("location:/index.php/home/Login/main/");
						exit;
					}
			}
		}else{
			header("location:/index.php/home/Login/main/");
			exit;
			$this->location( "未登录", '/home/Login/main/', 0 );
		}
	}

}//
?>