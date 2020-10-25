<?php

use LessPHP\LessKeeper\Keeper;
use LessPHP\Net\Http;

$kpr = new Keeper();

$msg    = "";
$pkgals = array();
$pkgdbins = array();

$rsp    = array(
    'status'  => 500,
    'message' => $this->T('Internal Server Error'),
);

if (isset($this->req->jobid)) {
    $rsp['jobid'] = $this->req->jobid;
} else {
    $rsp['jobid'] = LessPHP_Util_String::rand(10, 2);
}

try {
    
    // User
    if (!$this->req->user) {
        throw new \Exception(sprintf($this->T('`%s` can not be null'), $this->T('Username')));
    }
    $user = strtolower($this->req->user);
    if (!preg_match('/^([0-9a-z]{2,20})$/', $user)) {
        throw new \Exception(sprintf($this->T('`%s` is not valid'), $this->T('Username')));
    }

    $projPath = lesscreator_proj::path($this->req->proj);
    $projInfo = lesscreator_proj::info($this->req->proj);
    if (!isset($projInfo['projid'])) {
        throw new \Exception($this->T('Bad Request'));
    }
    $projid = $projInfo['projid'];

    // Get the package's information of all
    $rs = $kpr->LocalNodeListAndGet("/lf/pkg");
    if ($rs->type != Keeper::ReplyMulti) {
        throw new \Exception($this->T('Service Unavailable'), 500);
    }
    foreach ($rs->elems as $v) {

        $pkg = json_decode($v->body, true);
        
        if (!isset($pkg['projid'])) {
            continue;
        }
        
        $pkgals[$pkg['projid']] = $pkg;        
    }

    // Get the data instance settings of all
    $dbiNow = array();
    $rs = $kpr->NodeListAndGet("/db/ui/{$user}");
    if ($rs->type == Keeper::ReplyMulti) {
    
        foreach ($rs->elems as $v) {
        
            $inst = json_decode($v->body, false);
        
            if (!isset($inst->projid) || !isset($inst->datainstid)) {
                continue;
            }

            $dbiNow[$inst->projid][$inst->id] = $inst;
        }
    }

    // Get SysInfo, Stand-alone version
    $sysInfo = $kpr->Info();
    $sysInfo = json_decode($sysInfo->body, false);
    if (!isset($sysInfo->local->id)) {
        throw new \Exception($this->T('Service Unavailable'), 500);
    }

    // Get the dataset ids to be setup
    foreach ($_REQUEST['access_dbs'] as $k => $datasetid) {

        //if (!isset($pkgals[$projid])) {
        //    throw new \Exception(sprintf($this->T("The Package `%s` is not exists"), $projid));
        //}

        /* TODO if (!isset($pkgals[$projid]['lc_plugins'])) {
            continue;
        }

        $lc_plugins = explode(",", $pkgals[$projid]['lc_plugins']);
        if (!in_array("lessdata", $lc_plugins)) {
            continue;
        }
        */

        $pkgdbins[$datasetid] = array(
            'host' => $_REQUEST['access_host'][$k],
            'port' => $_REQUEST['access_port'][$k],
            'user' => $_REQUEST['access_user'][$k],
            'pass' => $_REQUEST['access_pass'][$k],
            'dbname' => $_REQUEST['access_dbname'][$k],
        );
    }
    //throw new \Exception(json_encode($pkgdbins));
    

    // Get the dataset information of all
    $datasets = array();

    $glob = $projPath."/lcproj/lessdata/*.ds.json";

    $rs = lesscreator_fs::FsList($glob);

    foreach ($rs->data as $v) {
    
        $v = $v->path;

        $json = lesscreator_fs::FsFileGet($v);
        $json = json_decode($json->data->body, false);
    
        if (!isset($json->id)) {
            continue;
        }

        if ($projInfo['projid'] != $json->projid) {
            continue;
        }

        $json->tables = array();
        

        $globsub = $projPath."/lcproj/lessdata/". $json->id .".*.tbl.json";
        $rs2 = lesscreator_fs::FsList($globsub);
    
        foreach ($rs2->data as $v2) {

            $v2 = $v2->path;
        
            $json2 = lesscreator_fs::FsFileGet($v2);
            $json2 = json_decode($json2->data->body, false);
    
            if (!isset($json2->tableid)) {
                continue;
            }

            $json->tables[] = $json2;            
        }

        $datasets[$json->id] = $json;
    }

    $dbinsts = array();
    foreach ($datasets as $datasetid => $ds) {

        if (!isset($ds->projid) || !isset($ds->id)) {
            continue;
        }

        if (isset($dbiNow[$ds->projid]) && isset($dbiNow[$ds->projid][$ds->id])) {
            $ds->datainstid = $dbiNow[$ds->projid][$ds->id]->datainstid;
            //$ds->accesspass = $dbiNow[$ds->projid][$ds->id]->accesspass;

            $ds->access_pass = $dbiNow[$ds->projid][$ds->id]->access_pass;
            $ds->access_user = $dbiNow[$ds->projid][$ds->id]->access_user;
            $ds->access_host = $dbiNow[$ds->projid][$ds->id]->access_host;
            $ds->access_port = $dbiNow[$ds->projid][$ds->id]->access_port;
            $ds->access_dbname = $dbiNow[$ds->projid][$ds->id]->access_dbname;
        } else {
            $ds->datainstid = LessPHP_Util_String::rand(12, 2);
        }

        if (strlen($ds->access_pass) < 6) {
            $ds->access_pass = LessPHP_Util_String::rand(16, 2);
        }
        //$ds->accessuser = "db-{$user}";
        //$ds->accessaddr = "{$sysInfo->local->addr}";
        
        if (isset($pkgdbins[$ds->id]['host']) 
            && strlen($pkgdbins[$ds->id]['host']) > 0) {
            $ds->access_host = $pkgdbins[$ds->id]['host'];
        }
        if (isset($pkgdbins[$ds->id]['port']) 
            && strlen($pkgdbins[$ds->id]['port']) > 0) {
            $ds->access_port = $pkgdbins[$ds->id]['port'];
        }
        if (isset($pkgdbins[$ds->id]['user']) 
            && strlen($pkgdbins[$ds->id]['user']) > 0) {
            $ds->access_user = $pkgdbins[$ds->id]['user'];
        }
        if (isset($pkgdbins[$ds->id]['pass']) 
            && strlen($pkgdbins[$ds->id]['pass']) > 0) {
            $ds->access_pass = $pkgdbins[$ds->id]['pass'];
        }
        if (isset($pkgdbins[$ds->id]['dbname']) 
            && strlen($pkgdbins[$ds->id]['dbname']) > 0) {
            $ds->access_dbname = $pkgdbins[$ds->id]['dbname'];
        }

        //throw new \Exception(json_encode($ds));

        $ds->user = "{$user}";

        $kpr->NodeSet("/db/ui/{$user}/{$ds->datainstid}", json_encode($ds));
        $dbinsts[] = $ds->datainstid;
    }
    //print_r($datasets);
    

    $job = array(
        'user'      => "{$user}",
        'insts'     => $dbinsts,
        'jobid'     => $rsp['jobid'],
        'status'    => 0,
    );
    $kpr->NodeSet("/db/qi/{$sysInfo->local->id}/{$job['jobid']}", json_encode($job)); 

    $qstatus = array(
        'jobid'  => $rsp['jobid'],
        'status' => 0,
    );
    $kpr->NodeSet("/db/qistatus/{$job['jobid']}", json_encode($qstatus));

    $rsp['status'] = 200;

} catch (\Exception $e) {
    $rsp['message'] = $e->getMessage();
}

die(json_encode($rsp));
