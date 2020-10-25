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
    # move into extra/fdrename
    $out = ' trigger to fdrename...'; # 'fdrename: args:['.serialize($args).']';
    
}
else if($args['triggertype'] == 'deletecheck'){
    #$out = "trigger to deletecheck....";
    #debug("trigger to deletecheck.... deleteresult:[$doDeleteResult]  hmorig:".serialize($hmorig));
    if(inString('dodelete', $act) && isset($hmorig) && $hmorig['itype'] == 1){ # dir deletion only
        $lastId = $hmorig['id'];
        $hm = $gtbl->getBy('id', "parentid=$lastId");
        if($hm[0]){
            $out .= "id:$lastId has sub contents. deletion will be refused.";
            $sql = "replace into ".$gtbl->getTbl()." set  ";
            foreach($hmorig as $k=>$v){
                if(!startsWith($k, 'pnsk') && !startsWith($k, 'oppnsk')){
                    $sql .= "$k='$v', ";
                }
            }
            $sql = substr($sql, 0, strlen($sql)-2); # rm ", "
            #$out .= " undo sql:[$sql]";
            $hm2 = $gtbl->execBy($sql, null);
            if($hm2[0]){
                $out .= " undo needed and succ.";
                $doDeleteResult = false;
                $deleteErrCode = '201811241145';
            }
            else{
                $out .= " undo failed.";
            }
        }
        else{
            $out .= "id:$lastId is empty and deletion succ."; 
        }
    }
    else{
        $out .= "act:$act not deletion or itype:".$hmorig['itype']." not dir, deletecheck skip....";
    }
    #debug('out:'.$out);
}
else{
    $out .= "unknown triggertype:[".$args['triggertype']."]";
    debug("unknown triggertype:[".$args['triggertype']."]");
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

?>
