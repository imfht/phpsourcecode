<?php
require("./comm/header.inc.php");

$out = str_replace('TITLE', $lang->get($_CONFIG['agentname']), $out);
$isgo = true;
if($tbl == ''){
    if(true){
        $tbl = $_CONFIG['welcometbl'];
        $_REQUEST['tbl'] = $tbl;
        debug(__FILE__.": empty tbl detection. 1703082103.");
        redirect($url, $time=3000, $msg='');
    }
    $isgo = false;
}
else{
    $out .= "<script type=\"text/javascript\">currenttbl='".$tbl."';\ncurrentdb='"
        .$db."';\n currentlistid= {};\n currentpath='".$rtvdir."';\n userinfo={"
        ."'id':'".$userid
        ."','email':'".$user->getEmail()
        ."','group':'".$user->getGroup()
        ."','branch':'".$user->get('branchoffice')
        ."','sid':'".$sid
        ."'};\n";
	$out .= "userinfo.\$lang = {"
		."\"func_cancel\":\"".$lang->get("func_cancel")."\","
		."\"notice_success\":\"".$lang->get("notice_success")."\","
		."\"notice_failure\":\"".$lang->get("notice_failure")."\","
		."\"notice_delete_soon\":\"".$lang->get("notice_delete_soon")."\","
		."\"notice_datap_invalid\":\"".$lang->get("notice_datap_invalid")."\","
		."\"more\":\"".$lang->get("more")."\","
		."\"func_view\":\"".$lang->get("func_view")."\","
		."\"func_edit\":\"".$lang->get("func_edit")."\","
		."\"func_print\":\"".$lang->get("func_print")."\","
		."\"func_delete\":\"".$lang->get("func_delete")."\","
		."\"func_copy\":\"".$lang->get("func_copy")."\","
		."\"func_orderby\":\"".$lang->get("func_orderby")."\","
		."\"func_pivot_group_col\":\"".$lang->get("func_pivot_group_col")."\","
		."\"func_pivot_value_col\":\"".$lang->get("func_pivot_value_col")."\","
		."\"func_pivot_order_col\":\"".$lang->get("func_pivot_order_col")."\","
		."\"func_pivot_seg_range\":\"".$lang->get("func_pivot_seg_range")."\","
		."\"input\":\"".$lang->get("input")."\","
		."\"\":\"\""
		."};\n";	
	$out .= "</script>\n";
}

$out_header = $out;
$out = ""; # re-init it

if($isgo){

$hmconf = GTbl::xml2hash($xmlpathpre, $elementsep, $mydb, $tbl);
$hmconf[0]['mydb'] = $mydb; # Thu, 1 Dec 2016 19:43:30 +0800
$gtbl = new GTbl($tbl, $hmconf[0], $elementsep);
if(Wht::get($_REQUEST, 'tit') == ''){
    $tit = $gtbl->getTblCHN();
}

# get detail path
$module_path = ''; $levelcode = ''; $codelist = '';
include_once($appdir."/comm/modulepath.inc.php");

$jdo = mkUrl($jdo, $_REQUEST, $gtbl); # ".($isheader?"</h3>":"")."
$accMode = $gtbl->getMode();

$out .= "<table align=\"center\" width=\"98%\"  style=\"background:transparent\">";
$out .= "<tr><td width=\"40%\" ".($isheader?"class=\"f17px\"":"")."> <!-- <b> &Pi; <a href=\"".$url."\">".$lang->get('navi_homepage')."</a> "
        ."<span class=\"f17px\">&rarr;</span> --> ".$module_path." </b> </td>";
$out .= "<td style=\"text-align:left\" colspan=\"18\">";

if($accMode!='' && ($accMode == 'r' || !inString('w', $accMode))){
    # readonly
}
else{
    $out .= "&nbsp;&nbsp; <button onclick=\"javascript:doActionEx('".$jdo."&act=add','contentarea');\">".$lang->get("func_create")."</button>";
}

$refArr = $gtbl->getRelatedRef($jdo."&act=list"); # related ref, added on Thu Apr 12 18:54:43 CST 2012
#print_r($refArr);
$maxRelatedCount = 6;
if(count($refArr) > 0){
    # specified related modules
    $maxRelatedCount = 3;
}
if(true){ # auto
    $hm = $gtbl->execBy($sql="select linkname, modulename, levelcode, thedb from ".$_CONFIG['tblpre']."info_menulist "
            ."where levelcode like '".substr($levelcode, 0, strlen($levelcode)-2)."__' "
            ."and levelcode<>'$levelcode' order by levelcode limit ".$maxRelatedCount, null,
            $withCache=array('key'=>'info_menulist-select-level-'.$levelcode));
    #debug($sql);
    if($hm[0]){
        $hm = $hm[1]; $tmphref = '';
        foreach($hm as $k=>$v){
            if($v['modulename'] != ''){
                $tmphref = $ido."&tbl=".$v['modulename']."&db=".$v['thedb'];
            }
            else{
                $tmphref = $url."&navidir=".$v['levelcode'];
            }
            $refArr[] = array('target'=>"window.location.href='".$tmphref."';",
                    'name'=>$v['linkname'], 'href'=>'JS');
        }
    }
}
if(count($refArr) > 0){
    foreach($refArr as $k=>$v){
        $tmphref = $v['href'];
        $isjs = 0;
        if($tmphref == 'JS'){
            $tmphref = $v['target'];
            $isjs = 1;
        }
        if($isjs==0 && strpos($tmphref,"sid=") === false){
            if(strpos($tmphref,"?") === false){
                $tmphref .= "?";
            }
            else{
                $tmphref .= "&";
            }
            $tmphref .= SID."=".$sid;
        }
        if($isjs == 0){
            $out .= "&nbsp; <button onclick=\"javascript:doActionEx('".$tmphref."','".($v['target']==''?'contentarea':$v['target'])
				."');\">".$v['name']."</button>";
        }else{
            $out .= "&nbsp; <button onclick=\"javascript:".$tmphref."\">".$v['name']."</button>";
        }
    }
}

$out .= "&nbsp;&nbsp;&nbsp;<button id=\"refreshbtn\" name=\"refreshbtn\" onclick=\"javascript:window.location.reload();\" "
        ."title=\"".$lang->get('func_refresh')."\">".$lang->get("func_refresh")."</button> &nbsp;&nbsp; &nbsp;</td></tr>";

$out .= "</table>";

$out .= "<div id=\"contentarea_outer\" style=\"display:none;border:0px dotted green; width:98%; margin-left:auto; margin-right:auto;\">";
$out .= "<div id=\"close_span\" style=\"text-align:right;height:10px;clear:both;\">"
        ."<button id=\"btn_close\" onclick=\"javascript:switchArea('contentarea_outer','off');\">X Close</button>"
        ."&nbsp;&nbsp;&nbsp;</div>";
$out .= "<div id=\"contentarea\" style=\"postion:absolute;\" align=\"center\"></div></div>\n";

$out .= "<span id=\"addarea\"></span> <span id=\"loadarea\"></span>\n";
$out .= "<div id=\"actarea\" align=\"center\"><br/><br/><span style=\"margin-left:38px;\"> Loading...... "
        ."<a href=\"javascript:doAction('".$jdo."&act=list');\"> ".$lang->get("func_reload")." </a> </span><br/><br/> &nbsp;</div>\n";
$out .= "<div id=\"tagmenu\" style=\"position:absolute;left:-50px;\"></div>";
$out .= "<div id=\"addareaextradiv\" onmousedown=\"javascript:fDragging(this, event, true);\" class=\"div\" "
        ."style=\"position:absolute;width: 350px;left:250px; top:250px;z-index:11; display:none\">"
        ."<span id=\"addareaextratitle\" style=\"background:#5f8ac5;width:100%;height:20;\">\n";

$out .= "<table style=\"color:white;font-weight:bold;\" id=\"addareaextratab\"><tr><td width=\"99%\">addarea&nbsp;</td>"
        ."<td align=\"right\"> <button name=\"cancelbtn\" id=\"cancelbtn\" "
        ."onclick=\"javascript:var a1706=document.getElementById('addareaextradiv'); if(a1706 != null){ "
        ."a1706.style.display='none';}\">&nbsp;X&nbsp;</button> </td></tr></table></span>"
        ."<span id=\"addareaextra\"></span> </div>\n";

if($act == ''){
    $out .= "<script async type=\"text/javascript\">if(typeof doAction == 'undefined'){ var doActionTimer=window.setTimeout(function(){ doAction('".$jdo."&act=list'); }, 2*1000); }else{ doAction('".$jdo."&act=list');}</script>\n";
}
else{
    $out .= "<script async type=\"text/javascript\">if(typeof doAction == 'undefined'){ var doActionTimer=window.setTimeout(function(){ doActionEx('".$jdo."&act=".$act."','contentarea'); }, 2*1000); }else{ doActionEx('".$jdo."&act=".$act."','contentarea');}</script>\n";
}

$out .= "<!--bottom line-->";

$out .= "<table align=\"center\" width=\"98%\"  style=\"background:transparent\"> <tr><td width=\"25%\" colspan=\"4\"> <b>"
        ." ".$module_path." </b> &nbsp; </td>";
$out .= "<td style=\"text-align:left;margin-right:58px\" colspan=\"15\"> ";

if($accMode!='' && ($accMode == 'r' || !inString('w', $accMode))){
    # readonly
}
else{
    $out .= "&nbsp;  <button onclick=\"javascript:doActionEx('"
        .$jdo."&act=add','contentarea');\">".$lang->get("func_create")."</button>  ";
}
# repeat related menu, Wed, 12 Apr 2017 21:34:24 +0800
if(count($refArr) > 0){
    foreach($refArr as $k=>$v){
        $tmphref = $v['href'];
        $isjs = 0;
        if($tmphref == 'JS'){
            $tmphref = $v['target'];
            $isjs = 1;
        }
        if($isjs==0 && strpos($tmphref,"sid=") === false){
            if(strpos($tmphref,"?") === false){
                $tmphref .= "?";
            }
            else{
                $tmphref .= "&";
            }
            $tmphref .= SID."=".$sid;
        }
        if($isjs == 0){
            $out .= "&nbsp; <button onclick=\"javascript:doActionEx('".$tmphref."','".($v['target']==''?'contentarea':$v['target'])
            ."');\">".$v['name']."</button>";
        }else{
            $out .= "&nbsp; <button onclick=\"javascript:".$tmphref."\">".$v['name']."</button>";
        }
    }
}
        
$out .= "&nbsp;&nbsp; <button id=\"refrehbtn2\" name=\"refreshbtn2\" "
    ."onclick=\"javascript:window.location.reload();\" title=\"".$lang->get("func_refresh")."\">".$lang->get("func_refresh")."</button>  &nbsp;&nbsp;&nbsp;";
$out .= "</td> </tr></table>\n";

$out_footer = "<hr width=\"1\"/> &nbsp;&nbsp;&nbsp;<span id=\"noticediv\" style=\"color:green;\"> </span>";

if(true){
    //- output more act options
    $actArr = $gtbl->getActOption();
    $out .= "\n<script type=\"text/javascript\">userinfo.actListOption=[";
    $tmpstr = '';
    if(count($actArr) > 0){
        foreach($actArr as $actk=>$actv){
            $tmpstr .= "{\"actName\":\"".$actv[1]."\", \"actUrl\":\"".$actv[0]."\""."},";
        }
    }
    if(endsWith($tmpstr, ',')){ $tmpstr = substr($tmpstr, 0, strlen($tmpstr)-1); }
    $out .= $tmpstr;
    $out .= "]; userinfo.dataList=[];</script>";
}

$data['title'] = $gtbl->getTblCHN();

$gtbl = null;
$hmconf = null;

}

$smttpl = getSmtTpl(__FILE__, $act='');
$smt->assign('welcomemsg',$welcomemsg);

$smt->assign('isheader', $isheader);
$smt->assign('out_header', $out_header);
$smt->assign('out_footer', $out_footer);

$smt->assign('content',$out);
$smt->assign('rtvdir', $rtvdir);

$smt->assign('ido', $ido);
$smt->assign('jdo', $jdo);
$smt->assign('url', $url);

$smt->assign('sid', $sid);

require("./comm/footer.inc.php");

#print $out;

?>