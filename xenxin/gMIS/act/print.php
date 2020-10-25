<?php
# print out a table, fields may come from mutiple tables
# added by Wadelau, on Sat Feb  4 13:18:25 CST 2012
# remedy by wadelau@ufqi.com, Wed Jul  1 11:16:57 CST 2015

$smttpl = substr($smttpl,0,strlen($smttpl)-5)."_".$tbl.".html";
if(1 && is_file($viewdir."/".$smttpl)){
    $out .= __FILE__.": smttpl:[".$smttpl."]\n";
}
else{
    #$out .= "没有指定打印模板，使用默认模板. ".$viewdir."/".$smttpl."\n";
    $smttpl = '';
}

$data = array(); # 用于打印模板的数据集
$srcprefix = $gtbl->getSrcPrefix();
$tblName = $gtbl->getTblCHN();

$out .= "<div align=\"center\"><h2>".$lang->get($_CONFIG['agentname']).($tblName=='' ? $tit : $tblName)."</h2>";
$data['title'] = $_CONFIG['agentname'].($tblName=='' ? $tit : $tblName);
$htmlheader = str_replace('TITLE', $tit.": ".$id, $htmlheader);
$data['id'] = $id;

$hmorig = array();
if($hasid){
    $gtbl->setId($id);
    $hmorig = $gtbl->getBy("*", null);
    $gtbl->setId('');
}
else{
    $fieldargv = array();
    for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
        $field = $gtbl->getField($hmi);
        if($field == null | $field == ''
                || $field == 'id'){
            continue;
        }
        if(array_key_exists($field, $_REQUEST)){
            $gtbl->set($field, $_REQUEST[$field]);
            $fieldargv[] = $field."=?";
        }
    }
    $hmorig = $gtbl->getBy("*", implode(" and ", $fieldargv));
}
if($hmorig[0]){
    $hmorig = $hmorig[1][0];
}

$data['hminfo'] = $hmorig;

$printref = $gtbl->getPrintRef(0);
if($printref != ''){
    $refarr = explode("|",$printref);
    foreach($refarr as $k=>$ref){
        $refdetail = explode(":",$refarr[$k]);
        $linkinfo = explode("=", $refdetail[1]); $rdnum = rand(0,9999);
        $out .= "\n<span id=\"linkinfo_".$rdnum."\"></span><script type=\"text/javascript\"> doActionEx('./act/readfield.php?tbl="
                .$refdetail[0]."&pnsk".$linkinfo[0]."=".$hmorig[$linkinfo[1]]."&pnob".$linkinfo[0]."=1&fieldlist=".$refdetail[2]
                ."&isheader=0&isoput=0','linkinfo_".$rdnum."');</script><br/>\n";
    }
}
$out .= "<table align=\"center\" width=\"800px\" border=\"0px\" class=\"printtbl\" cellpadding=\"6px\">";
$out .= "\n<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
        .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\">";

$lastclosed = 0; $tdi = 0; $skiptag = $_CONFIG['skiptag'];

for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){

    $field = $gtbl->getField($hmi);
	$fieldv = $hmorig[$field];
    $nextfield = $gtbl->getField($hmi+1);
    $fieldinputtype = $gtbl->getInputType($field);
    $nextfieldinputtype = $gtbl->getInputType($nextfield);
    if($field == null || $field == ''){
        continue;
    }
    $hasclosed = 0;
    $needcloserow = 0;

    #if($field == 'password'){
	if(inString('password', $field) || inString('pwd', $field)){
        $fieldv = $hmorig[$field] = $skiptag;
    }
    if($lastclosed == 1){
		$out .= "\n<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
		        .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\" idata=\"$i-$lastclosed\""
		        ." fieldname=\"$field\">";
		$tdi = 0;
    }

    if(!$user->canRead($field, '', '', $_REQUEST['id'], $id)){
		$fieldv = $skiptag;
        $out .= "<td idata=\"$i\" fieldname=\"$field\"><b>".$gtbl->getCHN($field)."</b>:&nbsp;</td><td> ".$fieldv."</td>";
		$tdi++;
    }
	else if($fieldinputtype == 'select'){
        $out .= "<td idata=\"$i\" fieldname=\"$field\"><b>".$gtbl->getCHN($field)."</b>:&nbsp;</td><td> "
                .$gtbl->getSelectOption($field, $hmorig[$field],'',1, $gtbl->getSelectMultiple($field) )."</td>";
		$tdi++;
    }
	else if($gtbl->getFieldPrint($field) != ''){
        $refdetail = explode(":", $gtbl->getFieldPrint($field));
        $urlpart = "";
        $tmparr = explode(",",$refdetail[1]);
        foreach($tmparr as $k=>$v){
            $link = explode("=",$v);
            $val = "";
            if($link[1] == 'THIS_TABLE'){
                $val = $tbl;
            }else{
                $val = $hmorig[$link[1]];
            }
            $urlpart .= "pnsk".$link[0]."=".$val."&pnob".$link[0]."=1&";
        }
        $urlpart = substr($urlpart, 0, strlen($urlpart)-1);
        $urlpart .= "&pnsm=1";
        $rdnum = rand(0,99999);
        if($lastclosed == 0){
            $out .= "</tr>\n<tr>";
        }
        $out .= "<td width=\"20%\" nowrap><b>".$gtbl->getCHN($field)."</b>:</td><td colspan=\"".($form_cols)
            ."\"><span id=\"linkinfo_".$rdnum."\"></span><script type=\"text/javascript\"> doActionEx('./act/readfield.php?tbl="
            .$refdetail[0]."&".$urlpart."&fieldlist=".$refdetail[2]."&isheader=0&isoput=0&mode=intbl&sid=$sid','linkinfo_"
            .$rdnum."');</script></td>\n";
		$tdi++; $needcloserow = 1;
    }
	else{

		if($fieldinputtype == 'file'){
		    
		    $origValue = $hmorig[$field];
		    if($origValue != '' && $srcprefix != '' && !startsWith($origValue, 'http')){
		        $fieldv = $hmorig[$field] = $srcprefix.'/'.$hmorig[$field];
		    }
		    
			$isimg = isImg($fieldv);
			if(strpos($fieldv, "$shortDirName/") !== false){ $fieldv = str_replace("$shortDirName/", "", $fieldv); }
			if($isimg){
				$fieldv = "<img src='".$fieldv."' width='80%' alt='-x-' />";
			}
		}

        if($gtbl->getSingleRow($field) == '1'){
			if($tdi > 0){
				$out .= "</tr>\n<tr idata=\"$i-$lastclosed\">";
			}
            if(inString('&lt;', $fieldv)){
                $fieldv = str_replace('&lt;', '<', $fieldv);
            }
            $out .= "<td><b>".$gtbl->getCHN($field)."</b>:&nbsp;</td>"
                ."<td colspan=\"".($form_cols)."\"> ".$fieldv." </td>";
			$needcloserow = 1;
        }
		else{
            $out .= "<td nowrap idata=\"$i\" fieldname=\"$field\"><b>".$gtbl->getCHN($field)."</b>:&nbsp;</td>";
            $out .= "<td class=\"tdprintfixedwidth\"> ".$fieldv." </td>";
			$tdi++;
        }
		if($needcloserow ==0
			&& ($nextfieldinputtype == 'file')){
            #$out .= "<td colspan=\"".($form_cols)."\"> <!-- $field --> </td>";
			#$tdi++;
		}
    }

    if($needcloserow == 1){
        if($gtbl->getExtraInput($nextfield) != ''){
            $out .= "<td colspan=\"".($form_cols)."\"> $field </td>";
        }
        $out .= "</tr>";
        $hasclosed = 1;
    }

    if($hasclosed == 0 && ++$i % 2 == 0){
        $out .= "</tr>";
        $out .= "\n<tr height=\"30\" valign=\"middle\"  onmouseover=\"javascript:this.style.backgroundColor='"
                .$hlcolor."';\" onmouseout=\"javascript:this.style.backgroundColor='';\" idata=\"$i\" "
                ."fieldname=\"$nextfield\">";
        $lastclosed = 0; $tdi = 0;
    }
	else if($hasclosed == 1){
        $lastclosed = 1;
    }

}

if($lastclosed < 1){ $out .= "</tr>"; }

$out .= "</table> <br/>";
$out .= "</div>";

$printref = $gtbl->getPrintRef(1);

$smt->assign('data', $data);

?>