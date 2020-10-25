<?php
require_once(ABSPATH.'/inc/models/user.php');
class user extends c_user{

	function user_power_list_select($name,$select=null,$is_list=true,$style=0)
	{
		$temp_arr=array();
		if($is_list)
		{
		$temp_arr = array( '0'=>'匿名',
						   '1'=>'普通会员',
						   '2'=>'vip1级用户',
						   '3'=>'vip2级用户',
						   '4'=>'vip3级用户',
						   '5'=>'vip4级用户',
						);
		}else{
		$temp_arr = array( '1'=>'普通会员',
						   '2'=>'vip1级用户',
						   '3'=>'vip2级用户',
						   '4'=>'vip3级用户',
						   '5'=>'vip4级用户',
							);
		}
		if(!$style)
		{
			select($temp_arr,$name,intval($select));
		}
		else
		{
			foreach ($temp_arr as $k=>$v)
			{
				?>
				<li><a href="javascript:;" onclick="changesel(2,'<?php echo $k ?>',this)" ><?php echo $v ?></a></li>
				<?php
			}
		}
	}
}
?>