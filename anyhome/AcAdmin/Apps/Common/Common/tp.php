<?php

function tpTbInfo($tb = ''){
    if (!$tb) $tb = CONTROLLER_NAME;

    $tb = C('DB_PREFIX').parse_name($tb);
    $tb = F($tb);
    // $MODULE_NAME = MODULE_NAME;
    // if (!$tb[$MODULE_NAME]) $MODULE_NAME = 'Common';
    // if (!$tb[$MODULE_NAME]) return;
    $ACTION_NAME = ACTION_NAME;
    if (!$tb[$ACTION_NAME]) $ACTION_NAME = 'Common';
    if (!$tb[$ACTION_NAME]) return;
    $acinfo = $tb[$ACTION_NAME];
    return $acinfo;
}


function tpLabel($fname = '',$tb ='')
{
    if ($fname == 'mid') {
        return "提交用户";
    }elseif ($fname == 'area_id') {
        return "地区";
    }elseif ($fname == 'status') {
        return "审核状态";
    }
    $finfo = tpFinfo($tb,$fname);
    $l = $finfo['comment'] ;
    if ($l) {
        return $l ;
    }
    return $finfo['field'];
}

function tpFinfo($tb ='',$fname = '')
{
    if (!$fname) return;
    $acinfo = tpTbInfo($tb);
    if (!$acinfo) return;
    foreach ($acinfo as $k){
        if ($k['field'] == $fname) {
            $finfo = $k;
            break;
        }
    }
    return $finfo;
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

    // $thStr .= "<th><input type=\"checkbox\" class=\"checkAll\" /></th>";
    $thStr = '';
    foreach ($acinfo as $k) {
        if ($k['isshow'] == 0) continue;
        if ($k['field'] == 'addtime') continue;
        if ($k['field'] == 'status') continue;
        if ($k['field'] == 'id') continue;
        $name = $k['field'];
        // $thStr .= "<th w_index=".$k['field']." >".$name."</th>";
        $thStr .= "<th w_index=".$k['field'].">".tpLabel($name)."</th>";
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
        $str .='<tr data-status="'.$k['status'].'" data-pk='.$k['id'].'>';
        // $str .='<tr data-status="'.$k['status'].'" data-pk='.$k['id'].'>';
        // $str .='<td><input type="checkbox" value="'.$k['id'].'" /></td>';
        foreach ($acinfo['fields'] as $key) {
            if ($key['isshow'] == 0) continue;

            if ($key['field'] == 'area_id') {
                $str .='<td>'.$k['Area']['AreaName'].'</td>';
            }elseif ($key['field'] == 'mid') {
                $str .='<td>'.$k['Member']['account'].'</td>';
            }else{
                $str .='<td  >'.tpFieldVal($k[$key['field']],$key).'</td>';
            }
            // $str .='<td>'.tpFieldVal($k[$key['fname']],$key).'</td>';

        }
        $str .='</tr>';
    }
    return $str;
}

function tpAtuoVal($fvalue ='',$fname ='',$finfo = '')
{
    if (!$fname || !$fvalue) return;
    if (!$finfo) {
        $acinfo = tpTbInfo($tb);
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
    if ($finfo) {
        return tpFieldVal($fvalue,$finfo);
    }

    if ($finfo['name'] == 'show_time') {
        return toDatetime($fvalue);
    }

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
        return $k['value'];
    }

}

function tpFieldVal2($fvalue ='',$fname = '')
{
    $finfo = tpFinfo('',$fname);
    return tpFieldVal($fvalue,$finfo);
}

function tpFieldVal($fvalue ='',$finfo = '')
{

    if ($finfo['field'] == 'a_id') {
        $ActiveTeam = M('ActiveTeam');
        $map['id'] = $fvalue;
        return $ActiveTeam->where($map)->getField('team_name');
    }

    if ($finfo['auto']) {
        if (function_exists($finfo['auto'])) {
            $fun = $finfo['auto'] ;
            return $fun($fvalue);
        }
    }
    if (strlen($fvalue) == 10 && is_numeric($fvalue)) {
        return str_replace("00:00:00", "", toDatetime($fvalue));
    }

    if ($finfo['field'] == 'starttime' ||
        $finfo['field'] == 'endtime' ||
        $finfo['field'] == 'addtime' ||
        $finfo['field'] == 'startime' ||
        $finfo['field'] == 'stime' ||
        $finfo['field'] == 'etime' ||
        $finfo['field'] == 'addtime' ||
        $finfo['field'] == 'show_time' 
        ) {
        
        return str_replace("00:00:00", "", toDatetime($fvalue));
    }

    if ($finfo['field'] == 'status') {
        if ($fvalue == 0) {
            return '未提交';
        }elseif ($fvalue == 1) {
            return '待审核';
        }elseif ($fvalue == 2) {
            return '审核通过';
        }elseif ($fvalue == -1) {
            return '退回';
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


function tpSearchTable($table = '')
{
    // if (!$vo) return;

    $acinfo = tpTbInfo();

    $html = "<table class=\"table\">";
    $i = 0;
    foreach ($vo as $k => $v) {
        $finfo = tpFinfo('',$k);
        if ($finfo['isshow'] == 0) continue;
        if ($finfo['field'] == 'id') continue;
        if ($finfo['field'] == 'addtime') continue;
        $html.= "<tr>";
        $comment = $finfo['comment'];
        if (!$comment) $comment = $finfo['field'];
        if ($finfo['field'] == 'mid') {
            // $html.= "<td width=\"160\">".tpLabel('mid')."</td><td>".$vo['Member']['account']."</td>";
        }elseif($finfo['field'] == 'area_id') {
            $html.= "<td width=\"160\">".tpLabel('area_id')."</td><td>".$vo['Area']['areaname']."</td>";
        }elseif($finfo['field'] == 'status') {
            $html.= "<td width=\"160\">审核状态</td><td>".tpFieldVal($v,$finfo)."</td>";
        }else{
            $html.= "<td width=\"160\">".$comment."</td><td>".tpFieldVal($v,$finfo)."</td>";
        }
        $html.= "</tr>";
        $i ++;
    }
    $html .= "</table>";
    return $html;
}



function tpViewTable($vo ='',$table = '')
{
    // if (!$vo) return;

    $acinfo = tpTbInfo();

    $html = "<table class=\"table\">";
    $i = 0;
    foreach ($vo as $k => $v) {
        $finfo = tpFinfo('',$k);
        if ($finfo['isshow'] == 0) continue;
        if ($finfo['field'] == 'id') continue;
        if ($finfo['field'] == 'addtime') continue;
        $html.= "<tr>";
        $comment = $finfo['comment'];
        if (!$comment && $finfo['label']) $comment = $finfo['label'];
        if ($finfo['field'] == 'mid') {
            // $html.= "<td width=\"160\">".tpLabel('mid')."</td><td>".$vo['Member']['account']."</td>";
        }elseif($finfo['field'] == 'area_id') {
            $html.= "<td width=\"160\">".tpLabel('area_id')."</td><td>".$vo['Area']['areaname']."</td>";
        }elseif($finfo['field'] == 'status') {
            $html.= "<td width=\"160\">审核状态</td><td>".tpFieldVal($v,$finfo)."</td>";
        }else{
            $html.= "<td width=\"160\">".$comment."</td><td>".tpFieldVal($v,$finfo)."</td>";
        }
        $html.= "</tr>";
        $i ++;
    }
    $html .= "</table>";
    return $html;
}

function dicOpt($field ='')
{
    $dics = F('dic');
    $dic;
    foreach ($dics as $k) {
        if ($k['field'] == $field) {
            $dic = $k;
            break;
        }
    }
    // return $dic['field'];
    if (!$dic) return;
    return tpSelect($field,$dic);
}


function tpAddTable($vo = '',$tb = '')
{

    $acinfo = tpTbInfo();
    if (!$acinfo) return;

    $html = "<table class=\"table table-striped table-bordered \">";
    $i = 0;

    
    foreach ($acinfo as $k) {
        if ($k['isshow'] == 0) continue;
        if ($k['field'] == 'addtime' ||$k['field'] == 'ctime' ||$k['field'] == 'id' || $k['field'] == 'status' || $k['field'] == 'mid'|| $k['field'] == 'area_id') continue;



        $date_str = '';$input = '';
        if ($k['type'] == 'datetime') {
            $date_str .= "datetime data-date-format=\"yyyy-m-d HH:ii p\" ";
        }elseif ($k['type'] == 'date') {
            $date_str .= "date data-date-format=\"yyyy-m-d\" ";
        }

        
        if ($k['ipttype'] == 'select') {
            $input = tpSelect($k['field'],$k);
        }elseif (dicOpt($k['field'])) {
            $input = dicOpt($k['field']);
        }else{
            $input = "<input ".$date_str." name=\"".$k['field']."\" class=\"form-control ".$valid." \" value=\"".tpFieldVal($vo[$k['field']],$k)."\" placeholder=\"".$k['tips']."\" type=\"text\" />";
        }
        $comment = $k['comment'];
        if (!$k['comment'] && $k['label']) {
            $comment = $k['label'];
        }
        $html.= "<tr>";
        $html.= "<td width=\"160\">".$comment."</td><td>
                ".$input."
                </td>";
        $html.= "</tr>";
        $i ++;
    }
    $html .= "</table>";
    return $html;
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


    foreach ($list_data as $k) {
        if ($k['value'] == $val) $selected = "selected";
        $opt .= "<option ".$selected." value=".$k['value']." >".$k['label']." </option>";
    }

    $str2 ="<div class=\"form-group\">
                <label class=\"control-label col-lg-2\">".$finfo['label']."</label>
                <div class=\"col-lg-10\">
                    <select name=\"".$finfo['field']."\" class=\"form-control\" >".$opt."
                    </select>
                </div>
            </div>";
    
    $str ="<select name=\"".$finfo['field']."\" class=\"form-control\" >".$opt."
                    </select>";
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


?>