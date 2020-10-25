<?php
/**
 * 123PHPSHOP
 * ============================================================================
 * 版权所有 2015 上海序程信息科技有限公司，并保留所有权利。
 * 网站地址: http://www.123PHPSHOP.com；
 * ----------------------------------------------------------------------------
 * 这是一个免费的软件。您可以在商业目的和非商业目的地前提下对程序除本声明之外的
 * 代码进行修改和使用；您可以对程序代码以任何形式任何目的的再发布，但一定请保留
 * 本声明和上海序程信息科技有限公司的联系方式！本软件中使用到的第三方代码版权属
 * 于原公司所有。上海序程信息科技有限公司拥有对本声明和123PHPSHOP软件使用的最终
 * 解释权！
 * ============================================================================
 *  作者:	123PHPSHOP团队
 *  手机:	13391334121
 *  邮箱:	service@123phpshop.com
 */
?>
<?php
 
function get_catalog_by_id($id){
	
	//mysql_select_db($database_localhost);
	$query_get_catalog_by_id = "SELECT * FROM `catalog` WHERE id = $id";
	$get_catalog_by_id = mysql_query($query_get_catalog_by_id) or die(mysql_error());
 	$totalRows_get_catalog_by_id = mysql_num_rows($get_catalog_by_id);
 	if($totalRows_get_catalog_by_id==0){
		return false;
	}
	
	return mysql_fetch_assoc($get_catalog_by_id);
}

function get_catalog_by_pid($pid){
 	$query_get_catalog_by_id = "SELECT * FROM `catalog` WHERE pid = $pid";
	$get_catalog_by_id = mysql_query($query_get_catalog_by_id)  or die(mysql_error()) ;
	$row_get_catalog_by_id = mysql_fetch_assoc($get_catalog_by_id);
	$totalRows_get_catalog_by_id = mysql_num_rows($get_catalog_by_id);
	if($totalRows_get_catalog_by_id==0){
		return false;
	}
 	return $row_get_catalog_by_id;
}

function get_catalog_path($pid){
		
	//检查pid是否是数组，如果不是数组，那么直接返回‘’；
 		if(!is_array($pid)){
			return '';
		}
		
	//	如果是数组，那么检查pid的第一个元素所代表的分类是否存在，如果不存在，那么直接返回pid
		$fist_cata_pid=get_catalog_by_id($pid[0]);
		   
		if(!$fist_cata_pid){
			return '';
		}
		
	//		如果存在，那么获取这个分类的pid，如果这个分类的pid是0的话，那么将这个pid压入原来的数组，然后返回
		
	 	if($fist_cata_pid['pid']=='0'){
			$result=array();
			foreach($pid as $item){
				$result[]=$item;
			}
			return implode('|',$result);
		}
		
		array_unshift($pid,$fist_cata_pid['pid']);
	//	 	如果不是0的话，那么将这个pid压入原来的数据，然后继续回调
		return get_catalog_path($pid);
}
  ?>