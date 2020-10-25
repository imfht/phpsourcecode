<?php
    
use LessPHP\Encoding\Json;

$ret = array(
    'status'  => 200,
    'message' => null,
);

try {
    
    $req = file_get_contents("php://input");
    $req = json_decode($req, false);
    if (!isset($req->proj) || strlen($req->proj) < 1) {
        throw new \Exception($this->T('Page Not Found'), 404);
    }
    
    $projPath = lesscreator_proj::path($req->proj);
    
    $info = lesscreator_proj::info($req->proj);
    if (!isset($info['projid'])) {
        throw new \Exception($this->T('Page Not Found'), 404);
    }

    $lcpj = "{$projPath}/lcproject.json";
    $lcpj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $lcpj);
    
    if (!isset($info['runtimes']['nginx'])) {
        $info['runtimes']['nginx'] = array(
            'status' => 0,
            'ngx_conf_mode' => 'std'
        );
    }

    if ($info['runtimes']['nginx']['status'] != $req->status) {
        $info['runtimes']['nginx']['status'] = intval($req->status);
    }

    if (isset($req->ngx_conf_mode)
        && $info['runtimes']['nginx']['ngx_conf_mode'] !== $req->ngx_conf_mode) {
        $info['runtimes']['nginx']['ngx_conf_mode'] = $req->ngx_conf_mode;
    }

    if ($info['runtimes']['nginx']['ngx_conf_mode'] == "custom"
        && strlen($req->ngx_conf) > 10) {

        lesscreator_fs::FsFilePut("{$projPath}/misc/nginx/virtual.custom.conf",
            $req->ngx_conf);
    }

    if (true) {
        $str = Json::prettyPrint($info);
        $rs = lesscreator_fs::FsFilePut($lcpj, $str);
        if ($rs->status != 200) {
            throw new \Exception($msg = "Error, ". $rs->message, 400);
        } else {
            throw new \Exception($this->T('Successfully Processed'), 200);
        }
    }

    throw new \Exception($this->T('Successfully Processed'), 200);
    
} catch (\Exception $e) {

    $ret['status'] = intval($e->getCode());
    $ret['message'] = $e->getMessage();
}

die(json_encode($ret));
