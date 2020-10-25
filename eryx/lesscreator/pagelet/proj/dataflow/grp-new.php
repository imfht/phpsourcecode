<?php

use LessPHP\Encoding\Json;


$msg = 'Internal Server Error';

$proj = preg_replace("/\/+/", "/", rtrim($this->req->proj, '/'));
$projPath = lesscreator_proj::path($proj);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $this->req->name;

    if (!strlen($name)) {
        die('Invalid Params');
    }

    $obj = $projPath ."/dataflow";
    $obj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $obj);
        
    $id = LessPHP_Util_String::rand(8, 2);

    $obj .= "/{$id}.grp.json";
    $obj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $obj);

    $set = array(
        'id'    => $id,
        'name'  => $name,
        'created' => time(),
        'updated' => time(),
    );
    lesscreator_fs::FsFilePut($obj, Json::prettyPrint($set));

    die("OK");
}

die($msg);
