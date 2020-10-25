<?php
/*
 * deep and complex search on all fields
 * added by wadelau@ufqi.com for pbtt
 * Mon Aug 29 18:34:45 CST 2016
 */

$formid = "gtbl_search_form";

$hiddenfields = "";

$colsPerRow = 2;
if($_REQUEST['otbl'] != ''){
    $colsPerRow = 2;
}

# reset old?
$jdo = str_replace("&pnsk", "&oldpnsk", $jdo);

$out .= "<fieldset style=\"border-color:#5f8ac5;border: 1px solid #5f8ac5;\"><legend><h4>".$lang->get("func_deepsearch_hint")
	."</h4></legend><form id=\""
	.$formid."\" name=\"".$formid."\" method=\"post\" action=\"".$jdo."&act=list-dodeepsearch\" "
	.$gtbl->getJsActionTbl()."><table cellspacing=\"0\" cellpadding=\"0\" "
	." style=\"border:0px solid black; width:86%; margin-left:auto; margin-right:auto;\">";
$out .= "<tr><td width=\"11%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            <td width=\"11%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            <td width=\"11%\">&nbsp;</td>
            <td width=\"22%\">&nbsp;</td>
            </tr>";
			
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
            if($tmparr[0] == 'request'){ # see xml/info_attachfiletbl.xml
                $hmorig[$field] = $_REQUEST[$tmparr[1]];
            }else{
                $hmorig[$field] = $tmparr[0]; # see xml/tuanduitbl.xml
            }
        }
    } 

}

if($hmorig[0]){
    $hmorig = $hmorig[1][0]; 
}

$closedtr = 1; $opentr = 0; # just open a tr, avoid blank line, Sun Jun 26 10:08:55 CST 2016
$columni = 0; $my_form_cols = 4;
$skiptag = $_CONFIG['skiptag'];

for($hmi=$min_idx; $hmi<=$max_idx;$hmi++){
    $field = $gtbl->getField($hmi);
    $fieldinputtype = $gtbl->getInputType($field);
    
    $filedtmpv = $_REQUEST['pnsk_'.$field];
    if(isset($fieldtmpv)){
    	$hmorig[$field] = $fieldtmpv;	
    }

	if($field == null || $field == ''){
		continue;
	}
	if($fieldinputtype == 'hidden'){
        $hiddenfields .= "<input type=\"hidden\" name=\"".$field."\" id=\"".$field."\" value=\"".$hmorig[$field]."\"/>\n";
    }
    if($gtbl->filterHiddenField($field, $opfield,$timefield)){
        #continue;
    }
    #if($field == 'password'){
	if(inString('password', $field) || inString('pwd', $field)){
        $hmorig[$field] = '';
        continue;
    }
    else if($fieldinputtype == 'file'){
		continue;
	}

    if($closedtr == 1){
        $out .= "<tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
        	.$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">";
        $closedtr = 0; $opentr = 1;
    }

    if($fieldinputtype == 'select'){

        if($gtbl->getSingleRow($field) == '1' && $opentr < 1){
			$out .= "</tr><tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
				.$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">"; 
		}

		$out .= "<td nowrap>".$gtbl->getCHN($field).":&nbsp;</td>";
		$out .= "<td> <select style=\"width:60px\" name=\"oppnsk$field\" id=\"oppnsk$field\">"
			.$gtbl->getLogicOp($field, $skiptag)."</select> "
			.$gtbl->getSelectOption($field, $hmorig[$field],'',0,$gtbl->getSelectMultiple($field))." <br/> "
			.$gtbl->getMemo($field)." <input type=\"hidden\" id=\"pnsk".$field."\" name=\"pnsk".$field
			."\" value=\"".$hmorig[$field]."\" /></td>";
		$opentr = 0;

        if($gtbl->getSingleRow($field) == '1'){
			$out .= "</tr><tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
				.$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">"; $opentr = 1;
		}

    }
    else{
		//- .$gtbl->getAccept($field) , rm validator
		$out .= "<td nowrap ".$gtbl->getCss($field)."> ".$gtbl->getCHN($field).": </td><td> "
			. "<select style=\"width:60px\" name=\"oppnsk$field\" id=\"oppnsk$field\">"
			.$gtbl->getLogicOp($field, $skiptag)."</select> <input type=\"text\" id=\"pnsk"
			.$field."\" name=\"pnsk".$field."\" "
			."value=\"".$hmorig[$field]."\" ".$gtbl->getJsAction($field)." "
			.$gtbl->getReadOnly($field)." /> <br/> ".$gtbl->getMemo($field)."</td>";
		$opentr = 0;

    }

    $out .= $gtbl->getDelayJsAction($field);       

    $columni++;

    if($columni % $colsPerRow == 0){
        $out .= "</tr>";
        $closedtr = 1;
    }

    if(++$rows % 6 == 0 && $closedtr == 1){
        $out .= "<tr height=\"30px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
        	.$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\" ><td style=\"border-top: 1px dotted #cccccc; "
        	."vertical-align:middle;\" colspan=\"".$my_form_cols."\">  </td> </tr>";
    }

}

$out .= "<tr height=\"10px\"><td style=\"border-top: 1px dotted #cccccc; vertical-align:middle;\" colspan=\"".$my_form_cols."\">  </td></tr>";
$out .= "<tr><td colspan=\"".$my_form_cols."\" align=\"center\">"
	.$lang->get("func_deepsearch_oplogic").": <select id='pnsm' name='pnsm'><option value='and'>".$lang->get("func_andsearch")
	."</option> <option value='or'>".$lang->get("func_orsearch")."</option> </select> "
	."&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"addsub\" id=\"addsub\" "
	."onclick=\"javascript:doActionEx(this.form.name,'actarea');\" /> \n"; 
$out .= "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"".$id."\"/>\n ".$hiddenfields."\n";
$out .= "&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"button\" name=\"cancelbtn\" value=\"".$lang->get("func_cancel")."\" "
	."onclick=\"javascript:switchArea('contentarea_outer','off');\" /> </td></tr>";
$out .= "</table> </form>  </fieldset>  <br/>";

?>
