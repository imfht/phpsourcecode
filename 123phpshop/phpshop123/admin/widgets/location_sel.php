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
 
//	将配送区域用；隔开

 if(isset($row_shipping_method_area['area'])){
	$area=explode(";",$row_shipping_method_area['area']);
 }
 
mysql_select_db($database_localhost, $localhost);
$query_areas = "SELECT id, name FROM area WHERE pid = 0";
$areas = mysql_query($query_areas, $localhost) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);
$totalRows_areas = mysql_num_rows($areas);


mysql_select_db($database_localhost, $localhost);
$query_areas_for_city = "SELECT id, name FROM area WHERE pid = 0";
$areas_for_city = mysql_query($query_areas_for_city, $localhost) or die(mysql_error());
  
 
mysql_select_db($database_localhost, $localhost);
$query_areas_for_district = "SELECT id, name FROM area WHERE pid = 0";
$areas_for_district = mysql_query($query_areas_for_district, $localhost) or die(mysql_error());


function should_check($area_name,$area_array){
 	for($i=0;$i<count($area_array);$i++){
		if(strpos($area_array[$i],$area_name)>-1){
			return true; 
		}
	}
 	return false;
}

?> 
<script src="/js/jquery-1.7.2.min.js"></script>
<script src="/js/jsAddress.js"></script>
<script>
function show_city(province_name){
   $(".city_list_item[province_name="+province_name+"]").show();
   $(".city_list_item[province_name!="+province_name+"]").hide();
   $(".district_list_item").hide();
}

function show_disticts(city_name){
   $(".district_list_item[city_name="+city_name+"]").show();
   $(".district_list_item[city_name!="+city_name+"]").hide();
}
</script>
<link href="/css/common_admin.css" rel="stylesheet" type="text/css" />

  <p>选择的列表</p>
  <table width="100%" border="0" class="phpshop123_list_box" id="area_selected">
    <tr>
      <td>省</td>
      <td>市</td>
      <td>区县</td>
    </tr>
	<?php if(isset($area)){ ?>
	<?php foreach($area as $area_item){ if($area_item!=''){ $location_array=explode('_',$area_item);if($area_item!=''){?>
	 <tr class="selected_area_row" province_name="<?php echo $location_array[0];?>" city_name="<?php echo $location_array[1];?>" district_name="<?php echo $location_array[2];?>">
      <td class="province_selected"><?php echo $location_array[0];?></td>
      <td class="city_selected"><?php echo $location_array[1];?></td>
      <td class="district_selected"><?php echo $location_array[2];?></td>
    </tr>
	<?php } } }} ?>
  </table>
  <p>&nbsp;</p>
  <table width="33%" border="0" id="areas_box">
  <tr>
    <td width="119" valign="top"><table width="118" border="0" id="province_box">
  
    <tr>
      <td width="108" valign="top" > 
	  <input value="*" type="checkbox" id="country" onChange="select_all()" />
	  <span>全国</span></br>
	  <?php do { ?>
        <div style="cursor:pointer;" onMouseOver="show_city('<?php echo $row_areas['name']; ?>')">
<input type="checkbox"  class="province" province_name="<?php echo $row_areas['name']; ?>" id="province_<?php echo $row_areas['id']; ?>" value="<?php echo $row_areas['id']; ?>" onclick="select_province('<?php echo $row_areas['name']; ?>')" <?php if(isset($area) && (in_array($row_areas['name']."_*_*",$area) || should_check($row_areas['name']."_",$area))){ ?> checked <?php } ?>>
<span area_pos="<?php if(isset($area)){echo should_check($row_areas['name']."_",$area);};?>"><?php echo $row_areas['name']; ?></span></br>
</div>
		 <?php } while ($row_areas = mysql_fetch_assoc($areas)); ?>
		</td>
    </tr>
   
</table></td>
    <td width="161" valign="top" id="city_box">
	<?php while($row_areas_for_city = mysql_fetch_assoc($areas_for_city)){ ?>
	<div class="city_list_item" province_name="<?php echo $row_areas_for_city['name']; ?>" id="city_<?php echo $row_areas_for_city['id'];?>" style="display:none;cursor:pointer;">
 		<?php 
			mysql_select_db($database_localhost, $localhost);
			$query_cities = "SELECT * FROM area WHERE pid = ".$row_areas_for_city['id'];
			$cities = mysql_query($query_cities, $localhost) or die(mysql_error());
			$totalRows_cities = mysql_num_rows($cities);
			while($row_cities = mysql_fetch_assoc($cities)){
		?>
       	<input type="checkbox" class="city"  province_name="<?php echo $row_areas_for_city['name']; ?>" city_name="<?php echo $row_cities['name']; ?>" value="<?php echo $row_cities['id'];?>" onclick="select_city('<?php echo $row_cities['name']; ?>')"  <?php if(isset($area) && (in_array($row_areas_for_city['name']."_".$row_cities['name']."_*",$area) || in_array($row_areas_for_city['name']."_*_*",$area) || should_check($row_areas['name']."_".$row_cities['name']."_",$area))){ ?> checked <?php } ?>><span  onMouseOver="show_disticts('<?php echo $row_cities['name']; ?>')"><?php echo $row_cities['name']; ?></span></br>
 	 	<?php } ?>
	  </div>
	<?php } ?>	</td><td width="255" valign="top">
		<?php 
		// 获取各个省份的信息
		while($row_areas_for_district = mysql_fetch_assoc($areas_for_district)){
//			获取这个省份下面的城市的信息
			mysql_select_db($database_localhost, $localhost);
			$query_cities = "SELECT * FROM area WHERE pid = ".$row_areas_for_district['id'];
			$cities = mysql_query($query_cities, $localhost) or die(mysql_error());
			$totalRows_cities = mysql_num_rows($cities);
			if($totalRows_cities>0){
			while($row_cities = mysql_fetch_assoc($cities)){
				//	 获取这个城市下面的区县的信息
				mysql_select_db($database_localhost, $localhost);
				$query_distict = "SELECT * FROM area WHERE pid = ".$row_cities['id'];
				$disticties = mysql_query($query_distict, $localhost) or die(mysql_error());
				$totalRows_distict = mysql_num_rows($disticties);?>
				<div class="district_list_item" city_name="<?php echo $row_cities['name'];?>" style="display:none;" >
 				<?php 	while($row_distict = mysql_fetch_assoc($disticties)){?>
						<input class="district" type="checkbox" province_name="<?php echo $row_areas_for_district['name']; ?>" city_name="<?php echo $row_cities['name']; ?>" district_name="<?php echo $row_distict['name']; ?>" value="<?php echo $row_distict['id'];?>" onclick="select_district('<?php echo $row_distict['name']; ?>')"  <?php if(isset($area) && (in_array($row_areas_for_district['name']."_".$row_cities['name']."_".$row_distict['name'],$area) || in_array($row_areas_for_district['name']."_*_*",$area) || in_array($row_areas_for_district['name']."_".$row_cities['name']."_*",$area)) ){ ?> checked <?php } ?>><?php echo $row_distict['name'];?></br>
 				<?php 	} ?>
 				</div>
				<?php 	}
			}
		}
		?>   </td> 
   </tr>
</table>
