<?php

# do some action defined in table::check tag in xxxx.xml
$checkactions = $gtbl->getTblCHK();
if(count($checkactions) > 0){
    foreach($checkactions as $chkact=>$do){
        if($chkact == $act){
            #$out .= __FILE__.": found preset checkaction:[".$do."]\n";
            include($appdir."/".$do);
        }
    }
}

# manage mode check
$mode = $gtbl->getMode();
$accMode = $mode;
if($mode != ''){
    $act2mode = array('add'=>'w',
            'addbycopy' => 'w',
            'list-addform'=>'w',
            'modify'=>'w',
            'import'=>'w',
            'updatefield'=>'w',
            'list'=>'r',
            'list-toexcel'=>'r',
            'view'=>'r',
            'list-dodelete'=>'d',
			'print' => 'r',
            'deepsearch' => 'r',
            'dodeepsearch' => 'r',
            'pivot' => 'r',
            'pivot-do' => 'r',
			'pickup' => 'r',
            );
    $modechar = $act2mode[$act];
    if(!isset($modechar)){
        error_log(__FILE__.": unknown act:[$act] in act2mode.201202282117");
    }
	else{
		if($mode == 'o-w'){
			# @todo
		}
        else if(strpos($mode, $modechar) === false){
            $out = "<p>访问被拒绝. <br/>act:[$act] is not allowed in mode:[$mode]. 201202282143\n";
            $out .= "<br/><br/> 联系上级或技术支持<a href='mailto:".$_CONFIG['adminmail']."?subject="
                    .$_CONFIG['agentname']."权限申请访问$tbl@$db'> 申请变更 </a> <br/><br/></p>";
            if($fmt == ''){
                #
            }
            else if($fmt == 'json'){
                $data = array();
                $data['out'] = $out;
                $data['targetid'] = $id;
                $out = json_encode($data);
            }
            else{
                debug($fmt, "unknown fmt:$fmt");
            }   
            #debug("act/tblcheck: out:$out");
            print $out;
            exit(0);
        }
		else{
            #$out .= "act:[$act] is ready.\n";
        }
    }
}

?>
