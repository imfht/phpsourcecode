<?php
/**
 * 地图信息
 * @author HumingXu E-mail:huming17@126.com
 */
$sn_mac = isset($_REQUEST['sn_mac']) && !empty($_REQUEST['sn_mac']) ? $_REQUEST['sn_mac']: '';
//中心点取搜索的一个平板
//$map_center_lat = isset($_REQUEST['map_center_lat']) && !empty($_REQUEST['map_center_lat']) ? $_REQUEST['map_center_lat']: '31.624171';
//$map_center_lng = isset($_REQUEST['map_center_lng']) && !empty($_REQUEST['map_center_lng']) ? $_REQUEST['map_center_lng']: '120.797521';
$map_points_str = '';
$map_type = isset($_REQUEST['map_type']) && !empty($_REQUEST['map_type']) ? $_REQUEST['map_type']: 'baidu';
$map_language = DZF_LANG;
if($map_language == 'sc'){
    $map_language = 'cn';
}
switch ($do) {
    case "index":
        $info_result = array();
        if($sn_mac){
            $info_sql = "SELECT * FROM ".DB::table('map_lng_lat')." WHERE sn_mac='".$sn_mac."' LIMIT 1";
            $info_result = DB::fetch_first($info_sql);
            if($info_result){
              $info_result['status_name'] = get_status($info_result['status']);   
            }
        }
		include template('admin/map/index');
        break;
    case "load":
	//DEBUG 查询
	if($sn_mac){
            $wheresql = " WHERE sn_mac = '".$sn_mac."' LIMIT 1";
            $query=DB::query("SELECT * FROM ".DB::table('map_lng_lat')." ".$wheresql);
            while($value = DB::fetch($query)) {
                if($map_type=='google'){
                    $map_points_str="['SN:".$value['sn_mac']."',  ".$value['latitude'].",".$value['longitude'].", 4],".$map_points_str;
                }else{
                    $map_points_str='{title:"SN:'.$value['sn_mac'].'",content:"'.$value['address'].'",point:"'.$value['longitude'].'|'.$value['latitude'].'",isOpen:0,icon:{w:21,h:21,l:0,t:0,x:6,lb:5}},'.$map_points_str;
                }
                //仅取一个搜索平板的坐标
                $map_center_lat = $value['latitude'];
                $map_center_lng = $value['longitude'];
            }
            $map_points_str = rtrim($map_points_str,',');
            $map_level = 15;
        }  else {
            $map_center_lat = '23.077069';
            $map_center_lng = '-25.33713';
            $map_level = 3;
        }
        include template('admin/map/map_'.$map_type);
		break;
        
    default:
		include template('admin/map/index');
        break;
}
?>