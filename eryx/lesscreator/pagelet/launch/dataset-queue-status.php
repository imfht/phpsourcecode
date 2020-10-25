<?php

use LessPHP\LessKeeper\Keeper;

$kpr = new Keeper();

$rsp = array(
    'status'  => 500,
    'message' => $this->T('Internal Server Error'),
    'data'    => array(),
);

try {

    if (!isset($this->req->jobid)) {
        throw new \Exception(sprintf($this->T('`%s` can not be null'), 'jobid'));
    }

    $rs = $kpr->NodeGet("/db/qistatus/{$this->req->jobid}");
    if ($rs->type == Keeper::ReplyError) {
        throw new \Exception(sprintf($this->T('`%s` can not found'), 'jobid'));
    }
    $rs = json_decode($rs->body, false);
    if (!isset($rs->jobid)) {
        throw new \Exception(sprintf($this->T('`%s` can not found'), 'jobid'));
    }
    if (!isset($rs->process_percent)) {
        $rs->process_percent = 1;
    }
    
    $rsp['data']   = $rs;
    $rsp['status'] = 200;
    $rsp['message'] = "";

} catch (\Exception $e) {
    
    $rsp['status']  = 500;
    $rsp['message'] = $e->getMessage();
}

die(json_encode($rsp));
