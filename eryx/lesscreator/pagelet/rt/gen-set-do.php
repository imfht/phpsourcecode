<?php

use LessPHP\Encoding\Json;

$ret = array(
    'status'  => 200,
    'message' => null,
);

try {
    
    if (!isset($this->req->proj) || strlen($this->req->proj) < 1) {
        throw new \Exception(sprintf($this->T('`%s` Not Found'), $this->T('Page')), 404);
    }

    $projPath = lesscreator_proj::path($this->req->proj);

    $info = lesscreator_proj::info($this->req->proj);
    if (!isset($info['projid'])) {
        throw new \Exception(sprintf($this->T('`%s` Not Found'), $this->T('Page')), 404);
    }

    $rts = lesscreator_env::RuntimesList();
    if (!isset($rts[$this->req->runtime])) {
        throw new \Exception(sprintf($this->T('Runtime `%s` Not Found'), $this->req->runtime), 404);
    }

    $lcpj = "{$projPath}/lcproject.json";
    $lcpj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $lcpj);

    if (!isset($info['runtimes'][$this->req->runtime])) {
        $info['runtimes'][$this->req->runtime] = array(
            'status' => 0,
        );
    }

            
    if ($info['runtimes'][$this->req->runtime]['status'] != $this->req->status) {

        $info['runtimes'][$this->req->runtime]['status'] = intval($this->req->status);

        $str = Json::prettyPrint($info);
        $rs = lesscreator_fs::FsFilePut($lcpj, $str);
                
        if ($rs->status != 200) {
            throw new \Exception($rs->message, $rs->status);
        }
    }    

    throw new \Exception($this->T('Successfully Processed'), 200);

} catch (\Exception $e) {
    
    $ret['status'] = intval($e->getCode());
    $ret['message'] = $e->getMessage();
}

die(json_encode($ret));
