<?php
/*
 * Pivot table or OLAP in -gMIS
 * added by wadelau@ufqi.com
 * Tue, 22 Nov 2016 21:16:30 +0800
 * update with data resort by Xenxin, Thu, 12 Jan 2017 13:28:13 +0800
 */

$formid = "gtbl_pivot_form"; //- ?

$hiddenfields = "";

$tblspan = 3;
$colsPerRow = 2;
if($_REQUEST['otbl'] != ''){
    $colsPerRow = 2;
}

if($act == 'pivot-do'){
    
    # submit form
    $navi = new PageNavi();
    $condi = $navi->getCondition($gtbl, $user);
    $grpby = Wht::get($_REQUEST, 'groupby');
    $calby = Wht::get($_REQUEST, 'calculateby');
    $ordby = Wht::get($_REQUEST, 'orderby');
    $sql = "select 1";
    $grpArr = explode(',', $grpby);
    $calArr = array();
    $grpTagArr = array();
    foreach ($grpArr as $k=>$v){ # group fields
        if($v != ''){
            $arr = explode('::', $v);
            if($arr[0] != ''){
                $arr[1] = str_replace('addgroupby', '', $arr[1]);
                if($arr[1] == ''){
                    $sql .= ",".$arr[0]." ";
                }
                else{
                    if($arr[1] == 'ymd'){
                        $itag = $arr[0]."ymd";
                        $sql .= ", substr(".$arr[0].", 1, 10) as $itag ";
                        $grpTagArr[$arr[0]] = $itag;
                    }
					else if(startsWith($arr[1], 'seg')){
                        $itag = $arr[0].$arr[1];
                        $itag = str_replace('-', '', $itag);
                        $itag = str_replace(' ', '', $itag);
                        $tmpArr1 = str_replace("seg", '', $arr[1]);
                        $tmpPosArr = explode("-", $tmpArr1);
                        #debug($tmpPosArr);
                        $tmpPosArr[0] = intval(trim($tmpPosArr[0]));
                        $tmpPosArr[1] = intval(trim($tmpPosArr[1]));
                        if($tmpPosArr[0] < 1){
                            $tmpPosArr[0] = 1;
                        }
                        if($tmpPosArr[1] < $tmpPosArr[0]){
                            $tmpPosArr[1] = $tmpPosArr[0] * 2; # why double?
                        }
                        $sql .= ", substr(".$arr[0].",".$tmpPosArr[0].","
								.($tmpPosArr[1]-$tmpPosArr[0]).") as $itag ";
                        $grpTagArr[$arr[0]] = $itag;
                        #debug("arr-1:".$arr[1]." arr1:$tmpArr1 itag:$itag sql:$sql");
                    }
                    else{
                        debug("unsupported addgroup:".$arr[1]);
                    }
                }
            }
        }
    }
    $calArrDisp = array();
    foreach (explode(',', $calby) as $k=>$v){ # calculate fields
        if($v != ''){
            $arr = explode('::', $v);
            if($arr[0] != ''){
                $arr[1] = str_replace('addvalueby', '', $arr[1]);
                $func = $arr[1]."(".$arr[0].")";
                if($arr[1] == 'countdistinct'){
                    $func = "count(distinct ".$arr[0].")";
                }
                $sql .= ",".$func." as ".$arr[0].$arr[1]." ";
                $calArrDisp[$arr[0].$arr[1]] = 1;
                if(!isset($calArr[$arr[0]])){
                    $calArr[$arr[0]] = $arr[0].$arr[1];
                }
                if(true){
                    $calArr[$arr[0].$arr[1]] = $arr[0];
                }
            }
        }
    }
    $sql .= " from ".$gtbl->getTbl();
    $sql .= " where ".($condi=='' ? '1=1' : $condi);
    $sql .= " group by 1";
    $grpArrDisp = array();
    foreach ($grpArr as $k=>$v){ # group by fields
        if($v != ''){
            $arr = explode('::', $v);
            if($arr[0] != ''){
                if(isset($grpTagArr[$arr[0]])){
                    $arr[0] = $grpTagArr[$arr[0]];
                }
                $sql .= ",".$arr[0]." ";
                $grpArrDisp[$arr[0]] = 1;
            }
        }
    }
    $sql .= " order by 1";
    $ordArr = array();
    foreach (explode(',', $ordby) as $k=>$v){ # order by fields
        if($v != ''){
            $arr = explode('::', $v);
            if($arr[0] != ''){
                if(isset($calArr[$arr[0]])){
                    $arr[0] = $calArr[$arr[0]]; # use calcu result as order
                    $sql .= ",".$arr[0]." desc ";
                }
                else if(isset($grpTagArr[$arr[0]])){
                    $arr[0] = $grpTagArr[$arr[0]];
                    $sql .= ",".$arr[0]." ";
                }
                else{
                    $sql .= ",".$arr[0]." ";
                }
                $ordArr[$arr[0]] = 1;
            }
        }
    }
    $grpArrDispTmp = array(); # prioritize order fields 
    foreach ($ordArr as $ok=>$ov){
        if(isset($grpArrDisp[$ok])){
            $grpArrDispTmp[$ok] = 1;
        }
    }
    foreach ($grpArrDisp as $gk=>$gv){
        if(!isset($ordArr[$gk])){
            $grpArrDispTmp[$gk] = 1;
        }
    }
    $grpArrDisp = $grpArrDispTmp;
    #$out .= "sql:[$sql]";
    #debug(__FILE__.": sql:[$sql]");
    $hm = $gtbl->execBy($sql, null);
    if($hm[0]){
        $hm = $hm[1];
        # table headers
        $out .= "<b>".$lang->get("func_pivot_hint")."</b><br/>";
        $out .= "<div id=\"pivot_resultset_g\">"
                ."<table id=\"pivot_resultset_gtbl\" style=\"border:1px solid black; width:96%; margin-left:auto; margin-right:auto;\">"
                ."";
        $out .= "<tr><td colspan=\"3\"></td></tr>";
        $out .= "<tr><td colspan=\"30\" style=\"text-align:center\">...Graphic...</td></tr>";
        $out .= "<tr><td colspan=\"30\" style=\"text-align:center\">"
                ."</td></tr>";
        $out .= "</table></div>";
        $out .= "<br/><b></b>";
        $out .= "<table id=\"pivot_resultset\" style=\"border:1px solid black; width:96%; margin-left:auto; margin-right:auto;\""
                ." class=\"pivot_resultset_cls\" name=\"pivot_resultset\">";
        $out .= "<tr><td colspan=\"3\"></td></tr>";
        $out .= "<tr style=\"font-weight:bold;\"><td> &nbsp;No.</td>";
        $colsCount = 1;
        foreach ($grpArrDisp as $gk=>$gv){
            $out .= "<td>".$gtbl->getCHN($gk)."</td>";
            $colsCount++;
        }
        foreach ($hm[0] as $vk=>$vv){
            if($vk == '1'){ continue; }
            else if(isset($grpArrDisp[$vk])){ continue; }
            else{
                $out .= "<td>".$gtbl->getCHN($vk)."</td>";
                $colsCount++;
            }
        }
        $colsPerRow = $colsCount;
        $out .= "</tr><tr><td colspan='".$colsPerRow."'><hr/></td></tr>";
        # resort data
        $dispArr = array();
        $dispSort = array();
        foreach ($hm as $dk=>$dv){
            $uniqk = '';
            $gi = 0; $lastK = '';
            foreach ($grpArrDisp as $gk=>$gv){
                $uniqk .= $dv[$gk]."\t";
                #debug(__FILE__.": uniqk:[$uniqk] dk:[$dk] lastk:[$lastK]");
                foreach ($calArrDisp as $ck=>$cv){
                    $dispArr[$uniqk][$ck] += $dv[$ck];
                }
                if($gi == 0){
                    if(!isset($dispSort[$uniqk])){
                        $dispSort[$uniqk] = sprintf("%04d", $dk+1); # every 4-digital as a segment
                    }
                }
                else{
                    if(!isset($dispSort[$uniqk])){
                        $prtk = $dispSort[$lastK];
                        $dispSort[$uniqk] = $prtk.sprintf("%04d", $dk+1);
                    }
                }
                $lastK = $uniqk;
                $gi++;
            }
        }
        #print_r($dispArr);
        #debug(__FILE__.": dispSort:".$gtbl->toString($dispSort));
        # display
        $colsum = array(); $colsumuniq = array(); $colStat = array();
        $rowi = 0; $fullki = 0;
        asort($dispSort, SORT_STRING); # sort by string and keep hash index
        $grpArrLen = count($grpArrDisp);
        foreach ($dispSort as $dk=>$dv){ #
            $dv = $dispArr[$dk];
            $out .= "<tr><td> &nbsp;".(++$rowi)."</td>";
            $dkArr = explode("\t", $dk);
            array_pop($dkArr);
            foreach ($dkArr as $dkk=>$dkv){
                $out .= "<td>$dkv</td>";
                if(!isset($colsumuniq[$dkk][$dkv])){
                    $colsum[$dkk]++;
                    $colsumuniq[$dkk][$dkv] = 1;
                    $colStat[$dkk]['max'] = '-';
                    $colStat[$dkk]['min'] = '-';
                }
            }
            $isFullKey = true;
            $arrLenBala = $grpArrLen - count($dkArr);
            for($dki=0; $dki<$arrLenBala; $dki++){
                $out .= "<td style='background-color:silver;' id='allcol'>ALL</td>";
                $isFullKey = false;
            }
            foreach ($calArrDisp as $ck=>$cv){
                $tmpv = $dispArr[$dk][$ck];
                $out .= "<td>".sprintf("%.3f", $tmpv)."</td>";
                if($isFullKey){
                    $colsum[$ck] += $tmpv;
                    $hasMin = true;
                    if(!isset($colStat[$ck]['min'])){
                        $colStat[$ck]['min'] = $tmpv;
                        $hasMin = false;
                    }
                    if($tmpv > $colStat[$ck]['max']){ $colStat[$ck]['max'] = $tmpv; }
                    else if($hasMin && $tmpv < $colStat[$ck]['min']){ $colStat[$ck]['min'] = $tmpv;}
                }
            }
            if($isFullKey){ $fullki++; }
            $out .= "</tr>";
        }
        $out .= "<tr><td colspan='".$colsPerRow."'><hr/></td></tr>";
        $out .= "<tr style=\"font-weight:bold;\" id=\"totalrow\"><td>GrandTotal</td>";
        foreach ($colsum as $sk=>$sv){
            $out .= "<td>".sprintf("%.3f", $sv)."</td>";
        }
        $out .= "</tr>";
        $out .= "<tr style=\"font-weight:bold;\" id=\"avgrow\"><td>Average.of</td>";
        foreach ($colsum as $sk=>$sv){
            if(isset($calArrDisp[$sk])){
                $out .= "<td>".sprintf("%.3f", $sv/$fullki)."</td>";
            }
            else{
                $out .= "<td> - </td>";
            }
        }
        $out .= "</tr>";
        $out .= "<tr style=\"font-weight:bold;\" id=\"maxrow\"><td>Max.of</td>";
        foreach ($colStat as $sk=>$sv){
            if(isset($calArrDisp[$sk])){
                $out .= "<td>".sprintf("%.3f", $sv['max'])."</td>";
            }
            else{
                $out .= "<td> - </td>";
            }
        }
        $out .= "</tr>";
        $out .= "<tr style=\"font-weight:bold;\" id=\"minrow\"><td>Min.of</td>";
        foreach ($colStat as $sk=>$sv){
            if(isset($calArrDisp[$sk])){
                $out .= "<td>".sprintf("%.3f", $sv['min'])."</td>";
            }
            else{
                $out .= "<td> - </td>";
            }
        }
        $out .= "<tr><td colspan=\"3\"></td></tr>";
        $out .= "</table>";
        $out .= "<script type=\"text/javascript\" src=\"$rtvdir/comm/gMISPivotDraw.js\"></script>"
            ."<script type=\"text/javascript\">window.setTimeout(gMISPivotDraw('pivot_resultset', '"
                    .json_encode($calArrDisp)."', '".json_encode($grpArrDisp)."','"
                    .json_encode($colsum)."', '".json_encode($colStat)."', 'pivot_resultset_g'), 3*1000);</script>";
        $out .= "<style>
                .gmis_pivot_draw_tbl tr:hover{
	               background-color: #afc4e2; }
                .spanbar{
                    background-color:#0FF;
                    border-width:1px;
                    overflow:hidden;
                    display:inline-block;
                    border-color:#0f0;
                    border-style:solid;
                    align:justify; }
                .pivot_resultset_cls tr:hover{
	               background-color: #afc4e2; }
                </style>";
    }
    else{
        $out .= "No Data for query:[$sql]. 1612061412.";
    }
}
else{

# form 
# reset old?

$out .= "<fieldset style=\"border-color:#5f8ac5;border: 1px solid #5f8ac5;\"><legend><h4>".$lang->get("func_pivot_dataset")."("
        .number_format($_REQUEST['pntc']).")</h4></legend>"
       ."<form id=\"".$formid."\" name=\"".$formid."\" method=\"post\" action=\"".$jdo."&act=pivot-do\" "
	   .$gtbl->getJsActionTbl().">";
$out .= "<table style='border:0px; width:96%; margin-left:auto; margin-right:auto;'>";
        
$out .= "<tr><td colspan='$tblspan'>".$lang->get("func_pivot_ophint")."</td></tr>";
$out .= "<tr><td colspan='$tblspan' width='100%'>";

$hmorig = array();

if(true){
    foreach($_REQUEST as $k=>$v){
        if(startsWith($k,"pnsk")){
            $hmorig[substr($k,4)] = $v;
        }
        else if(startsWith($k, 'parent')){ # Attention! parentid
            $k2 = $v;
            $hmorig[$k2] = $_REQUEST[$k2];
        }
    }
    for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
        $field = $gtbl->getField($hmi);
        if($field == null | $field == ''
                || $field == 'id'){
                    continue;
        }
        $fielddf = $gtbl->getDefaultValue($field);
        if($fielddf != ''){
            $tmparr = explode(":", $fielddf);
            if($tmparr[0] == 'request'){ # see xml/hss_info_attachfiletbl.xml
                $hmorig[$field] = $_REQUEST[$tmparr[1]];
            }else{
                $hmorig[$field] = $tmparr[0]; # see xml/hss_tuanduitbl.xml
            }
        }
    }
}

if($hmorig[0]){
    $hmorig = $hmorig[1][0];
}

$closedtr = 1; $opentr = 0; # just open a tr, avoid blank line, Sun Jun 26 10:08:55 CST 2016
$columni = 0; $my_form_cols = 4;
$firstField = '';
$secondField = '';

for($hmi=$min_idx; $hmi<=$max_idx;$hmi++){
    $field = $gtbl->getField($hmi);
    $fieldinputtype = $gtbl->getInputType($field);

    $filedtmpv = $_REQUEST['pnsk_'.$field];
    if(isset($fieldtmpv)){
        $hmorig[$field] = $fieldtmpv;
    }

    if($field == null || $field == ''){ # || $field == 'id'
        continue;
    }
    if($fieldinputtype == 'hidden'){
        $hiddenfields .= "<input type=\"hidden\" name=\"".$field."\" id=\"".$field."\" value=\"".$hmorig[$field]."\"/>\n";
    }
    if($gtbl->filterHiddenField($field, $opfield,$timefield)){
        #continue; # should be displayed
    }
    #if($field == 'password'){
	if(inString('password', $field) || inString('pwd', $field)){
        $hmorig[$field] = '';
        continue;
    }
    else if($fieldinputtype == 'file'){
        continue;
    }
    
    # real input field
    if($firstField == '' && ($field != 'id' || $field != $gtbl->getMyId())){ $firstField = $field; }
    else if($secondField == '' && ($field != 'id' || $field != $gtbl->getMyId()) ){ $secondField = $field; }
    $chnName = $gtbl->getCHN($field);
    $out .= "<a href='javascript:void(0);' onmouseover=\"javascript:showPivotList($hmi, 1, '$field', '".$chnName."');\" "
            .">$hmi. ".$chnName."($field)"
            ."</a><span id='divPivotList_$hmi' style=\"display:none; position: relative; margin-left:-5px; "
            ." margin-top:-10px; z-index:97; background-color:silver;\" "
            ." ></span>&nbsp;&nbsp;&nbsp;&nbsp;"; # onmouseout=\"javascript:this.style.display='none';\"

}

$out .= "</td></tr>";

$firstFieldChn = $gtbl->getCHN($firstField);
$secondFieldChn = $gtbl->getCHN($secondField);
$out .= "<tr>"
        ."<td width='34%'><fieldset><legend title='".$lang->get("func_pivot_group_col")."'>".$lang->get("func_pivot_group_col")."</legend>"
        ."<span id='span_groupby'>"
        .$firstFieldChn."($firstField) addgroupby   <a href=\"javascript:void(0);\" onclick=\"javascript:doPivotSelect('$firstField', "
        ."'1', 'addgroupby', 0, '".$firstFieldChn."');\" title=\"Remove\"> X(Rm) </a>   <a href=\"javascript:void(0);\" onclick=\"javascript:doPivotSelect('"
        .$firstField."', '1', 'addorderby', 1, '".$firstFieldChn."');\" title=\"Order\"> ↿⇂(Od) </a><br>"
        ."</span><input type='hidden' name='groupby' id='groupby' value=',".$firstField."::addgroupby'/>"
        ."</fieldset></td>";
$out .= "<td width='33%'><fieldset><legend title='".$lang->get("func_pivot_value_col")."'>".$lang->get("func_pivot_value_col")."</legend>"
        ."<span id='span_calculateby'>"
        .$gtbl->getCHN($secondField)."($secondField) addvaluebycount   <a href=\"javascript:void(0);\" onclick=\"javascript:doPivotSelect('$secondField', "
        ."'1', 'addvaluebycount', 0, '".$gtbl->getCHN($secondField)."');\" title=\"".$lang->get("func_delete")."\"> X(Rm) </a>   <a href=\"javascript:void(0);\" onclick=\"javascript:doPivotSelect('"
        .$secondField."', '1', 'addorderby', 1, '".$secondFieldChn."');\" title=\"".$lang->get("func_orderby")."\"> ↿⇂(Od) </a><br>"
        ."</span><input type='hidden' name='calculateby' id='calculateby' value=',".$secondField."::addvaluebycount'/>"
        ."</fieldset></td>";
$out .= "<td><fieldset><legend title='".$lang->get("func_pivot_order_col")."'>".$lang->get("func_pivot_order_col")."</legend>"
        ."<span id='span_orderby'>"
        .$gtbl->getCHN($firstField)."($firstField) addorderby   <a href=\"javascript:void(0);\" onclick=\"javascript:doPivotSelect('$firstField', "
        ."'1', 'addorderby', 0, '".$gtbl->getCHN($firstField)."');\" title=\"Remove\"> X(Rm) </a>   <a href=\"javascript:void(0);\" onclick=\"javascript:doPivotSelect('"
        .$firstField."', '1', 'addorderby', 1, '".$firstFieldChn."');\" title=\"Order\"> ↿⇂(Od) </a><br>"
        ."</span><input type='hidden' name='orderby' id='orderby' value=',".$firstField."::addorderby'/></fieldset></td>"
        ."</tr>";
	
$out .= "<tr><td colspan='$tblspan'> <input type=\"submit\" name=\"addsub\" id=\"addsub\" "
        ."onclick=\"javascript:doActionEx(this.form.name,'pivotarea');\" /> \n"; 
        $out .= "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"".$id."\"/>\n ".$hiddenfields."\n";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"button\" name=\"cancelbtn\" value=\"".$lang->get("func_cancel")."\" "
                ."onclick=\"javascript:switchArea('contentarea_outer','off');\" /> </td></tr></table>";

$out .= "</form> <br/> <div id='pivotarea'>Data Processing....</div> </fieldset>"
        ."";

}

?>
