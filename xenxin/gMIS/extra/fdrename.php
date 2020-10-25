<?php
#
# file and name rename and its subdirectories 
# wadelau@ufqi.com, Mon Nov 12 16:14:56 CST 2018
#

/*
require("../comm/header.inc.php");

$gtbl = new GTbl($tbl, array(), $elementsep);

include("../comm/tblconf.php");
*/

# main actions
# file dir rename

if($args['triggertype'] == 'renamecheck'){
    $out = ''; # 'fdrename: args:['.serialize($args).']';
    $fdname = Wht::get($_REQUEST, 'filename');
    if(!startsWith($fdname, '/')){ $fdname = '/'.$fdname; }
    
    $tmpObj = new WebApp();
    $fdname_orig = ''; $hasSubContent = true;
    $hm = $tmpObj->execBy("select id, parentname, pparentname from ".$_CONFIG['tblpre']."filedirtbl where parentid=$id", '');
    if($hm[0]){
        $hm = $hm[1][0];
        $parentname = $hm['parentname'];
        $pparentname = $hm['pparentname'];
        if($parentname != '' && $pparentname != ''){
            $fdname_orig = str_replace($pparentname, "", $parentname);
            debug("extra/fdrename: id:".$hm['id']." parentname:$parentname pparentname:$pparentname orig:$fdname_orig new:$fdname");
        }
        else{
            debug("extra/fdrename: cannot found orig. id:".$hm['id']." parentname:$parentname pparentname:$pparentname new:$fdname");
        }
    }
	else{
		$hasSubContent = false;
	}
    if($fdname_orig != '' && $fdname_orig != $fdname){
        $out .= updateSubDir($tmpObj, $id, $fdname, $fdname_orig);
    }
    else if($fdname_orig == '' && $hasSubContent){
        $out .= "update subdir failed. 201811161902.";
    }
    else{
        $out .= "same dirname $fdname. 201811162001.";
    }
}
else if($args['triggertype'] == 'deletecheck'){
	# move into extra/fddelete.php
	$out = "trigger to deletecheck";
}
else{
	$out .= "unknown triggertype:[".$args['triggertype']."]";
	debug($out);
}

#$out .= serialize($_REQUEST);
# or
#$data['respobj'] = array('output'=>'content');

/*
# module path
$module_path = '';
include_once($appdir."/comm/modulepath.inc.php");

# without html header and/or html footer
$isoput = false;

require("../comm/footer.inc.php");
*/

# travel all subdir
function updateSubDir($myObj, $xid, $dirname, $dirname_old){
    $rtn = '';
    global $_CONFIG;

    $hm = $myObj->execBy('select * from '.$_CONFIG['tblpre'].'filedirtbl where parentid='.$xid.' limit 9999', ''); # why cannot limit 9999?
    if($hm[0]){
        $hm = $hm[1];
        #debug(" read hm:".serialize($hm));
        foreach($hm as $tmpk=>$tmpv){
            #$rtn .= ' readdata:'.serialize($tmpv)."<br/>";
            $tmpxid = $tmpv['id'];
            $parentname = $tmpv['parentname'];
            $pparentname = $tmpv['pparentname'];
            #debug(" $parentname , $pparentname will be update with $dirname for $dirname_old xid:$tmpxid\n ");
            # replace dir name
            if(inString($dirname_old, $parentname)){
                $parentname = str_replace($dirname_old, $dirname, $parentname);
            }
            if(inString($dirname_old, $pparentname)){
                $pparentname = str_replace($dirname_old, $dirname, $pparentname);
            }
            debug(" $parentname , $pparentname have been updated with $dirname for $dirname_old xid:$tmpxid\n ");
            # update sql
            $updtsql = "update ".$_CONFIG['tblpre']."filedirtbl set parentname='$parentname', pparentname='$pparentname' where id=$tmpxid limit 1";
            $updthm = $myObj->execBy($updtsql, '');
            if($updthm[0]){
                $rtn .= "sub id $tmpxid updt succ."; # [$updtsql]";
            }
            else{
                $rtn .= "sub id $tmpxid updt fail."; # [$updtsql]";
            }
            # check sub dir as follow
            if($tmpxid > 0){
                updateSubDir($myObj, $tmpxid, $dirname, $dirname_old);
            }
        }
    }
    else{
        #debug(" extra/fdname: read failed. hm:".serialize($hm));
    }
    
    return $rtn;
}

?>
