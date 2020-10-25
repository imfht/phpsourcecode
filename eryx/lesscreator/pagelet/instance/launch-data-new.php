<?php
$ret = array('Status' => "Error");

if (strlen($this->req->instanceid) < 1
    || strlen($this->req->data) < 10) {
    die(json_encode($ret));
}
$projInstId = $this->req->instanceid;
list($datasetid, $tableid) = explode("_", $this->req->data);


$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();
$projInst = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$projInstId}/info");
$projInst = json_decode($projInst->body, true);
if (!isset($projInst['ProjId'])) {
    die(json_encode($ret));
}

$fsd = $projPath."/data/{$datasetid}.ds.json";
$rs = lesscreator_fs::FsFileGet($fsd);
if ($rs->status != 200) {
    die(json_encode($ret));
}
$dataInfo = json_decode($rs->data->body, true);
if ($projInfo['projid'] != $dataInfo['projid']) {
    die(json_encode($ret));
}

$fst = $projPath."/data/{$datasetid}.{$tableid}.tbl.json";
$rs = lesscreator_fs::FsFileGet($fst);
if ($rs->status != 200) {
    die(json_encode($ret));
}
$tableInfo = json_decode($rs->data->body, true);

$dataInst = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$projInstId}/data/{$tableid}");
$dataInst = json_decode($dataInst->body, true);

if (!isset($dataInst['DataInst'])) {
    $dataInst['DataInst'] = LessPHP_Util_String::rand(8, 2);
}
if (!isset($dataInst['Created'])) {
    $dataInst['Created'] = time();
}

$dataInst['ProjId']    = $projInfo['projid'];
$dataInst['DataSetId'] = $datasetid;
$dataInst['DataType']  = $dataInfo['type'];
$dataInst['TableId']   = $tableid;
$dataInst['Updated']   = time();
$dataInst['TableInfo'] = $tableInfo;
$dataInst['User']      = 'guest';

$kpr->NodeSet("/app/u/guest/{$projInfo['projid']}/{$projInstId}/data/{$tableid}", json_encode($dataInst));
$kpr->NodeSet("/h5db/actor/setup/{$dataInst['DataInst']}.{$tableid}", json_encode($dataInst));

$ret['Status'] = "OK";
$ret['DataInst'] = $dataInst['DataInst'];
die(json_encode($ret));
