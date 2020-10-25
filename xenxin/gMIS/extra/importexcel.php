<?php
# import data from external excel file with .xlsx 
# tpl by wadelau@ufqi.com on Sun Jan 31 10:22:15 CST 2016
# remedy by xenxin@ufqi.com, Wed Jul 31 16:55:07 HKT 2019
#

require("../comm/header.inc.php");
require("../class/SimpleXLSX.php");

$gtbl = new GTbl($tbl, array(), $elementsep);

include("../comm/tblconf.php");

$myExtraTitle = "导入外部 Excel / SpreadSheet 数据";

# main actions
$act = Wht::get($_REQUEST, "act");
if($act == ''){ $act = 'import'; }

# check writeable
include("../act/tblcheck.php");
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
if(startsWith($act, 'import') && $hasDisableW && !$isAddByCopy){
    $out .= "Access to writing denied. 访问被拒绝. 201811111014.";
    $out .= "<br/><br/><a href=\"javascript:switchArea('contentarea_outer','off');\">关闭 并尝试其他操作.</a>";

}
else{

    # writeable okay...
    $out = ""; # clear bfr content
if($act == 'import'){
    $dataFieldStr = "<div id='rawdatadiv' style='text-align:center;'>".
        "<table name='datafields' name='datafields' style='border-spacing:0px;margin:0 auto;'>";
    $dataRow = "<tr>"; $firstRow = "<tr>"; $secRow = "<tr>"; $columni = 1; 
    $dataFields = array(); $dataSelect = array();
    for($hmi=$min_idx; $hmi<=$max_idx;$hmi++){
        $field = $gtbl->getField($hmi);
        $fieldinputtype = $gtbl->getInputType($field);
        $fieldExtraInput = $gtbl->getExtraInput($field, null);
        if($field == null || $field == ''){
            continue;
        }
        else if($fieldinputtype == 'hidden'){
            continue;
        }
        else if($gtbl->filterHiddenField($field, $opfield, $timefield)){
            continue;
        }
        else if(!$user->canWrite($field)){
            continue;
        }
        else if($fieldinputtype == 'file'){
            continue; #?
        }
        else if($fieldExtraInput != '' && inString('linktbl',$fieldExtraInput)){
            continue; #? allow xdirectory....
        }
        $dataRow .= "<td style='border:1px solid black;color:blue;'>".$gtbl->getCHN($field)."</td>";
        $firstRow .= "<td style='border:0px solid black;text-align:center;'>".($columni++)."</td>";
        $secRow .= "<td style='border:1px solid black;'>&nbsp;</td>";
        $dataFields[] = $field;
        # chk select options
        if($fieldinputtype == 'select'){
            $dataSelect[$field] = $gtbl->getSelectOption($field, null);
        }
    }
    # figure out select
    $dataSelect2 = array();
    foreach($dataSelect as $k=>$v){
        if(preg_match_all("/ value=\"([^\"]*)\">([^\-|\(]+)[\(|\-]*/", $v, $matchArr)){
            #print_r($matchArr);
            foreach($matchArr[0] as $k2=>$v2){
                #debug($matchArr[1][$k2]." : ".$matchArr[2][$k2]);
                #$dataSelect2[$k][$matchArr[2][$k2]] = $matchArr[1][$k2];
                $dataSelect2[$k][$matchArr[1][$k2]] = $matchArr[2][$k2];
            }
        }
    }
    #debug("extra/importexcel: dataSelect2:".serialize($dataSelect2));
    $dataSelect = $dataSelect2;
    # figure out select, end
    # retrieve example data
    $exampleRow = '<tr>';
    $hmResult = $gtbl->execBy("select ".implode(',', $dataFields)." from $tbl where 1=1 order by rand() limit 1", '', null);
    if($hmResult[0]){
        $hmResult = $hmResult[1][0];
        foreach($hmResult as $k=>$v){
            if(array_key_exists($k, $dataSelect)){
                $v = $dataSelect[$k][$v];
            }
            $exampleRow .= "<td style='border:1px solid black;'>$v</td>";
        }
    }
    $dataFieldStr .= "$firstRow</tr>$dataRow</tr>$exampleRow</tr>$secRow</tr></table></div>";

    $out .= "<fieldset><legend>$myExtraTitle: 步骤1</legend>
        <form id='addstepform' name='addstepform' action='extra/importexcel.php?sid=$sid&act=doimportpreview&tbl=$tbl&db=$db' method='post'
            enctype='multipart/form-data'>";
    
    $out .= "<p style='text-align:center;'>当前可接受的外部数据表格式表头及样例:<br/>$dataFieldStr</p>";
    $out .= "<p><strong>下载<button type='button' name='dnldexampledata' onclick=\"javascript:doActionEx('extra/importexcel.php?sid=$sid&act=doimportdownload&tbl=$tbl&db=$db', 'example_download_frame');\" title='下载数据格式样表'>数据样表</button> "
        ."  --→ 另存为 .xlsx  --→ 整理准备数据 --→ 上传数据来源文件</strong></p>";
    $out .= "<div id='dnlddiv' style='display:none'><iframe id='example_download_frame' name='example_download_frame' width='0' height='0'></iframe></div>";

    $out .= "<p>选择数据来源 Excel / SpreadSheet 文件( .xlsx):<br/><input name='myexcelfile' id='myexcelfile' type='file'/></p>";
    $out .= "<p><strong>注意</strong>:"
        ."<br/><strong>批量导入外部数据便捷、高效</strong>, "
        ."<br/>同时, 批量导入外部数据流程也比较复杂, "
        ."<br/>解析过程受到文件格式、数据格式、字符编码、浏览器和操作系统设置等多重因素影响; "
        ."<br/>写入过程也涉及到选择项翻译、数据格式化、数据校验和数据表本身的约束与限制。"
        ."<br/><br/>这些过程可能存在格式识别或写入异常, <span style='color:blue;'>请 在数据导入后立即进行数据审核</span>."
        ."<br/>个别没有显示在样例表中的数据项表示无法通过批量导入程序处理, "
        ."<br/>&nbsp;&nbsp;或者可以通过导入程序自动生成，无需手工录入."
        ."<br/><br/>待上传的数据表需删除掉上述样例数据，仅保留表头及其顺序即可(<span style='color:blue;'>蓝色</span>, 第二行)."
        ."<br/>待导入外部数据表文件格式为 Microsoft Office Excel Open XML Format, 默认扩展名为 .xlsx "
        ."</p>";

    $out .= "<p><hr/></p><p><input type='submit' value='保存 & 下一步' id='addmultistepsubmit' onclick=\"javascript:doActionEx(this.form.name,'contentarea');\"/></p>";
    $out .= "</form></fieldset>";
}
else if($act == 'doimportpreview'){
    $dataFile = ""; $field = "myexcelfile"; $isSucc = true;
    # file upload
    $filearr = array();
    $disableFileExtArr = array('html','php','js','jsp','pl','shtml', 'sh', 'c', 'cpp', 'py');
    if($_FILES[$field]['name'] != ''){
        # safety check 
        $tmpFileNameArr = explode(".",strtolower($_FILES[$field]['name']));
        $tmpfileext = end($tmpFileNameArr);
        if(in_array($tmpfileext, $disableFileExtArr)){
            debug("found illegal upload file:[".$_FILES[$field]['name']."]");
            $out .= "File:[".$_FILES[$field]['name']."] is not allowed. 201210241927";
            $isAllow = false; $isSucc = false;
        }
        else{
            # allowed
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
        $filename = date("dHi")."_".substr($filename, -$fileNameLength).".".$tmpfileext;
        #print __FILE__.": filename:[$filename]";
        if(move_uploaded_file($_FILES[$field]['tmp_name'], $appdir."/".$filedir."/".$filename)){
            $out .= "File:[$filedir/$filename] succ.";
            $dataFile = $filedir."/".$filename;
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
            $out .= " File:[$filename] fail for $tmpErrMsg. 201202251535.";
            $isSucc = false;
        }
        } # allowed end
    }
    else{
        $out .= "File is empty / 数据文件为空, 请 返回重试. 201202251016.";
        $isSucc = false;
    }
    # file upload end
    # data parse
    if($dataFile != ''){
        $xlsx = new SimpleXLSX();
        $xlsx->debug = true; // display errors
        $xlsx->skipEmptyRows = true; // skip empty rows
        $result = $xlsx->parseFile($appdir."/".$dataFile);
        $maxRowPreview = 5; $rowCount = 0;
        if($result){
            $rows = $xlsx->rows(); $totalCount = count($rows);
            #debug($rows);
            $out .= "<p><strong>待导入数据预览</strong><hr/></p><table id='rawdata' name='rawdata' ".
                " style='border-spacing:0px;margin:0 auto;'>";
            foreach($rows as $k=>$row){
                $out .= "<tr>";
                foreach($row as $ck=>$col){
                    $out .= "<td style='border:1px solid black;'>$col</td>";
                }
                $out .= "</tr>";
                if($rowCount++ > $maxRowPreview){
                    $out .= "<tr><td colspan='6'><br/>....<br/><strong>共计 $totalCount 行, 余下还有 "
                        .($totalCount-$maxRowPreview)." 行</strong></td></tr>";
                    break;
                }
            }
            if($rowCount < $maxRowPreview){
                $out .= "<tr><td colspan='6'><br/><strong>共计 $totalCount 行</strong></td></tr>";
            }
            $out .= "</table><p><hr/></p>";
        }
        else{
            $out .= "Parse file error. 201202251021.";
             $isSucc = false;
        }
    }
    else{
        # no file.
    }
    # data parse end
    # ready to resp
    $rawOut = $out;
    $out = "<fieldset><legend>$myExtraTitle: 步骤2</legend>
        <form id='addstepform' name='addstepform' action='extra/importexcel.php?sid=$sid&act=doimportsave&tbl=$tbl&db=$db' method='post'
            enctype='multipart/form-data'>";
    if($isSucc){
        # next step
        $out .= "<p>$rawOut</p>";
        $out .= "<p><input type='hidden' id='datafile' name='datafile' value='".$dataFile."'/></p>";
        $out .= "<p>Data file read succ.... / 数据文件读取成功!</p>";
    }
    else{
        # back
        $out .= "<p>$rawOut</p>";
        $out .= "<p style='color:red;'>Data file read fail.... / 数据文件读取失败! 请 返回重试... </p>";
        $out .= "<p><a href='#' onclick='javascript:GTAj.backGTAjax('contentarea', 1);'><< Back / 返回</a></p>";
    }
    $out .= "<p><hr/></p>";
    $out .= "<p><input type='button' value='返回 & 上一步' id='addmultistepback' onclick=\"javascript:GTAj.backGTAjax('contentarea', 1);\"/>";
    $out .= "&nbsp; &nbsp; <input type='submit' value='保存 & 下一步' id='addmultistepsubmit' onclick=\"javascript:doActionEx(this.form.name,'contentarea');\"/></p>";
    $out .= "</form></fieldset>";
}
else if($act == 'doimportsave'){
    $isSucc = true;
    $dataFile = Wht::get($_REQUEST, 'datafile');
    if($dataFile != ''){
        if(file_exists($appdir."/".$dataFile)){
            # collect fields
            $dataFields = array(); $dataSelect = array();
            $opFieldStr = ''; $timeFieldStr = '';
        $dataRow = "<tr>"; $firstRow = "<tr>"; $secRow = "<tr>"; $columni = 1; 
        for($hmi=$min_idx; $hmi<=$max_idx;$hmi++){
            $field = $gtbl->getField($hmi);
            $fieldinputtype = $gtbl->getInputType($field);
            $fieldExtraInput = $gtbl->getExtraInput($field, null);
            if($field == null || $field == ''){
                continue;
            }
            else if($fieldinputtype == 'hidden'){
                continue;
            }
            else if($gtbl->filterHiddenField($field, $opfield, $timefield)){
                if(in_array($field, $opfield)){
                    $opFieldStr = "$field='$userid'";
                }
                else if(in_array($field, $timefield)){
                    $timeFieldStr = "$field='".date("Y-m-d H:i:s", time())."'";
                }
                continue;
            }
            else if(!$user->canWrite($field)){
                continue;
            }
            else if($fieldinputtype == 'file'){
                continue; #?
            }
            else if($fieldExtraInput != '' && inString('linktbl',$fieldExtraInput)){
                continue; #?
            }
            $dataFields[] = $field;
            # chk select options
            if($fieldinputtype == 'select'){
                $dataSelect[$field] = $gtbl->getSelectOption($field, null);
            }
        }
        #debug("extra/importexcel: dataSelect:".serialize($dataSelect));
        $dataSelect2 = array();
        foreach($dataSelect as $k=>$v){
            if(preg_match_all("/ value=\"([^\"]*)\">([^\-|\(]+)[\(|\-]*/", $v, $matchArr)){
                #print_r($matchArr);
                foreach($matchArr[0] as $k2=>$v2){
                    #debug($matchArr[1][$k2]." : ".$matchArr[2][$k2]);
                    $dataSelect2[$k][$matchArr[2][$k2]] = $matchArr[1][$k2];
                }
            }
        }
        #debug("extra/importexcel: dataSelect2:".serialize($dataSelect2));
        $dataSelect = $dataSelect2;
            # parse data
        $xlsx = new SimpleXLSX();
        $xlsx->debug = true; // display errors
        $xlsx->skipEmptyRows = true; // skip empty rows
        $result = $xlsx->parseFile($appdir."/".$dataFile);
        if($result){
            $rows = $xlsx->rows(); $totalRows = count($rows);
            $sql = ""; $field1Name = $gtbl->getCHN($dataFields[0]); $field2Name = $gtbl->getCHN($dataFields[1]);
            $succCount = 0; $failCount = 0; $failArr = array();
            foreach($rows as $k=>$row){
                if($row[0]==='' && $row[1]==='' && $row[2]===''){
                    debug("extra/importexcel: found definition row, 3rd... skip....".serialize($row));
                    continue;
                }
                else if($row[0]=='1' && $row[1]=='2'){
                    debug("extra/importexcel: found definition row... skip....".serialize($row));
                    continue;
                }
                else if($row[0]==$field1Name && $row[1]==$field2Name){
                    debug("extra/importexcel: found definition row, 2nd... skip....".serialize($row));
                    continue;
                }
                $sql = "insert into $tbl set ";
                foreach($row as $ck=>$col){
                    $field = $dataFields[$ck];
                    if($field == ''){
                        debug("extra/importexcel: found illegal field, skip.... ck:$ck");
                        continue;
                    }
	                if(in_array($field, $timefield)){
                        $col = date("Y-m-d H:i:s", time()); 
                    }
                    if(array_key_exists($field, $dataSelect)){
                        $col = $dataSelect[$field][$col];
                        #debug("extra/importexcel: found col:".$field." in selelct and convert....");
                    }
                    if($col == '' && $col != '0'){
                        if($gtbl->isNumeric($hmfield[$field]) == 1){
                            $col = 0;
                            $out .= "<br/><span style='color:red;'>".$gtbl->getCHN($field)."/$field 空值异常</span>, 发生在 [ "
                                .implode(', ', $row)." ] , 已按默认值处理, 行: ".($k+1);
                        }
                    }
                    $sql .= $field."='".addslashes($col)."',";
                }
                if($opFieldStr != ''){
                    $sql .= $opFieldStr.','; 
                }
                if($timeFieldStr != ''){
                    $sql .= $timeFieldStr.','; 
                }
                $sql = substr($sql, 0, strlen($sql)-1); # rm ,
                debug($sql);
                $dbResult = $gtbl->execBy($sql, null, null);
                debug($dbResult);
                if($dbResult[0]){
                    $succCount++;
                }
                else{
                    $errStr = '<br/> <span style="color:red;">异常信息: '.($servResp=htmlentities(serialize($dbResult)));
                    if(inString('Duplicate entry', $servResp)){
                        $errStr .= " / 重复或冲突数据 ";
                    }
                    else{
                        $errStr .= " / 其他写入错误 ";
                    }
                    $errStr .= "</span>, 行: ".($k+1);
                    $row[] = $errStr; 

                    $failArr[] = $row; $failCount++;
                }
            }
            #$out .= "<p>$k: $sql</p>";
            $out .= "<p><strong>数据总条数: $totalRows , 有效数据: ".($totalCount=$succCount+$failCount)
                    ." , 其中成功导入 $succCount , 失败条数: ".($failCount)." </strong>.";
            if($failCount > 0){
                $out .= "<br/><br/>导入失败数据如下:<br/>";
                foreach($failArr as $k=>$v){
                    $out .= "<br/>".implode(', ', $v);
                }
                $out .= "<br/><br/>请 修改调整后重试.";
            }
            else{
                $out .= "<br/><br/>请 立即 <a href=\"#\" onclick=\"javascript:window.location.reload();\">刷新列表页</a> 进行数据审核.";
                # @todo rm dataFile ?
            }
            $out .= "</p>";
        }
        else{
            $out .= "Parse file:[$dataFile] error. 201202251021.";
            $isSucc = false;
        } 
            # parse data end
        }
        else{
            $isSucc = false;
            $out .= "Data file:[$dataFile] not exists. 201202251146.";
        }
    }
    else{
        $isSucc = false;
        $out .= "Data file is empty. 201202251147.";
    }
    # ready to resp
    if(true){
        $rawOut = $out;
        $out = "<fieldset><legend>$myExtraTitle: 步骤3</legend>
        <form id='addstepform' name='addstepform' action='../extra/importexcel.php?sid=$sid&act=doimportsave&tbl=$tbl&db=$db' method='post'
            enctype='multipart/form-data'>";
        if($isSucc){ 
            $out .= "<p><strong>外部数据导入结果</strong></p><hr/>".$rawOut;
        }
        else{
            $out .= "<p style='color:red;'> 数据导入保存失败, 请 返回重试... </p>";
        }
        $out .= "<p><hr/></p>";
        $out .= "<p><input type='button' value='继续导入' id='addmultistepback' onclick=\"javascript:doActionEx('extra/importexcel.php?tbl=$tbl&sid=$sid', 'contentarea');\"/>";
        $out .= "&nbsp; &nbsp; <input type='button' value='关闭' id='addmultistepclose' onclick=\"javascript:switchArea('contentarea_outer','off');\"/></p>";
        $out .= "</form></fieldset>";
    }
    # ready to resp, end
    
}
else if($act == 'doimportdownload'){
    $dataFieldStr = "";
    $dataRow = ""; $firstRow = ""; $secRow = ""; $columni = 1; 
    $dataFields = array(); $dataSelect = array();
    for($hmi=$min_idx; $hmi<=$max_idx;$hmi++){
        $field = $gtbl->getField($hmi);
        $fieldinputtype = $gtbl->getInputType($field);
        $fieldExtraInput = $gtbl->getExtraInput($field, null);
        if($field == null || $field == ''){
            continue;
        }
        else if($fieldinputtype == 'hidden'){
            continue;
        }
        else if($gtbl->filterHiddenField($field, $opfield, $timefield)){
            continue;
        }
        else if(!$user->canWrite($field)){
            continue;
        }
        else if($fieldinputtype == 'file'){
            continue; #?
        }
        else if($fieldExtraInput != '' && inString('linktbl',$fieldExtraInput)){
            continue; #? allow xdirectory....
        }
        $dataRow .= $gtbl->getCHN($field).",";
        $firstRow .= ($columni++).",";
        $secRow .= ",";
        $dataFields[] = $field;
        # chk select options
        if($fieldinputtype == 'select'){
            $dataSelect[$field] = $gtbl->getSelectOption($field, null);
        }
    }
    # figure out select
    $dataSelect2 = array();
    foreach($dataSelect as $k=>$v){
        if(preg_match_all("/ value=\"([^\"]*)\">([^\-|\(]+)[\(|\-]*/", $v, $matchArr)){
            #print_r($matchArr);
            foreach($matchArr[0] as $k2=>$v2){
                #debug($matchArr[1][$k2]." : ".$matchArr[2][$k2]);
                #$dataSelect2[$k][$matchArr[2][$k2]] = $matchArr[1][$k2];
                $dataSelect2[$k][$matchArr[1][$k2]] = $matchArr[2][$k2];
            }
        }
    }
    $dataSelect = $dataSelect2;
    # figure out select, end
    # retrieve example data
    $exampleRow = '';
    $hmResult = $gtbl->execBy("select ".implode(',', $dataFields)." from $tbl where 1=1 order by rand() limit 1", '', null);
    if($hmResult[0]){
        $hmResult = $hmResult[1][0];
        foreach($hmResult as $k=>$v){
            if(array_key_exists($k, $dataSelect)){
                $v = $dataSelect[$k][$v];
            }
            $exampleRow .= "$v,";
        }
    }
    $dataFieldStr .= "$firstRow\n$dataRow\n$exampleRow\n$secRow\n";
    $isSucc = true; 
    # prepare file
    $dnld_dir = $appdir."/dnld";
    $dnld_file = "exampledata_".str_replace("gmis_","",$tbl)."_".date("Y-m-d-H-i").".csv";
    $myfp = fopen($dnld_dir.'/'.$dnld_file, 'wb');
    if($myfp){
        fwrite($myfp, chr(0xEF).chr(0xBB).chr(0xBF));
		fwrite($myfp, $dataFieldStr);
    }
    else{
        debug("extra/importexcel: example data file:[$dnld_file] download failed. 201202251827."); 
        $isSucc = false;
    }
    fclose($myfp);
    # prepare file, end
    if($isSucc){
        $out .= "File:$dnld_file succ.<br/><script type=\"text/javascript\">";
        $out .= "parent.window.open('".$rtvdir."/dnld/".$dnld_file."','Excel File Download','scrollbars,toolbar,location=0,status=yes,resizable,width=600,height=400');";
        $out .= "</script>";
    }
    else{
        $out .= "File:$dnld_file fail. <script type=\"text/javascript\">";
        $out .= "window.alert('样表数据准备未成功, 请稍后重试.');";
        $out .= "</script>";
    }
    $out .= "<!-- SUCC, OK, OKAY -->";
}

} # end of writeable

$out .= '<!-- my output timestamp: '.($myServTime=date("Y-m-d H:i:s", time())).' -->';
# or
$data['respobj'] = array('output-timestamp'=>$myServTime);

# module path
$module_path = '';
include_once($appdir."/comm/modulepath.inc.php");

# without html header and/or html footer
$isoput = false;

require("../comm/footer.inc.php");

?>
