<?php
# part of jdo.php, view details of a record
# added by wadelau on Tue Apr 10 19:52:51 CST 2012

$colsPerRow = 3;
if($_REQUEST['pnsktuanid'] != '' && $_REQUEST['otbl'] != ''){
    $colsPerRow = 2;
}

$out .= "<fieldset style=\"border-color:#5f8ac5;border: 1px solid #5f8ac5\">"
        ."<legend><h4>".$lang->get("func_view_detail")."</h4></legend><table align=\"center\" width=\"98%\" "
	." cellspacing=\"0\" cellpadding=\"6px\" border=\"0px\">";
$out .= "<tr><td width=\"10%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            <td width=\"10%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            <td width=\"10%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            </tr>";
$hmorig = array();

if($hasid){
    $gtbl->setId($id);
    $hmorig = $gtbl->getBy("*", null);
    $gtbl->setId('');
}else{
    $fieldargv = array();
    for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
        $field = $gtbl->getField($hmi);
        if($field == null || $field == ''
                || $field == $gtbl->getMyId()){
            continue;
        }
        if(array_key_exists($field, $_REQUEST)){
            $gtbl->set($field, $_REQUEST[$field]);
            $fieldargv[] = $field."=?";
        }
    }
    $hmorig = $gtbl->getBy("*", implode(" and ", $fieldargv));
}
foreach($_REQUEST as $k=>$v){
	if(startsWith($k,"pnsk")){
		$hmorig[substr($k,4)] = $v;
	}
	else if(startsWith($k, 'parent')){
		$k2 = $v;
		$hmorig[$k2] = $_REQUEST[$k2];
	}
}
if($hmorig[0]){
    $hmorig = $hmorig[1][0];
}

$closedtr = 1; $hasEndLine = 0;
$columni = 0;
for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
    $field = $gtbl->getField($hmi);
    $fieldinputtype = $gtbl->getInputType($field);
    if($field == null || $field == ''
            ){
        continue;
    }
    if($closedtr == 1){
        $out .= "<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
                .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">";
        $closedtr = 0;
    }

    #if($field == 'password'){
	if(inString('password', $field) || inString('pwd', $field)){
        $hmorig[$field] = '';
    }

    if(!$user->canRead($field,'','', $_REQUEST[$gtbl->getMyId()], $id)){
        $out .= "<--NOREAD-->";

    }else if($fieldinputtype == 'select'){
		$out .= "<td style=\"vertical-align:top\"><b>".$gtbl->getCHN($field)."</b>:&nbsp;</td>"
		        ."<td style=\"vertical-align:top\"> "
		        .$gtbl->getSelectOption($field, $hmorig[$field],'', $gtbl->getSelectMultiple($field))."</td>";
        
    }else if($fieldinputtype == 'file'){
		$origValue = $hmorig[$field];
        if($origValue != '' && $srcprefix != '' && !startsWith($origValue, 'http')){
            $hmorig[$field] = $srcprefix.'/'.$hmorig[$field];
        }
        $isimg = isImg($hmorig[$field], $field);
		if(strpos($hmorig[$field], "$shortDirName/") !== false){
		    $hmorig[$field] = str_replace("$shortDirName/", "", $hmorig[$field]);
		}
		if($gtbl->getSingleRow($field)){ $out .= "</tr><tr>"; }
        if($isimg){
            $out .= "<td style=\"vertical-align:top\">".$gtbl->getCHN($field).":&nbsp;</td>"
                    ."<td class=\"tdiviewfixedwidth\" style=\"vertical-align:top\"> <a href=\""
                    .$hmorig[$field]."\" target=\"_blank\" title=\"".$lang->get("func_open_large")."\"><img src=\"".$hmorig[$field]
                    ."\" alt=\"-x-\" style=\"width:118px\"/></a><br/>".($srcprefix==''?$rtvdir."/":'')
                    .$hmorig[$field]." </td>";
        }
		else{
            $out .= "<td >".$gtbl->getCHN($field).":&nbsp;</td><td class=\"tdiviewfixedwidth\"> <a href=\""
                    .$hmorig[$field]."\" title=\"".$hmorig[$field]."\" target=\"_blank\">".$hmorig[$field]."</a> </td>";
        }
    }
	else if($gtbl->getExtraInput($field,$hmorig) != ''){
        $out .= "</tr><tr><td>".$gtbl->getCHN($field).":</td><td colspan=\"".$form_cols."\"><span id=\"span_".$act."_"
                .$field."_val_add\"><input id=\"".$field."\" name=\"".$field."\" class=\"search\" value=\""
                .$hmorig[$field]."\" /></span> <span id=\"span_".$act."_".$field
                ."\"><a href=\"javascript:void(0);\" onclick=\"javascript:doActionEx('"
                .$gtbl->getExtraInput($field,$hmorig)."&act="
                .$act."&otbl=".$tbl."&field=".$field."&oldv=".$hmorig[$field]."&oid=".$id."&isheader=0&sid=$sid&parentcode=".$hmorig[$field]
                ."','extrainput_".$act."_"
                .$field."_inside');document.getElementById('extrainput_".$act."_".$field
                ."').style.display='block';\">Disp</a></span> <div id=\"extrainput_".$act."_".$field."\" class=\"extrainput\"> ";
		$out .= "<table width=\"100%\"><tr><td width=\"100%\" style=\"text-align:right\"> <b> <a href=\"javascript:void(0);\""
		        ." onclick=\"javascript:if('".$id."' != ''){ var linkobj=document.getElementById('".$field
		        ."'); if(linkobj != null && '".$id."'!=''){ document.getElementById('".$field
		        ."').value=document.getElementById('linktblframe').contentWindow.sendLinkInfo('','r','".$field
		        ."');} } document.getElementById('extrainput_".$act."_".$field
		        ."').style.display='none';\">X</a> </b> &nbsp; </td></tr><tr><td> <div id=\"extrainput_".$act."_".$field
		        ."_inside\"></div></td></tr></table> </div>";
        if($field != 'operatelog'){
            $out .= " <script type=\"text/javascript\"> parent.doActionEx('".$gtbl->getExtraInput($field,$hmorig)
                ."&act=".$act."&otbl=".$tbl."&field=".$field."&oldv=".$hmorig[$field]."&oid=".$id."&isheader=0&sid=$sid&parentcode="
                .$hmorig[$field]."','extrainput_".$act."_"
                .$field."_inside'); document.getElementById('extrainput_".$act."_".$field."').style.display='block';</script>";
        }
        $out .= "  <br/>".$gtbl->getMemo($field)." </td></tr><tr>";

    }
	else if($fieldinputtype == 'textarea'){

        $hmorig[$field] = str_replace("<br/>", "\n", $hmorig[$field]);

        if($gtbl->getSingleRow($field) == '1'){
			$tmpmemo = $gtbl->getMemo($field);
            if($tmpmemo == ''){
                # memo desc
            }
            $out .= "</tr>\n<tr><td style=\"vertical-align:top\">".$gtbl->getCHN($field).":</td><td colspan=\"".($form_cols)."\">
                <div id='".$field."_myeditordiv' style='width:680px;height:450px;display:none'></div>
                <div id='".$field."_mytextdiv' style='width:680px;height:450px;display:block'>
                <textarea id=\"".$field."\" name=\"".$field."\" rows=\"20\" cols=\"96\" class=\"search\" "
                        ."onclick=\"javascript:openEditor('".$rtvdir."/extra/htmleditor.php?field=".$field."', '"
                        .$field."'); parent.switchArea('".$field."_myeditordiv','on'); parent.switchArea('".$field
                        ."_mytextdiv','off');\">".$hmorig[$field]."</textarea> </div><br/> ".$tmpmemo."<br/> </td></tr><tr>";
            $out .= '';

        }
		else{
            $out .= "<td style=\"vertical-align:top\">".$gtbl->getCHN($field).":</td><td><textarea id=\"".$field."\" name=\""
                    .$field."\" rows=\"11\" cols=\"35\"  class=\"search\">".$hmorig[$field]."</textarea> <br/> "
                    .$gtbl->getMemo($field)." </td>";
        }

    }
	else{

		$tmpval = $hmorig[$field];
		if(strpos($tmpval, '<') !== false){
			$tmpval = str_replace('<', '&lt;', $tmpval);
		}

        $fhref = $gtbl->getHref($field, $hmorig);
        if(count($fhref)>0){
			if(strpos($fhref[0],"javascript") !== false){
              $tmpval = "<a href=\"javascript:void(0);\" onclick=\"".$fhref[0]."\" title=\"".$fhref[1]."\" target=\""
                      .$fhref[2]."\">".$tmpval."</a>";
            }else{
              $tmpval = "<a href=\"".$fhref[0]."\" title=\"".$fhref[1]."\" target=\"".$fhref[2]."\">".$tmpval."</a>";
            }
        }

        if($gtbl->getSingleRow($field) == 1){
            $out .= "</tr><tr> <td style=\"vertical-align:top\"><b>".$gtbl->getCHN($field)."<b>:</td> ";
            $out .= "<td colspan=\"".($form_cols-1)."\" class=\"tdiviewfixedwidth\" style=\"vertical-align:top\"> "
                    .$tmpval."  </td></tr><tr>";
        }else{
            $out .= "<td style=\"vertical-align:top\"><b>".$gtbl->getCHN($field)."</b>:&nbsp;</td>"
                    ."<td class=\"tdiviewfixedwidth\" "
                    ."style=\"word-wrap:break-word;white-space:normal;word-break:break-all;width:22%;vertical-align:top;\"> "
                    .$tmpval." </td>";
        }
    }

    $columni++;
    if($columni % $colsPerRow == 0){
        $out .= "</tr>";
        $closedtr = 1;
    }

    if(++$rows % 6 == 0 && $closedtr == 1){
        $out .= "<tr><td style=\"border-top: 1px dotted #cccccc; vertical-align:middle;\" colspan=\""
                .$form_cols."\">  </td> </tr>";
        $hasEndLine = 1;
    }
    else{
        $hasEndLine = 0;
    }
}

# access ctrl
$hasDisableW = false;
if(true){
    $accMode = $gtbl->getMode();
    if($accMode == 'r'){
        $hasDisableW = true;
    }
    else if($accMode == 'o-w'){
        $hasDisableW = true;
        $recOwnerList = array('op', 'operator', 'ioperator', 'editor');
        foreach($recOwnerList as $ownk=>$ownv){
            $theOwner = $hmorig[$ownv];
            if($theOwner == $userid){
                $hasDisableW = false;
                break;
            }
        }
    }
    else{
        debug("unkown accmode:[$accMode].");
    }
}

if($hasEndLine == 0){
    $out .= "<tr ><td style=\"border-top: 1px dotted #cccccc; vertical-align:middle;\" colspan=\""
        .$form_cols."\">  </td></tr>";
}
$rtn2top = 'document.location.href=\'#contentarea_outer\';';
$out .= "<tr><td colspan=\"".$form_cols."\" align=\"center\" style=\"word-spacing:8px;\">
	<input type=\"button\" name=\"viewbtn\" id=\"viewbtn\" value=\"".$lang->get("func_edit")."\"
		onclick=\"javascript:doActionEx('"
		.$jdo."&act=modify','contentarea');$rtn2top\"".($hasDisableW ? ' disabled' : '')."/>
	<input type=\"button\" name=\"printbtn\" id=\"printbtn\" value=\"".$lang->get("func_print")."\"
		onclick=\"javascript:window.open('"
		.$jdo."&act=print&isoput=1&isheader=0','PrintWindow','scrollbars,toolbar,location=0');\"/>
	<input type=\"button\" name=\"deletebtn\" id=\"deletebtn\" value=\"".$lang->get("func_delete")."\"
		onclick=\"javascript:if(window.confirm('".$lang->get("notice_confirm")." ".$lang->get("func_delete")." Id:".$id
		." ?')){doAction('".$jdo."&act=list-dodelete');}\"".($hasDisableW ? ' disabled' : '')."/>
	<input type=\"button\" name=\"addbycopybtn\" id=\"addbycopybtn\" value=\"".$lang->get("func_copy")."\"
        onclick=\"javascript:doActionEx('".$jdo."&act=addbycopy','contentarea');$rtn2top\"/>";

# more act options
if(true){
    $actArr = $gtbl->getActOption($hmorig);
    $out .= "\n";
    foreach($actArr as $actk=>$actv){
        if(startsWith($actv[0], 'javascript:')){
            #
        }
        else{
            $actv[0] = "javascript:doActionEx('".$actv[0]."','contentarea');";
        }
        $out .= "<input type='button' name='actoption_$actk' value='".$actv[1]."'"
            ." onclick=\"".$actv[0].";$rtn2top\"/>\n";
    }
}

$out .=	"<input type=\"button\" name=\"cancelbtn\" value=\"".$lang->get("func_close")."\"
		onclick=\"javascript:parent.switchArea('contentarea_outer','off');\" />";
	
$out .="</td></tr>";

$out .= "</table> </fieldset>  <br/>";

?>