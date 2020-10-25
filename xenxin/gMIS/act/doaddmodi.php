<?php
# save data from act=add|modify

$fieldlist = array();
$fieldvlist = array(); # remedy for overrided by $obj->get during adding, need a tmp container for query string, Thu Jun 11 22:15:32 CST 2015
$filearr = array();
$disableFileExtArr = array('html','php','js','jsp','pl','shtml', 'sh', 'c', 'cpp', 'py');

/*
 * security double check for end-user's input
 * found illegal and empty it
 * Xenxin@ufqi
 * 14:05 12/9/2019
 @param $rawInput, fk, fv
 @return $safeOutput
 */
function securityFileCheck4Tmp($fv){
	$rtn = $fv;
	$rtn = securityFileCheck($rtn);
	$rtn = realpath($rtn);
	$tmpUploadDir = get_cfg_var('upload_tmp_dir');
	$tmpUploadDir = $tmpUploadDir=='' ? '/tmp' : $tmpUploadDir;
	if(strpos($rtn, $tmpUploadDir) !== 0){
		$rtn = '';
	}
	if($rtn != '' && !file_exists($rtn)){
		$rtn = '';
	}
	if($rtn != '' && !is_uploaded_file($rtn)){
		$rtn = '';
	}
	debug('act/doaddmodi: fk4tmp-x:'.$fv.'->'.$rtn);
	return $rtn;
}

//- securityFileCheck , 
//- Xenxin@ufqi
// 11:17 Thursday, December 19, 2019
function securityFileCheck($fv){
	$rtn = $fv;
	//$rtn = realpath($rtn);
	$badChars = array(';', '%3B', ' ', "%20", '&', "%26", "..", "//", './', "\\", '\.');
	$rtn = str_replace($badChars, '', $rtn);
	if(!preg_match('/^(?:[a-z0-9_\-\/#~]|\.(?!\.))+$/iD', $rtn)){
		$rtn = '';
	}
	if(strpos($rtn, '/') === 0 && strpos($rtn, '/www') !== 0 
		&& strpos($rtn, '/var') !== 0
		&& strpos($rtn, '/tmp') !== 0
		&& strpos($rtn, '/srv') !== 0){
		$rtn = '';
	}
	debug('act/doaddmodi: fk-x:'.$fv.'->'.$rtn);
	return $rtn;
}

//- real processing..

if($id != ''){
    $gtbl->setId($id); # speical field
}

for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
    $field = $gtbl->getField($hmi);
    $fieldv = ''; # remedy by wadelau@ufqi.com, Wed Oct 17 12:46:16 CST 2012
	$fieldInputType = $gtbl->getInputType($field);
    if($field == null | $field == '' 
            || $field == $gtbl->getMyId()){
        continue;

    }else if(!$user->canWrite($field)){
        $out .= "$field cannot be written.\n";
        continue;

    }else if(in_array($field, $opfield)){
        $fieldv = $userid;
        $fieldlist[] = $field;
        #$gtbl->set($field, $fieldv);
		$fieldvlist[$field] = $fieldv;
    }
	else if(in_array($field,$timefield)){
		$fieldv = '';
        if($gtbl->getId() == ''){
            # insert
            if(inList($field, 'inserttime,insertime,insertd,created,starttime,dinserttime')){ # see comm/tblconf.php
                $fieldv = date("Y-m-d H:i:s", time()); # 'NOW()';
                $fieldlist[] = $field;
            }
        }
        else{
            # update
            if(inList($field, 'updatetime,endtime,editime,edittime,modifytime,updated,dupdatetime')){
                $fieldv = date("Y-m-d H:i:s", time()); # 'NOW()';
                $fieldlist[] = $field;
            }
        }
        if($fieldv != ''){
            $fieldvlist[$field] = $fieldv;
        }
        else{
            debug(__FILE__.": unclassified timefield:[$field]. 1611101112.");
			#$fieldv = date("Y-m-d H:i:s", time()); # 'NOW()';
            #$fieldlist[] = $field;
			# remedy 19:13 Wednesday, April 15, 2020
			if(isset($_REQUEST[$field])){
				$fieldv = trim(Wht::get($_REQUEST, $field));
				if($fieldv == ''){ $fieldv = date("Y-m-d H:i:s", time()); }
				$fieldlist[] = $field;
				$fieldvlist[$field] = $fieldv;
			}
        }
        continue;
    }
	#else if($field == 'password'){
    else if(inString('password', $field) || inString('pwd', $field)){
        if($_REQUEST[$field] != ''){
           $fieldv = sha1($_REQUEST[$field]); 
		   $fieldlist[] = $field; 
		   #$gtbl->set($field, $fieldv); # 2014-10-26 21:33
		   $fieldvlist[$field] = $fieldv;
        }
		else{
          continue;    
        }            
        debug("field:[$field] fieldv:[$fieldv]");
		#print(__FILE__.": field:[$field] fieldv:[$fieldv]");
    }
	else{
        if($fieldInputType == 'file'){
            $fieldv_orig = $_REQUEST[$field.'_orig'];
			$_FILES[$field]['name'] = securityFileCheck($_FILES[$field]['name']);
			$_FILES[$field]['tmp_name'] = securityFileCheck4Tmp($_FILES[$field]['tmp_name']);
            if($_FILES[$field]['name'] == '' && $fieldv_orig != ''){
				if(strpos($fieldv_orig, $shortDirName) === false){
                    $fieldv_orig = $shortDirName."/".$fieldv_orig;
                }
                $fieldv = $fieldv_orig;
            }
			else if($_FILES[$field]['name'] != ''){
                # safety check 
                $tmpFileNameArr = explode(".",strtolower($_FILES[$field]['name']));
                $tmpfileext = end($tmpFileNameArr);
                if(in_array($tmpfileext, $disableFileExtArr)){
                   debug("found illegal upload file:[".$_FILES[$field]['name']."]");
                   $out .= "file:[".$_FILES[$field]['name']."] is not allowed. 201210241927";
                   continue;
                }

                $filedir = $_CONFIG['uploaddir'];
                if($gtbl->getId() != ''){ # remove old file if necessary
                    $oldfile = $gtbl->get($field); # this might override what has been set by query string
                    if($oldfile != ""){
                    	$oldfile = str_replace($shortDirName."/","", $oldfile);
                        unlink($appdir."/".$oldfile);
                    }
					else{
                        debug("oldfile:[$oldfile] not FOUND. field:[$field]. 201810111959.");				
                    }
                }
				$filedir = $filedir."/".date("Ym"); # Fri Dec  5 14:19:05 CST 2014
				if(!file_exists($appdir."/".$filedir)){
					mkdir($appdir."/".$filedir);	
				}
				$filename = basename($_FILES[$field]['name']);
				$filename = Base62x::encode($filename);
				$fileNameLength = strlen($filename);
				$fileNameLength = $fileNameLength > 128 ? 128 : $fileNameLength; 
                $filename = date("dHis")."_".substr($filename, -$fileNameLength).".".$tmpfileext;
				#print __FILE__.": filename:[$filename]";
                if(move_uploaded_file($_FILES[$field]['tmp_name'], $appdir."/".$filedir."/".$filename)){
                    $out .= "file:[$filedir/$filename] succ.";
                }
				else{
                    // Check $_FILES['upfile']['error'] value.
                    $tmpErrMsg = '';
                    switch ($_FILES[$field]['error']) {
                        case UPLOAD_ERR_OK:
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $tmpErrMsg = ('No file sent');
                            break;
                        case UPLOAD_ERR_INI_SIZE:
                            $tmpErrMsg = ('Exceeded filesize limit '.ini_get('upload_max_filesize').' in server-side');
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            $tmpErrMsg = ('Exceeded filesize limit/'.$_REQUEST['MAX_FILE_SIZE'].' in client-side');
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $tmpErrMsg = 'Only partially uploaded';
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $tmpErrMsg = 'Stopped by other extensions';
                            break;
                        default:
                            $tmpErrMsg = ('Unknown errors ['.$_FILES[$field]['error'].']');
                    }
                    $out .= " file:[$filename] fail for $tmpErrMsg. 201202251535.";
                }
                #$fieldv = $filedir."/".$filename;
                $fieldv = $shortDirName."/".$filedir."/".$filename; 
                $filearr['filename'] = basename($_FILES[$field]['name']); # sometimes original name may be different with uploadedfile.
                $filearr['filesize'] = intval($_FILES[$field]['size']/1000 + 1); # KB
                $filearr['filetype'] = substr($_FILES[$field]['type'], 0, 64); # see filedirtbl definition
				
				$tmpFileName = Wht::get($_REQUEST, 'filename');
				if($tmpFileName != '' && $tmpFileName != $filearr['filename']){ $filearr['filename'] = $tmpFileName.$filearr['filename']; }
				
            }
        }
		else if($gtbl->getSelectMultiple($field)){

            if(is_array($_REQUEST[$field])){ 
                $fieldv = implode(",", $_REQUEST[$field]);
            }else{
                $fieldv = $_REQUEST[$field]; 
            }
        }
		else{
            $fieldv = trim(Wht::get($_REQUEST, $field));
			if($fieldv == ''){
				$fieldv = $hmfield[$field."_default"];
				if($fieldv == ''){
					if($gtbl->isNumeric($hmfield[$field]) == 1){
						#print __FILE__.": field:[".$field."] type:[".$hmfield[$field]."] is int.";
						$fieldv = 0;
					}
				}
			}
            else{
				if(strpos($fieldv,"<") !== false){ # added by wadelau on Sun Apr 22 22:09:46 CST 2012
					if($fieldInputType == 'textarea'){	
                		# allow all html tags except these below 
                		$fieldv = str_replace("<script","&lt;script", $fieldv);
                		$fieldv = str_replace("<iframe","&lt;iframe", $fieldv);
                		$fieldv = str_replace("<embed","&lt;embed", $fieldv);
					}
					else{
                		$fieldv = str_replace("<","&lt;", $fieldv);
					}
            	}
            	if(strpos($fieldv, "\n") !== false){
                	$fieldv = str_replace("\n", "<br/>", $fieldv);
            	}
        	}
    	}
    	$fieldlist[] = $field;
    	#$gtbl->set($field, $fieldv);
    	$fieldvlist[$field] = $fieldv;
	}

	#print(__FILE__.": field:[$field] fieldv:[$fieldv]");

}

#print(__FILE__.": fieldlist:[".$gtbl->toString($fieldlist)."]");
foreach($fieldvlist as $k=>$v){
	$gtbl->set($k, $v);		
}

if(count($filearr)){
    foreach($filearr as $k=>$v){
        $gtbl->set($k, $v);
    }
}

$hm = $gtbl->setBy(implode(',', $fieldlist), null); 
#print_r($hm);
if($hm[0]){
    
    $hm = $hm[1];
    if($gtbl->getId() == ''){
        $gtbl->setId($hm['insertid']);
        $id = $gtbl->getId();
    }
	# read newly-written data, Tue Sep 27 13:28:06 CST 2016
	$hmNew = $gtbl->getBy('*', $gtbl->myId.'="'.$id.'"');
	if($hmNew[0]){
		$hmNew = $hmNew[1][0];
		#debug(__FILE__.": resultset-tag:[".$gtbl->resultset."]");
		$gtbl->set($gtbl->resultset, $hmNew);
		#debug($gtbl->get($gtbl->resultset));
	}
    # some triggers bgn, added on Fri Mar 23 21:51:12 CST 2012
    include("./act/trigger.php");
    # some triggers end, added on Fri Mar 23 21:51:12 CST 2012

    $out .= "<script> parent.sendNotice(true, '".$lang->get('notice_success')."'); parent.switchArea('contentarea_outer','off'); </script>";

}
else{
    $servResp = serialize($hm); $servResp = str_replace("'", "\'", $servResp);
    $foundErr = false;
    foreach($fieldlist as $k=>$v){
        if(inString($v, $servResp)){
            $servResp .= "<br/>".$gtbl->getCHN($v)."[$v]: ".$lang->get('notice_save_error');        
            $foundErr = true;
        }
    }
    if(!$foundErr){
        if(inString('Duplicate entry', $servResp)){
            $priuni = $gtbl->get($gtbl->PRIUNI);
            $servResp .= "<br/>".$lang->get('notice_save_error');
            foreach($priuni as $k=>$v){
                if($k == 'PRI'){ continue; } # skip id?
                foreach($v as $k2=>$v2){
                    if(isset($_REQUEST[$v2])){
                        $servResp .= "<br/>".$gtbl->getCHN($v2)."[$v2]: ".$_REQUEST[$v2]."";        
                    }
                }
            }
            $foundErr = true;
        }
    }
    $out .= "<script> if(typeof parent.sendNotice !='undefined'){ parent.sendNotice(false, '".$lang->get('notice_failure')."'); }; if(true){ var servResp=parent._g('respFromServ'); if(servResp){ servResp.innerHTML='<p style=\"color:red;\">".$lang->get('notice_failure')."<br/>".$servResp."</p>';}} </script>";
    debug("act/doaddmodi: ".$servResp);
}

$gtbl->setId('');

?>