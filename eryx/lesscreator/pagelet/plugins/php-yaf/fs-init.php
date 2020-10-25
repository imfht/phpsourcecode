<?php

use Zend\Version\Version;
use LessPHP\Encoding\Json;
use LessPHP\Util\Directory;

$ret = array(
    'status'  => 200,
    'message' => null,
);

$req = file_get_contents("php://input");
$req = json_decode($req, false);

try {
    
    if (!isset($req->data->projdir) || strlen($req->data->projdir) < 1) {
        throw new \Exception(sprintf($this->T('`%s` can not be null'), 'ProjDir'), 400);
    }
    
    $projPath = lesscreator_proj::path($req->data->projdir);
    
    $info = lesscreator_proj::info($req->data->projdir);
    if (!isset($info['projid'])) {
        throw new \Exception(sprintf($this->T('`%s` Not Found'), $this->T('Project')), 404);
    }


    $lcpj = "{$projPath}/lcproject.json";
    $lcpj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $lcpj);
    

    $tplpath = LESSCREATOR_DIR ."/pagelet/plugins/php-yaf/fs-init-tpl";
    $fs = Directory::listFiles($tplpath);
    foreach ($fs as $file) {
        $file = trim($file, "/");
        $str = file_get_contents($tplpath ."/". $file);        

        if ($file == "conf/application.ini" || $file == "application/views/index/index.phtml") {
            $str = str_replace("{{.projid}}", $info['projid'], $str);
        }

        lesscreator_fs::FsFilePut("{$projPath}/{$file}", $str);
    }

    if (!isset($info['runtimes']['nginx']) 
        || $info['runtimes']['nginx']['ngx_conf_mode'] != "custom"
        || $info['runtimes']['nginx']['status'] != 1) {

        $info['runtimes']['nginx']['status'] = 1;
        $info['runtimes']['nginx']['ngx_conf_mode'] = "custom";
        
        $str = Json::prettyPrint($info);
        $rs = lesscreator_fs::FsFilePut($lcpj, $str);
        if ($rs->status != 200) {
            throw new \Exception($msg = "Error, ". $rs->message, 400);
        }
    }
    
    throw new \Exception($this->T('Successfully Processed'), 200);
    
} catch (\Exception $e) {

    $ret['status'] = intval($e->getCode());
    $ret['message'] = $e->getMessage();
}

die(json_encode($ret));
