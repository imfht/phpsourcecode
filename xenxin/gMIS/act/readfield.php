<?php

require("../comm/header.inc.php");

# read table config, refer to /jdo.php
require("../comm/tblconf.php");

$navi = new PageNavi();
$orderfield = $navi->getOrder();
if($orderfield == '' && $hasid){
    $orderfield = $gtbl->getOrderBy();
    $navi->set('isasc', $orderfield=='id'?1:0);
}
$gtbl->set("pagesize", 0); # all records
$gtbl->set("pagenum", $navi->get('pnpn'));
$gtbl->set("orderby",$orderfield." ".($navi->getAsc()==0?"asc":"desc"));
$fieldlist = $_REQUEST['fieldlist'];
$hm = $gtbl->getBy("$fieldlist", $navi->getCondition($gtbl, $user));

$tblwidth = "800px";
$intbl = 0;
if($_REQUEST['mode'] == 'intbl'){
    $tblwidth = "500px";
    $intbl = 1;
}

# view mode
$out .= "<table align=\"center\" style=\"background:#fff\" width=\""
        .$tblwidth."\" cellspacing=\"0\" cellpadding=\"0\" class=\"printtbl\">";
if($hm[0]){
    $hm = $hm[1];
}else{
    $out .= "No Record.";
    $hm = array();
}
#print_r($hm);
if(count($hm) < 2){
    $out .= "<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
            .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">";

    foreach($hm as $k=>$hmorig){

        for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
            $field = $gtbl->getField($hmi);
            $fieldinputtype = $gtbl->getInputType($field);
            if($field == null || $field == ''
                    || $field == 'id' || !isset($hmorig[$field])){

                continue;
            } 

            if($fieldinputtype == 'select'){
                $out .= "<td width=\"10%\">".$gtbl->getCHN($field).":&nbsp;</td><td width=\"35%\"> "
                        .$gtbl->getSelectOption($field, $hmorig[$field],'',1)."</td>";

            }else if($fieldinputtype == 'file'){
                   $out .= "<td class=\"downline\" valign=\"middle\" > <a href=\"javascript:window.open('"
                           .$hmorig[$field]."');\" title=\"".$lang->get('notice_download_image')." ".$hmorig[$field]."\">";
                   $tmparr = explode(".", $hmorig[$field]); $fileext = $tmparr[count($tmparr)-1];
                   if(in_array($fileext, array('gif','jpg','jpeg','png','bmp'))){
                       $out .= " <img src=\"".$hmorig[$field]."\" style=\"width:100%\" /> ";

                   }else{
                        $out .= "".$hmorig[$field]."";
                   }
                   $out .= "</a></td>";

            }else if($gtbl->getExtraInput($field) != ''){
                if(true){
                    $out .= "</tr><tr><td>".$gtbl->getCHN($field).":</td><td colspan=\"".($form_cols-1)."\"><span id=\"span_"
                            .$act."_".$field."_val_add\"><input id=\"".$field."\" name=\"".$field."\" class=\"search\" value=\""
                            .$hmorig[$field]."\" /></span><br/> <span id=\"span_".$act."_".$field
                            ."\"><a href=\"javascript:void(0);\" onclick=\"javascript:doActionEx('".$gtbl->getExtraInput($field)
                            ."&act=".$act."&otbl=".$tbl."&field=".$field."&oldv=".$hmorig[$field]."&oid=".$id."','extrainput_"
                            .$act."_".$field."_inside');document.getElementById('extrainput_".$act."_".$field
                            ."').style.display='block';\">Disp</a></span> <div id=\"extrainput_".$act."_"
                            .$field."\" class=\"extrainput\"> ";
                    $out .= "<table width=\"100%\"><tr><td width=\"100%\" style=\"text-align:right\"> <b> "
                            ."<a href=\"javascript:void(0);\" onclick=\"javascript:if('"
                            .$id."' != ''){ var linkobj=document.getElementById('"
                            .$field."'); if(linkobj != null){ document.getElementById('"
                            .$field."').value=document.getElementById('linktblframe').contentWindow.sendLinkInfo('','r','"
                            .$field."');} } document.getElementById('extrainput_".$act."_"
                            .$field."').style.display='none';\">X</a> </b> &nbsp; </td></tr><tr><td> <div id=\"extrainput_".$act."_"
                            .$field."_inside\"></div></td></tr></table>";
                    $out .= "</div>  </td>  </tr><tr>";

                }

            }else{
                if($gtbl->getSingleRow($field) == '1'){
                    $out .= "</tr><tr><td>".$gtbl->getCHN($field).":&nbsp;</td><td colspan=\"".($form_cols-1)."\"> "
                            .$hmorig[$field]." </td> </tr><tr>";
                }else{
                    $out .= "<td width=\"10%\" BORDERCOLOR=\"737995\" >".$gtbl->getCHN($field).":&nbsp;</td><td width=\"35%\"> "
                            .$hmorig[$field]." </td>";
                }
            }
            if(++$i % 2 == 0){ 
                $out .= "</tr>";
                $out .= "<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
                        .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">";
            }
        }
    }
}
else{
#print_r($hm);

    $out .= "<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
            .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\"><td width=\"5%\">序号</td>";
    for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
        $field = $gtbl->getField($hmi);
        $fieldinputtype = $gtbl->getInputType($field);
        if($field == null || $field == ''
                || $field == 'id' || !isset($hm[0][$field])){

            continue;
        }
        $out .= "<td>".$gtbl->getCHN($field)."</td>";
    }
    $out .= "</tr>";
    foreach($hm as $k=>$hmorig){

        $out .= "<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
                .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\"><td>".++$i."</td>";
        for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
            $field = $gtbl->getField($hmi);
            $fieldinputtype = $gtbl->getInputType($field);
            if($field == null || $field == ''
                  || $field == 'id' || !isset($hmorig[$field])){ # 

                continue;
            } 

            if($fieldinputtype == 'select'){
                $out .= "<td width=\"35%\"> ".$gtbl->getSelectOption($field, $hmorig[$field],'',1)."</td>";
                
            }else if($fieldinputtype == 'file'){
                $out .= "<td class=\"downline\" valign=\"middle\" > <a href=\"javascript:window.open('"
                        .$hmorig[$field]."');\" title=\"".$lang->get('notice_download_image')." ".$hmorig[$field]."\">";
                $tmparr = explode(".", $hmorig[$field]); $fileext = $tmparr[count($tmparr)-1];
                if(in_array($fileext, array('gif','jpg','jpeg','png','bmp'))){
                    $out .= " <img src=\"".$hmorig[$field]."\" style=\"width:100%\" /> ";

                }else{
                    $out .= "".$hmorig[$field]."";
                }
                $out .= "</a></td>";

            }else if($gtbl->getExtraInput($field) != ''){

                    $out .= "<td><span id=\"span_".$act."_".$field."_val_add\"><input id=\"".$field."\" name=\""
                            .$field."\" class=\"search\" value=\"".$hmorig[$field]."\" /></span><br/> <span id=\"span_".$act."_"
                            .$field."\"><a href=\"javascript:void(0);\" onclick=\"javascript:doActionEx('"
                            .$gtbl->getExtraInput($field)."&act=".$act."&otbl=".$tbl."&field=".$field."&oldv=".$hmorig[$field]
                            ."&oid=".$id."','extrainput_".$act."_".$field."_inside');document.getElementById('extrainput_".$act."_"
                            .$field."').style.display='block';\">Disp</a></span> <div id=\"extrainput_".$act."_".$field
                            ."\" class=\"extrainput\"> ";
                    $out .= "<table width=\"100%\"><tr><td width=\"100%\" style=\"text-align:right\"> <b> "
                            ."<a href=\"javascript:void(0);\" onclick=\"javascript:if('".$id
                            ."' != ''){ var linkobj=document.getElementById('".$field
                            ."'); if(linkobj != null){ document.getElementById('".$field
                            ."').value=document.getElementById('linktblframe').contentWindow.sendLinkInfo('','r','"
                            .$field."');} } document.getElementById('extrainput_".$act."_".$field
                            ."').style.display='none';\">X</a> </b> &nbsp; </td></tr><tr><td> <div id=\"extrainput_".$act."_"
                            .$field."_inside\"></div></td></tr></table>";
                    $out .= "</div>  </td>";

            }else{
                    $out .= "<td> ".$hmorig[$field]." </td>";
            }
        }
        $out .= "</tr>";
    }

}
$out .= "</table>";

# list mode ?

require("../comm/footer.inc.php");

print $out;

?>
