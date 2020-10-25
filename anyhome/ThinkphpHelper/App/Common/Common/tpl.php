<?php
function tpTbInfo($tb = ''){
	if (!$tb) $tb = CONTROLLER_NAME;
	$tb = F($tb);
	if (!$tb[MODULE_NAME]) return;

	if ($tb[MODULE_NAME][ACTION_NAME]) 
		$acinfo = $tb[MODULE_NAME][ACTION_NAME];
	else
		$acinfo = $tb[MODULE_NAME]['common'];
	return $acinfo;
}


function tpLabel($value='')
{
	# code...
}


function tpTable(){

}

function tpTableToolsBar(){

}
function tpTableTitle($tb = '')
{
	if (!$tb) $tb = CONTROLLER_NAME;
	$tb = F($tb);
	return $tb['mdName'];
}
function tpTablehd($tb = ''){
	$acinfo = tpTbInfo($tb);
	if (!$acinfo) return;
	$bsTheme = C('BS_THEME');

	foreach ($acinfo['fields'] as $k) {
		if ($k['isshow'] == 0) continue;
		$thStr .= "<th>".$k['label']."</th>";
	}
	return $thStr;
}
function tpTableft(){
	
}

function tpTablepg(){
	
}

function tpTablelist($volist = array(),$tb = ''){
	if (!$volist) return;
	$acinfo = tpTbInfo($tb);
	if (!$acinfo) return;

	foreach ($volist as $k) {
		$str .='<tr data-pk='.$k['id'].'>';
		foreach ($acinfo['fields'] as $key) {
			if ($key['isshow'] == 0) continue;
			$str .='<td>'.tpFieldVal($k[$key['fname']],$key).'</td>';
		}
		$str .='</tr>';
	}
	return $str;
}

function tpFieldVal($fvalue ='',$finfo = '')
{
	if ($finfo['auto']) {
		if (function_exists($finfo['auto'])) {
			$fun = $finfo['auto'] ;
			return $fun($fvalue);
		}
	}
	if ($finfo['type'] == 'select') {
		$list_str = str_replace("\n", "-", $finfo['list']);
		$list_str = str_replace("：", ":", $list_str);
		$list_arr = explode('-',$list_str);
		$list_data = array();
		foreach ($list_arr as $k) {
			$tmp_arr = explode(':',$k);
			$v =  array();
			$v['value'] = $tmp_arr[0];
			$v['label'] = $tmp_arr[1];
			$list_data[] = $v;
		}
		foreach ($list_data as $k) {
			if ($k['value'] == $fvalue) {
				return $k['label'];
			}
		}
	}elseif ($finfo['type'] == 'datetime' && !$fvalue) {
		return toDatetime(time());
	}


	foreach ($list_data as $k) {
		if ($k['value'] == $val) $selected = "selected";
		$opt .= "<option ".$selected." value=".$k['value']." >".$k['label']." </option>";
	}


	return $fvalue;
}

function tpIpt($fname = '',$finfo = '',$val){
	if (!$fname) return;
	if (!$finfo) {
		$acinfo = tpTbInfo();
		if (!$acinfo) return;
	}

	if (!$finfo) {
		foreach ($acinfo['fields'] as $k){
			if ($k['fname'] == $fname) {
				$finfo = $k;
				break;
			}
		}
	}
	if ($finfo['valid'] == 'required')  $valid = "required";
	$iptCols = 10;
	$ainfo = tpTbInfo();
	if ($ainfo['globalIptCols']) $iptCols = $ainfo['globalIptCols'];
	if ($finfo['iptCols'])  $iptCols = $finfo['iptCols'];

	if ($finfo['type'] == 'hidden') {
		$str = "<input value=\"".tpFieldVal($val,$finfo)."\" name=\"".$finfo['fname']."\" type=\"hidden\">";
	}elseif($finfo['type'] == 'umeditor'){
		$str = "<div class=\"form-group\">
			<label class=\"control-label col-lg-2\">".$finfo['label']."</label>
			<div class=\"col-lg-".$iptCols." \">
				<script name=\"".$finfo['fname']."\" type=\"text/plain\" id=\"umeditor\" style=\"width:100%;height:240px;\">".$val."</script>
			</div>
		</div>";
	}elseif($finfo['type'] == 'datetime' || $finfo['type'] == 'date'){
		if ($finfo['type'] == 'datetime') {
			$date_str .= "datetime data-date-format=\"yyyy-m-d HH:ii p\" ";
		}elseif ($finfo['type'] == 'date') {
			$date_str .= "date data-date-format=\"yyyy-m-d\" ";
		}

		$str = "<div class=\"form-group\">
			<label class=\"control-label col-lg-2\">".$finfo['label']."</label>
			<div  class=\"col-lg-".$iptCols." \">
				<input ".$date_str." name=\"".$finfo['fname']."\" class=\"form-control ".$valid." \" value=\"".tpFieldVal($val,$finfo)."\" placeholder=\"".$finfo['tips']."\" type=\"text\" />
			</div>
		</div>";
	}elseif($finfo['type'] == 'upimg'){
		if (!$val) $val = "Public/up.png";
		$str = "<div img-up class=\"form-group\">
			<input type=\"hidden\" name=\"".$finfo['fname']."\">
			<label class=\"control-label col-lg-2\">".$finfo['label']."</label>
			<div class=\"col-lg-".$iptCols." \">
				<div class=\"thumbnail\">
					<img width=\"100%\" src=\"".$val."\" >
					<a href=\"#\" class=\"btn btn-primary btn-sm btn-file btn-block\" ><i class=\"icon-cloud-upload\"></i>修改</a>
				</div>
			</div>
		</div>";
	}else{
		$str ="<div class=\"form-group\">
			<label class=\"control-label col-lg-2\">".$finfo['label']."</label>
			<div class=\"col-lg-".$iptCols." \">
				<input name=\"".$finfo['fname']."\" class=\"form-control ".$valid." \" value=\"".tpFieldVal($val,$finfo)."\" placeholder=\"".$finfo['tips']."\" type=\"".$finfo['type']."\" />
			</div>
		</div>";
	}

	
	return $str;
	
}
function tpOptionYear($syear = '',$eyear = ''){
	if (!$syear) $syear =  date('Y')-1;
	if (!$eyear) $eyear =  date('Y')+2;
	$retStr = '';
	for ($i=$syear; $i < $eyear; $i++) { 
		$retStr .="<option value=\"".$i."\">".$i."年</option>";
	}
	return $retStr;
}

function tpOptionMonth(){
	$retStr = '';
	for ($i=1; $i < 13; $i++) { 
		$retStr .="<option value=\"".$i."\">".$i."月</option>";
	}
	return $retStr;
}

function tpSelect($fname = '',$finfo = '',$val){
	if (!$fname) return;
	if (!$finfo) {
		$acinfo = tpTbInfo();
		if (!$acinfo) return;
	}

	if (!$finfo) {
		foreach ($acinfo['fields'] as $k){
			if ($k['fname'] == $fname) {
				$finfo = $k;
				break;
			}
		}
	}
	$list_str = str_replace("\n", "@", $finfo['list']);
	$list_str = str_replace("：", ":", $list_str);
	$list_arr = explode('@',$list_str);
	$list_data = array();
	foreach ($list_arr as $k) {
		$tmp_arr = explode(':',$k);
		$v =  array();
		$v['value'] = $tmp_arr[0];
		$v['label'] = $tmp_arr[1];
		$list_data[] = $v;
	}

	$iptCols = 10;
	$ainfo = tpTbInfo();
	if ($ainfo['globalIptCols']) $iptCols = $ainfo['globalIptCols'];
	if ($finfo['iptCols'])  $iptCols = $finfo['iptCols'];


	foreach ($list_data as $k) {
		if ($k['value'] == $val) $selected = "selected";
		$opt .= "<option ".$selected." value=".$k['value']." >".$k['label']." </option>";
	}
	
	$str ="<div class=\"form-group\">
				<label class=\"control-label col-lg-2\">".$finfo['label']."</label>
				<div class=\"col-lg-".$iptCols." \">
					<select name=\"".$finfo['fname']."\" class=\"form-control\" >".$opt."
					</select>
				</div>
			</div>";
	return $str;
}

function tpCheckbox(){
	
}

function tpForm($vo){
	$acinfo = tpTbInfo();
	if (!$acinfo) return;

	foreach ($acinfo['fields'] as $k) {
		if ($k['isshow'] == 0) continue;
		if ($k['type'] == 'select') {
			$str .= tpSelect($k['fname'],$k,$vo[$k['fname']]);
		}else{
			$str .= tpIpt($k['fname'],$k,$vo[$k['fname']]);
		}
	}
	return $str;
}



function toDatetime( $time, $format = 'Y-m-d H:i:s' ) {
  if ( empty ( $time ) ) {
    return "";
  }
  if ( is_numeric( $time ) ) {
    return date( $format, $time );
  }
  $format = str_replace( '#', ':', $format );
  return date( $format, strtotime( $time ) );
}

function toDate( $time, $format = 'Y-m-d' ) {
  if ( empty ( $time ) ) {
    return $time;
  }
  $format = str_replace( '#', ':', $format );
  return date( $format, $time );
}

?>