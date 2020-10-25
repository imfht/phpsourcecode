<?php

$ret = array('Status' => "Error");

if (strlen($this->req->instanceid) < 1) {
    die(json_encode($ret));
}
if (strlen($this->req->flowgrpid) < 1) {
    die(json_encode($ret));
}
if (strlen($this->req->flowactorid) < 1) {
    die(json_encode($ret));
}

$insid = $this->req->instanceid;
$grpid = $this->req->flowgrpid;
$actorid = $this->req->flowactorid;

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

$fsg = $projPath."/dataflow/{$grpid}.grp.json";
$rs = h5ceator_fs::FsFileGet($fsg);
if ($rs->status != 200) {
    die(json_encode($ret));
}

$fsa = $projPath."/dataflow/{$grpid}/{$actorid}.actor.json";
$rs = h5ceator_fs::FsFileGet($fsa);
if ($rs->status != 200) {
    die(json_encode($ret));
}
$actorInfo = json_decode($rs->data->body, true);
if ($actorInfo['para_mode'] != lesscreator_service::ParaModeServer) {
    die(json_encode($ret));
}

$fss = $projPath."/dataflow/{$grpid}/{$actorid}.actor";
$fss = h5ceator_fs::FsFileGet($fss);
if ($fss->status != 200) {
    die(json_encode($ret));
}

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();

$actorInst = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$insid}/flow/{$actorid}");
$actorInst = json_decode($actorInst->body, true);
$actorInst['ActorId']    = $actorid;
$actorInst['ParaHost']   = $this->req->hosts;
$actorInst['ProjInst']   = $insid;
$actorInst['User']       = 'guest';

$instInfo = array(
    'ProjId'    => $projInfo['projid'],
    'ProjInst'  => $insid,
    'GrpId'     => $grpid,
    'ActorId'   => $actorid,
    'Func'      => '10',
    'ParaHost'  => $this->req->hosts,
    'Info'      => $actorInfo,
);
/* if (isset($this->req->hosts) && strlen($this->req->hosts) > 7) {
    $actorIns['ParaHost'] = $this->req->hosts;
    $set['ParaHost'] = $this->req->hosts;
} */

$kpr->NodeSet("/app/u/guest/{$projInfo['projid']}/{$insid}/flow/{$actorid}", json_encode($actorInst));

$kpr->NodeSet("/h5flow/script/{$insid}/{$actorid}", $fss->data->body);
$kpr->NodeSet("/h5flow/ctrlq/{$insid}.{$actorid}", json_encode($instInfo));

$ret['Status'] = 'OK';

die(json_encode($ret));
