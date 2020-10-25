<?php
# In-site searching 
# Xenxin@ufqi
# Wed May 30 15:40:33 CST 2018

require("../comm/header.inc.php");
include("../comm/tblconf.php");
include_once($appdir."/class/base62x.class.php");
include_once($appdir."/class/insitesearch.class.php");

# variables
$MAX_SUCC_COUNT = 5;
$MAX_FIELD_COUNT = 99999;
$isep = "::";
$time_bgn = time();

$gtbl2 = $gtbl;
if($testDb != ''){
    $args = array('db'=>$_CONFIG['maindb']);
    $gtbl2 = new GTbl($tbl, $args, $sep);
    debug($gtbl2);
}

$iss = new InSiteSearch();

$issubmit = Wht::get($_REQUEST, 'issubmit');
$issout .= serialize($_REQUEST);
$isskw = Wht::get($_REQUEST, 'isskw');
$issLastId = Wht::get($_REQUEST, 'isslastid');

if($issLastId == ''){ $issLastId = 0; } # max fields return

# module path
$module_path = '';
include_once($appdir."/comm/modulepath.inc.php");

# actions
if($issubmit == 1 && $isskw != ''){

    if($act == 'init'){
        # init a searcho
        
    }
    else{

    $resultList = array();
    $succCount = 0;
    $succTblList = array();
    $tbl_all_count = 0;
    $fieldi = 0;
    $skipFieldI = $issLastId;
    $hm = $iss->execBy($sql="select id, idb, itbl, ifield from ".$iss->getTbl()
        ." where 1=1 order by icount desc, id desc limit 0, ".$MAX_FIELD_COUNT, null, 
        $withCache=array('key'=>'iss-fields-'.$issLastId));
    if($hm[0]){
        $hm = $hm[1]; 
        foreach($hm as $k=>$v){

            if($fieldi++ < $skipFieldI){ 
                #debug("fieldi:$fieldi less than $issLastId, skip next...");
                continue;
            }

            $issLastId++; # $v['id'];
            $idb = $v['idb'];
            $itbl = $v['itbl'];
            $ifield = $v['ifield'];

            $hm2 = $iss->execBy($sql2="select $ifield from $itbl where $ifield like '%$isskw%' limit 3",
                null, $withCache=array('key'=>"iss-search-$tbl-$ifield-$isskw"));
            if($hm2[0]){
                $hm2 = $hm2[1];
                foreach($hm2 as $k2=>$v2){
                    $resultList[$idb.$isep.$itbl.$isep.$ifield] = $v2;
                }
                $succTblList[] = $itbl;
                $succCount++;
                #debug("read succ from $itbl-$ifield, $succCount / $issLastId / $sql2 / hm2:".serialize($hm2));
            }
            else{
                #debug("read failed from $itbl-$ifield, skip...$issLastId ");
            }

            if($succCount > $MAX_SUCC_COUNT){
                debug("insitesearch succCount reached, exit now...");
                break;
            }
            $tbl_all_count++;
        }
    }
    else{
        debug("read iss fields failed. 201805310746.");
    }
    $data['result_list'] = $resultList;
    $data['isslastid'] = $issLastId;

    $moduleNameList = array();
    if(count($succTblList) > 0){
        $moduleList = implode("','", $succTblList);
        $hm = $iss->execBy($sql3="select * from ".$_CONFIG['tblpre']."info_menulist "
            ." where modulename in ('$moduleList') order by id",
            null, $withCache=array('key'=>'iss-read-module-path-'.$moduleList));
        if($hm[0]){
            $hm = $hm[1];
            foreach($hm as $k=>$v){
               $moduleNameList[$v['thedb'].$isep.$v['modulename']] = $v; 
            }
            #debug($moduleNameList);
        }
        else{
            debug("read modulename failed. 201805311254.");
        }
    }
    $data['module_list'] = $moduleNameList;

    }

}
else if($act == 'clickreport'){
    $objId = Wht::get($_REQUEST, 'objid');
    $fieldArr = explode($isep, $objId);
    debug("objId:[$objId] fieldArr:".serialize($fieldArr));
    $imd5 = md5( implode("\t\t", $fieldArr) );

    $hm = $iss->execBy($sql="update ".$iss->getTbl()
            ." set icount=icount+1 where imd5='$imd5' limit 1", null, null);
    if($hm[0]){
        debug("icount++ succ with sql:$sql\n");
    }
    else{
        debug("icount++ failed with sql:$sql\n");
    }
    
    $fmt = 'json';
    $_REQUEST['fmt'] = $fmt;
    $smttpl = '';

}
else{ # prepare form data
       
}

$time_all_cost = time() - $time_bgn;

# output
$smt->assign('welcomemsg',$welcomemsg);
$smt->assign('isheader', $isheader);
$smt->assign('out_header', $out_header);
$smt->assign('out_footer', $out_footer);

$smt->assign('rtvdir', $rtvdir);

$smt->assign('ido', $ido);
$smt->assign('jdo', $jdo);
$smt->assign('url', $url);
$smt->assign('sid', $sid);
$smt->assign('rtvdir', $rtvdir);
$smt->assign('output', $out);
$smt->assign('issout',$issout);

$smt->assign('issubmit', $issubmit);
$smt->assign('levelcode', $levelcode);
$smt->assign('modulepath', $module_path);

$smt->assign('isskw', $isskw);
$smt->assign('isep', $isep);
$smt->assign('max_last_id', 0); # starting point next
$smt->assign('tbl_all_count', $tbl_all_count+1);
$smt->assign('time_all_cost', $time_all_cost);

$smt->assign('act', $act);

#tpl
if($fmt == ''){
    $smttpl = getSmtTpl(__FILE__,$act='');
    $smttpl = 'insitesearch.html';
}
else{
    #debug("output $fmt format only....");
}

require("../comm/footer.inc.php");

?>
