<?php

$formid = "gtbl_add_form";
$srcprefix = $gtbl->getSrcPrefix();
$hiddenfields = "";

$colsPerRow = 3;
if($_REQUEST['otbl'] != ''){
    $colsPerRow = 2;
}

$out .= "<fieldset style=\"border-color:#5f8ac5;border: 1px solid #5f8ac5;\"><legend><h4>".$lang->get("func_create")."/"
		.$lang->get("func_edit")."</h4>"
        ."</legend><form id=\"".$formid."\" name=\"".$formid."\" method=\"post\" action=\""
        .$jdo."&act=list-addform\" ".$gtbl->getJsActionTbl()." data-formid=\"$formid\" "
		." title=\"$formid\"><table align=\"center\" width=\"98%\" "
        ."cellspacing=\"0\" cellpadding=\"6px\" border=\"0px\">";
$out .= "<tr><td width=\"11%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            <!-- <td width=\"2%\">&nbsp;</td> -->
            <td width=\"11%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            <td width=\"11%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            </tr>";
			
$hmorig = array();
$isAddByCopy = false;
if(startsWith($act, "modify")){
    if($hasid){
        $gtbl->setId($id);
        $hmorig = $gtbl->getBy("*", null);
        $gtbl->setId('');
    }
	else{
        $fieldargv = array(); # ""; # for php 7.3+
        for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
            $field = $gtbl->getField($hmi);
            if($field == null | $field == '' 
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
    # very first row
    if($hmorig[0]){
        $hmorig = $hmorig[1][0]; 
    }
}
else{
    # read copy obj
    if($act == 'addbycopy'){
        if($hasid){
            $gtbl->setId($id);
            $hmorig = $gtbl->getBy("*", null);
            $gtbl->setId('');
        }
        else{
            $fieldargv = array(); # ""; # for php 7.3+
            for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
                $field = $gtbl->getField($hmi);
                if($field == null | $field == '' 
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
        if($act == 'addbycopy' && $id != ''){
            $id = '';
            $gtbl->setId($id);
            $isAddByCopy = true;
        }
        # very first row
        if($hmorig[0]){
            $hmorig = $hmorig[1][0]; 
        }

    }

    # reset preset info
    for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
        $field = $gtbl->getField($hmi);
        if($field == null | $field == '' 
                || $field == 'id'){
            continue;
        }
        $fielddf = $gtbl->getDefaultValue($field);
        if($fielddf != ''){
            $tmparr = explode(":", $fielddf);
            if($tmparr[0] == 'request'){ # see xml/info_attachfiletbl.xml
                $hmorig[$field] = $_REQUEST[$tmparr[1]];
            }
            else if($tmparr[0] == 'system'){
                $systemInfoArr = array('USERID'=>$user->getId(), 'GROUPID'=>$user->getGroup());
                $hmorig[$field] = $systemInfoArr[$tmparr[1]];
            }
            else{
                $hmorig[$field] = $tmparr[0]; # see xml/tuanduitbl.xml
            }
        }
    }
    # highest level
    foreach($_REQUEST as $k=>$v){
        if(startsWith($k,"pnsk")){
            $hmorig[substr($k,4)] = $v;
        }
		else if(startsWith($k, 'parent')){ # Attention! parentid
			$k2 = $v;
			$hmorig[$k2] = $_REQUEST[$k2];	
		}
    }
    #print_r($hmorig);
}

# check writeable
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
                #print "userid:$userid theowner:$theOwner .";
                break;
            }
        }
    }
    else{
        #debug("unkown accmode:[$accMode].");
    }
}
if(startsWith($act, 'modify') && $hasDisableW && !$isAddByCopy){
    $out .= $lang->get("notice_access_deny")." 201811111014.";
    $out .= "<br/><br/><a href=\"javascript:switchArea('contentarea_outer','off');\">".$lang->get("func_close")."</a>";

}
else{

$closedtr = 1; $opentr = 0; # just open a tr, avoid blank line, Sun Jun 26 10:08:55 CST 2016
$columni = 0; $hasEndLine = 0;
for($hmi=$min_idx; $hmi<=$max_idx;$hmi++){
    $field = $gtbl->getField($hmi);
    $fieldinputtype = $gtbl->getInputType($field);
    
	if($field == null || $field == ''){
		continue;
	}
	else if($fieldinputtype == 'hidden'){
        $hiddenfields .= "<input type=\"hidden\" name=\"".$field."\" id=\"".$field."\" value=\""
		.$hmorig[$field]."\"/>\n";
        continue;
    }
    else if($gtbl->filterHiddenField($field, $opfield,$timefield)){
        continue;
    }
    else if(!$user->canWrite($field)){
        $out .= "<--NOWRITE--><input type=\"hidden\" id=\"".$field."\" name=\"".$field."\" value=\""
		.$hmorig[$field]."\" />";
        continue;
    }
	
	if(inString('password', $field) || inString('pwd', $field)){
        $hmorig[$field] = '';
    }
    else if($isAddByCopy && $gtbl->getReadOnly($field, $fieldinputtype) != ''){
        $hmorig[$field] = '';
    }
	if(($id=='' || $id==0) && $hmorig[$field] == '' 
		&& $hmfield[$field.'_default'] != ''){
		$hmorig[$field] = $hmfield[$field.'_default'];
	}
    
	if($closedtr == 1){
        $out .= "<tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
             .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">";
        $closedtr = 0; $opentr = 1;
    }
	
    if($fieldinputtype == 'select'){
        if($gtbl->getSingleRow($field) == '1' && $opentr < 1){
			$out .= "</tr><tr height=\"30px\" valign=\"top\"  onmouseover=\"javascript:"
				."this.style.backgroundColor='".$hlcolor
				."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">"; 
		}
		if(true){
        	$out .= "<td nowrap style=\"vertical-align:top\"><b>".$gtbl->getCHN($field)."</b>:&nbsp;</td>";
        	$out .= "<td style=\"vertical-align:top\">".$gtbl->getSelectOption($field, $hmorig[$field],'',0,$gtbl->getSelectMultiple($field))
			." <br/> ".$gtbl->getMemo($field)." <input type=\"hidden\" id=\"".$field
			."_select_orig\" name=\"".$field."_select_orig\" value=\"".$hmorig[$field]."\" /></td>";
        	$opentr = 0;
		}

        if($gtbl->getSingleRow($field) == '1'){
			$out .= "</tr><tr height=\"30px\" valign=\"middle\"  "
				."onmouseover=\"javascript:this.style.backgroundColor='".$hlcolor
				."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">"; 
			$opentr = 1;
		}
    }
	else if($fieldinputtype == 'textarea'){

        $hmorig[$field] = str_replace("<br/>", "\n", $hmorig[$field]); 
		$acceptVal = $gtbl->getAccept($field);
		
        if($gtbl->getSingleRow($field) == '1'){
            if($tmpmemo == ''){
				# memo desc
            }
            if($opentr < 1){
            	$out .= "</tr>\n<tr>";	
            }
            $out .= "<td style=\"vertical-align:top\"><b>".$gtbl->getCHN($field).($acceptVal==''?'':'<span class="redb">*</span>')."</b>:</td><td colspan=\""
	    	      .($form_cols)."\">
                        <div id='".$field."_myeditordiv' style='width:680px;height:450px;display:none'></div>
                        <div id='".$field."_mytextdiv' style='width:680px;height:450px;display:block'>
                        <textarea id=\"".$field."\" name=\"".$field."\" rows=\"11\" cols=\"85\"  class=\"search\""
            		." onclick=\"javascript:openEditor('".$rtvdir."/extra/htmleditor.php?field=".$field."&sid=".$sid."', '"
            		.$field."'); parent.switchArea('".$field."_myeditordiv','on'); parent.switchArea('"
            		.$field."_mytextdiv','off');\" ".$gtbl->getJsAction($field).$acceptVal.">".$hmorig[$field]."</textarea> </div><br/> "
            		.$tmpmemo." </td></tr><tr>";
            $out .= '';
            $opentr = 1;
        }
		else{
            $out .= "<td style=\"vertical-align:top\"><b>".$gtbl->getCHN($field).($acceptVal==''?'':'<span class="redb">*</span>')."</b>:</td>"
                    ."<td><textarea id=\"".$field."\" name=\""
                    .$field."\" rows=\"11\" cols=\"35\" ".$gtbl->getJsAction($field).$acceptVal." "
                    .$gtbl->getReadOnly($field)." class=\"search\">".$hmorig[$field]."</textarea> <br/> "
		            .$gtbl->getMemo($field)." </td>";
        }
    }
	else if($fieldinputtype == 'file'){
	    $origValue = $hmorig[$field];
        if($origValue != '' && $srcprefix != '' && !startsWith($origValue, 'http')){
            $hmorig[$field] = $srcprefix.'/'.$hmorig[$field];
        }
        if($columni % 2 != 0 || $gtbl->getSingleRow($field)){
			$out .= "</tr><tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:"
					."this.style.backgroundColor='".$hlcolor."';\" onmouseout=\"javascript:"
				."this.style.backgroundColor='';\">";
			$opentr = 1;
        }
        $fieldv = $hmorig[$field]; $fieldv = str_replace($shortDirName."/","", $fieldv);
        $isimg = isImg($fieldv);
        $out .= "<td nowrap style=\"vertical-align:top\"><b>".$gtbl->getCHN($field)."</b>:</td>"
			."<td style='word-break:break-all;vertical-align:top;'><input type=\"file\" id=\"".$field
			."\" name=\"".$field."\" size=\"20\" class=\"noneinput wideinput\" ".$gtbl->getJsAction($field)
			." /> <input type=\"hidden\" name=\"".$field."_orig\" value=\"".$fieldv."\" /> <br/> "
			.($fieldv==''?'':$fieldv)." ".$gtbl->getMemo($field)."</td>";
			$out .="<td colspan='4'> ".($isimg==1?"<img src=\"".$fieldv."\" alt=\"-x-\" width=\"118px\" /><br/>"
			.$fieldv:"")." <script>document.getElementById('"
			.$formid."').enctype='multipart/form-data';</script>  </td></tr><tr>";
	    $opentr = 1;

    }else if($gtbl->getExtraInput($field, $hmorig) != ''){

		if($act=='add'){
			$iconImage = 'plus.gif';
		}
		else if($act=='modify'){
			$iconImage = 'minus.gif';
		}

		$out .= "</tr><tr><td><b>".$gtbl->getCHN($field)."</b>:</td><td colspan=\"".$form_cols."\"><span id=\"span_"
			.$act."_".$field."\"><input id=\"".$field."\" name=\"".$field."\" class=\"search\" value=\""
			.$hmorig[$field]."\" /></span> <span id=\"span_".$act."_".$field."_v\"><a href=\"javascript:"
			."void(0);\" onclick=\"javascript:doActionEx('".$gtbl->getExtraInput($field, $hmorig)."&act="
			.$act."&field=".$field."&oldv=".$hmorig[$field]."&otbl=".$tbl."&oid=".$id."&isheader=0&sid=".$sid."&randi=".rand(0,99999)."','extrainput_"
			.$act."_".$field."_inside');document.getElementById('extrainput_".$act."_".$field
			."').style.display='block'; document.getElementById('extendicon_${id}_$field').src='./img/minus.gif';"
			."\"><img border=\"0\" id=\"extendicon_${id}_$field\" src=\"img/".$iconImage."\""
			." width=\"15\" height=\"15\" /></a></span> <div id=\"extrainput_".$act."_".$field."\""
			." class=\"extrainput\"> ";
		$out .= "<table width=\"100%\" ><tr><td width=\"100%\" style=\"text-align:right\"> <b> "
			."<a href=\"javascript:void(0);\" onclick=\"javascript: document.getElementById('extrainput_"
			.$act."_".$field."').style.display='none';  document.getElementById('extendicon_${id}_$field').src="
			."'./img/plus.gif';\">X</a> </b> &nbsp; </td></tr><tr><td> <div id=\"extrainput_".$act."_"
			.$field."_inside\"></div></td></tr></table> </div>";
		//$out .="  </div>   <br/>".$gtbl->getMemo($field)." </td>  </tr><tr>";
		if($field != "operatelog" && $id != ''){
			$out .= "<script type=\"text/javascript\"> parent.doActionEx('".$gtbl->getExtraInput($field, $hmorig)
				."&act=".$act."&otbl=".$tbl."&field=".$field."&oldv=".$hmorig[$field]."&oid=".$id
				."&isheader=0&sid=".$sid."','extrainput_".$act."_".$field."_inside');document.getElementById('extrainput_"
				.$act."_".$field."').style.display='block'; </script>";
		}
		$out .= "   <br/>".$gtbl->getMemo($field)."</td></tr><tr>";
		$opentr = 1;

    }else{
		$acceptVal = $gtbl->getAccept($field);
		$tmpInputType = 'text';
		if(inString('time', $field)){ $tmpInputType = 'datetime-local';}
		else if(inString('date')){ $tmpInputType = 'date'; }
		else if(inString('0', $hmfield[$field.'_default'])){ $tmpInputType = 'number'; }
		if($gtbl->getSingleRow($field) == '1'){
            $out .= "</tr>\n<tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
                    .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\"><td style=\"vertical-align:top\" "
                    .$gtbl->getCss($field)."><b>".$gtbl->getCHN($field).($acceptVal==''?'':'<span class="redb">*</span>')."</b>:</td><td colspan=\"".($form_cols)
                    ."\"  style=\"vertical-align:top\"><input type=\"".$tmpInputType."\" id=\"".$field."\" name=\""
                     .$field."\" class=\"\" style=\"width:600px\" value=\""
                    .$hmorig[$field]."\" ".$gtbl->getJsAction($field).$acceptVal." "
                    .$gtbl->getReadOnly($field)." /> <br/>   ".$gtbl->getMemo($field)." </td></tr><tr>";
            $opentr = 1;
		}
		else{
            $rdonly = $gtbl->getReadOnly($field);
        	$out .= "<td nowrap ".$gtbl->getCss($field)." style=\"vertical-align:top\"><b>".$gtbl->getCHN($field).($acceptVal==''?'':'<span class="redb">*</span>')."</b>: "
        	        ."</td><td style=\"vertical-align:top\"><input type=\"".$tmpInputType."\" id=\""
        	        .$field."\" name=\"".$field."\" class=\"noneinput wideinput\" value=\"".$hmorig[$field]."\" "
                    .$gtbl->getJsAction($field).$acceptVal." ".$rdonly." ";
             if(in_array($field, $timefield) && $rdonly==''){
                $out .= " onclick=\"javascript:WdatePicker();\"";
             }
             $out .= "/> <br/> "
        	        .$gtbl->getMemo($field)."</td>";
        	$opentr = 0;
		}

    }

    $out .= $gtbl->getDelayJsAction($field, $hmorig);
    $columni++;
    if($columni % $colsPerRow == 0){
        $out .= "</tr>";
        $closedtr = 1;
    }

    //print "i:[".$columni."]\n"; 
    if(++$rows % 6 == 0 && $closedtr == 1){
        $out .= "<tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
                .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\" >"
                ."<td style=\"border-top: 1px dotted #cccccc; vertical-align:middle;\" colspan=\""
                .$form_cols."\">  </td> </tr>";
        $hasEndLine = 1;
    }
    else{
        $hasEndLine = 0;
    }

}

if($hasEndLine == 0){
    $out .= "<tr height=\"10px\"><td style=\"border-top: 1px dotted #cccccc; vertical-align:middle;\" colspan=\""
        .$form_cols."\">  </td></tr>";
}
$out .= "<tr><td colspan=\"".$form_cols."\" align=\"center\">"
        ."<input type=\"submit\" name=\"addsub\" id=\"addsub\""
        ."onclick=\"javascript:doActionEx(this.form.name,'actarea');\" /> \n";
$out .= "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"".$id."\"/>\n ".$hiddenfields."\n";
$out .= "&nbsp;&nbsp;&nbsp;<input type=\"reset\" name=\"resetbtn\" />";
$out .= "&nbsp;&nbsp;&nbsp;<input type=\"button\" name=\"cancelbtn\" value=\"".$lang->get("func_cancel")."\" "
        ."onclick=\"javascript:switchArea('contentarea_outer','off');\" /> <br/><span id='respFromServ'></span> </td></tr>";
$out .= "</table></form></fieldset><br/>";
$out .= "<style>.redb{ color:red; font-size:16px; font-weight:bold; padding-left:2px;}</style>";

#$out .= "<script> parent.userinfo.targetId='".$id."'; parent.userinfo.act='".$act."'; </script>"; 
# relocated to comm/footer.inc

} # end of hasDisableW

?>