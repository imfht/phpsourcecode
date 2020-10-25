<?php
/*
 * pick up/select on all avaliable values
 * added by xenxin@ufqi.com
 * init. Mon Sep 17 21:21:03 CST 2018
 * updt. Sat Mar 28 11:16:55 CST 2020
 */

include_once($appdir.'/class/pickup.class.php');

$formid = "gmis_pickup";
$hiddenfields = "";
$colsPerRow = 1; $shortFieldCount = 4;
$pickupFieldCount = Wht::get($_REQUEST, 'pickupfieldcount');
$pickupFieldCount = $pickupFieldCount < $shortFieldCount ? $shortFieldCount : $pickupFieldCount;
$rowHeight = 40;

$pickup = new PickUp($gtbl->get('args_to_parent')); # args see class/GTbl
$pickup->setTbl($gtbl->getTbl());
$pickup->set('fieldlist', $gtbl->getFieldList());
$pickup->set('myid', $gtbl->getMyId());
$base62x = new Base62x();
$base62xTag = 'b62x.';

$out .= "<fieldset style=\"border-color:#5f8ac5;border: 1px solid #5f8ac5; background:#E8EEF7;\">"
    ."<legend><h4>".$lang->get("func_pickup")."</h4></legend><form id=\""
	.$formid."\" name=\"".$formid."\" method=\"post\" action=\"".$jdo."&act=list\" "
	.$gtbl->getJsActionTbl()."><table cellspacing=\"0\" cellpadding=\"0\" "
	." style=\"border:0px solid black; width:98%; margin-left:auto; margin-right:auto; background:transparent;\">";

$out .= "<tr height='".($rowHeight/2)."px'><td width=\"1%\">&nbsp;</td>
            <td width=\"9%\"> </td>
            <td>&nbsp; </td>
            <td style='width:35px;'>";
if($pickupFieldCount <= $shortFieldCount){ 
    $out .= "<a onclick=\"javascript:parent.fillPickUpReqt('"
         .$jdo."', '', $max_idx, 'moreoption', this);\" title=\"".$lang->get("more")."\"><b>+".$lang->get("more")."</b></a>";
}
else{
    $out .= "<a onclick=\"javascript:parent.fillPickUpReqt('"
    	 .$jdo."', '', $shortFieldCount, 'moreoption', this);\" title=\"-".$lang->get("more")."\"  style=\"color:#ffffff;background-color:#1730FD;\"><b>-".$lang->get("more")."</b></a>";
}
$out .= "</td></tr>";
			
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
            }
            else{
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
	#else if($field == 'password'){
    else if(inString('password', $field) || inString('pwd', $field)){
        $hmorig[$field] = '';
        continue;
    }
    else if($fieldinputtype == 'file'){
		continue;
	}

    # main fields
    if(true){
        $options = ""; $prtFieldType = 'string';
		$hasHitOption = 0;
        $optionListAll = $pickup->getOptionList($field, $fieldinputtype);
        $optionList = $optionListAll[0];
        $prtFieldType = $optionListAll[1];
        #debug("field:$field options:".serialize($optionList));
        $opCount = count($optionList);
        if($opCount > 0){
            $opi = 0; $lastopv = null;
            foreach($optionList as $ok=>$ov){
				$urlParts = array();
                if($fieldinputtype == 'select'){
                    $opv = $ov[$field.'_uniq_all']; # same with class/pickup
                    $origopv = $opv;
                    if($opv== ''){ $opv = '(Empty)'; } # === in case of '0'
                    else{
                        $opv = str_replace('<', '&lt;', $opv);
                    }
                    $opv = $gtbl->getSelectOption($field, $opv, '', $needv=1, $isMultiple=0); 
                    $urlParts = fillPickUpReqt($jdo, $field, $origopv, 'inlist', $base62x); 
                    $options .= "<a href='javascript:void(0);' "
                        ." onclick=\"javascript:parent.fillPickUpReqt('".$jdo."', '$field', '$origopv', 'inlist', this);\""
                        ." style=\"".$urlParts[2]."\">";
                    $options .= $urlParts[1].$opv.'('.$ov['icount'].')';
                    $options .= "</a> ";
                    #debug(" select field:$field opv:$opv\n");
                }
                else if($prtFieldType == 'string'){
                    $opv = $ov[$field.'_uniq_all']; # same with class/pickup
                    if($opv== ''){ $opv = '(Empty)'; } # === in case of '0'
                    else{
                        $opv = str_replace('<', '&lt;', $opv);
                    }
                    $origopv = $base62xTag.$base62x->encode($opv);
                    $urlParts = fillPickUpReqt($jdo, $field, $origopv, 'containslist', $base62x); 
                    $options .= "<a href='javascript:void(0);' "
                        ." onclick=\"javascript:parent.fillPickUpReqt('".$jdo."', '$field', '$origopv', 'containslist', this);\""
                        ." style=\"".$urlParts[2]."\">";
                    $options .= $urlParts[1].$opv;
                    if(isset($ov['icount'])){
                        $options .= "(".$ov['icount'].")";
                    }
                    $options .= "</a> ";
                }
                else if($prtFieldType == 'number'){
                    if(true){
                        $opv = $ov[$field];
                        if($opv === ''){ $opv = '0'; } # === in case of '0'

                        if($lastopv !== null){
                            $origopv = $lastopv.'~'.$opv;
                            $urlParts = fillPickUpReqt($jdo, $field, $origopv, 'inrangelist', $base62x); 
                            $options .= "<a href='javascript:void(0);' "
                                ." onclick=\"javascript:parent.fillPickUpReqt('".$jdo."', '$field', '$origopv', 'inrangelist', this);\""
                                ." style=\"".$urlParts[2]."\">";
                            $options .= $urlParts[1].$lastopv."~".$ov[$field];
                        } 
                        else{
                            #$options .= "+~".$ov[$field]; # nothing < imin?
                        }
                        $options .= "</a> ";
                        
                        $lastopv = $ov[$field];

                        if($opi == $opCount-1){
                            $origopv = $lastopv.'~';
                            $urlParts = fillPickUpReqt($jdo, $field, $origopv, 'inrangelist', $base62x); 
                            $options .= "<a href='javascript:void(0);' "
                                ." onclick=\"javascript:parent.fillPickUpReqt('".$jdo."', '$field', '$origopv', 'inrangelist', this);\""
                                ." style=\"".$urlParts[2]."\">";
                            $options .= $urlParts[1].$ov[$field]."~";
                            $options .= "</a> ";
                        }
                    }
                }
                else{
                    debug("unsupported prtFieldType:$prtFieldType from field:$field skip....\n");
                }
                $opi++;
				if($hasHitOption == 0){
					if(isset($urlParts[1]) && $urlParts[1] == '-'){
						$hasHitOption = 1;
					}	
				}
            }
			$reqv = Wht::get($_REQUEST, "pnsk$field");
			if($reqv != '' && $hasHitOption == 0){
				$opType = ""; $origReqv = $reqv;
                if($fieldinputtype == 'select'){
                    $opType = "inlist";
                }
                else if($prtFieldType == 'string'){
                    $opType = "containslist";
                    $reqv = $base62xTag.$base62x->encode($reqv);
                }
                else if($prtFieldType == 'number'){
                    $opType = "inrangelist";
                }
				$options .= "<a href='javascript:void(0);' "
					." onclick=\"javascript:parent.fillPickUpReqt('".$jdo."', '$field', '$reqv', '$opType', this);\""
					." style=\"color:#ffffff;background-color:#1730FD;\">";
				$options .= '-'.$origReqv;
				$options .= "</a> ";
			}
        }
        if($options != ''){
            $out .= "<tr height=\"{$rowHeight}px\" valign=\"middle\" onmouseover=\"javascript:this.style.backgroundColor='"
                .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">";
            $out .= "<td></td>";
            $out .= "<td><b>".$gtbl->getCHN($field)."</b></td>";
            $out .= "<td style='word-break:all;word-spacing:10px;line-height:25px;'> $options </td>";
            $out .= "<td></td></tr>"; 
            $rows++; 
            $lastBlankTr = 0;
			$bgcolor = "#DCDEDE";
            if($rows%2 == 0){
                $bgcolor = "";
            }
        }
        else{
            #$pickupFieldCount++;
            #debug("\tfield:$field has no options. 1809191930. skip....\n");
        }
    }

    $out .= $gtbl->getDelayJsAction($field);       

    $columni++;
    if($columni % $colsPerRow == 0){
        $out .= "</tr>";
        $closedtr = 1;
    }

    if(true && $rows % 6 == 0 && $lastBlankTr == 0){
        $out .= "<tr height=\"".($rowHeight/2)."px\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
        	.$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\" ><td style=\"border-top: 1px dotted #cccccc; "
        	."vertical-align:middle;\" colspan=\"".$my_form_cols."\">  </td> </tr>";
        $lastBlankTr = 1;
    }

    if($rows >= $pickupFieldCount){
        break;
    }
}

if(false){
    $out .= "<tr height=\"10px\"><td style=\"border-top: 1px dotted #cccccc; vertical-align:middle;\" colspan=\""
        .$my_form_cols."\">  </td></tr>";
}
$out .= "<tr><td colspan=\"".$my_form_cols."\" align=\"center\">";
$out .= "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"".$id."\"/>\n ".$hiddenfields."\n";
$out .= "</td></tr>";
$out .= "</table> </form>  </fieldset>  <br/>";

#
# save as an alternative backup
# use javascript in front-end instead.
#
function fillPickUpReqt($myurl, $field, $fieldv, $oppnsk, $base62x=null){
    $newurl = $myurl;
    $urlParts = explode("&", $newurl);
    $hasReqK = false;
    $hasReqKop = false;
    $hasReqV = false;
    $tagPrefix = '+';
    $stylestr = '';
    $origFieldv = $fieldv; $reqVal = '';
    $base62xTag = 'b62x.'; # for string only
    if($base62x == null){
        $base62x = new Base62x();
    }
    if(inList($oppnsk, 'inlist,containslist,inrangelist')){
        #$fieldv = strtolower($fieldv); # why?
        $isString = false;
        if($oppnsk == 'containslist'){ $isString = true; }
		$urlPartsNew = array();
        foreach($urlParts as $k=>$v){
            $paraParts = explode("=", $v);
            if(count($paraParts) > 1){
                $reqk = $paraParts[0];
                $reqv = $paraParts[1];
                if($reqk == "pnsk$field"){
                    #$reqv = strtolower($reqv); # why?
                    if(true && $isString){
                        if(inString(',', $reqv)){
                            $tmpArr = explode(',', $reqv);
                            foreach($tmpArr as $tmpk=>$tmpv){
                                if(!startsWith($tmpv, $base62xTag)){
                                    $tmpArr[$tmpk] = $base62xTag.$base62x->encode($tmpv);
                                }
                            }
                            $reqv = implode(',', $tmpArr);
                        }
                        else{
                            if(!startsWith($reqv, $base62xTag)){
                                $reqv = $base62xTag.$base62x->encode($reqv);
                            }
                        }
                    }
					if($reqv != ''){ $reqVal = $reqv; }
                    if(inList($fieldv, $reqv)){
                        $tmpArr = array();
						if(is_array($reqv)){ $tmpArr=explode(',', $reqv); }
                        foreach($tmpArr as $tmpk=>$tmpv){
                            if($tmpv == $fieldv){
                                unset($tmpArr[$tmpk]); # break; ?
                            }
                        }
                        $reqv = implode(',', $tmpArr);
                        $hasReqV = true;
                    }
                    else{
                        $reqv .= ",$fieldv"; 
                    }
                    $hasReqK = true;
                }
                else if($reqk == "oppnsk$field"){
                    $reqv = $oppnsk; $hasReqKop = true;
                }
				$paraParts[0] = $reqk;
				$paraParts[1] = $reqv;
            }
            $v = implode('=', $paraParts);
			$urlPartsNew[$k] = $v;
        }
        #$newurl = implode('&', $urlParts);
		$newurl = implode('&', $urlPartsNew);
        if(!$hasReqK){
            $newurl .= "&pnsk$field=$fieldv";
            #$newurl .= "&pnsk$field=B62X.".Base62x::encode($fieldv);
        }
        if(!$hasReqKop){
            $newurl .= "&oppnsk=$oppnsk";
        }
        if($hasReqV){
            $tagPrefix = '-';
            $stylestr = 'color:#ffffff;background-color:#1730FD;';
        }
		else{
			if($reqVal != ''){
				#debug("found reqVal:$reqVal not in optionList.");	
			}
		}
    }
    else{
        debug("Unknown oppnsk:$oppnsk. 1809210905. \n");
    }
    $newurl .= "&act=list";
    #debug("fillPickUpUrl result: newurl:$newurl tagprefix:$tagPrefix\n");
    return array($newurl, $tagPrefix, $stylestr);
}

?>
