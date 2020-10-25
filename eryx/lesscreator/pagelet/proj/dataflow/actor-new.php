<?php

use LessPHP\Encoding\Json;


$msg    = 'Internal Server Error';

$projPath = lesscreator_proj::path($this->req->proj);


$grps = array();
$glob = $projPath."/dataflow/*.grp.json";
foreach (glob($glob) as $v) {
    
    $rs = lesscreator_fs::FsFileGet($v);
    if ($rs->status != 200) {
        continue;
    }

    $json = json_decode($rs->data->body, true);
    if (!isset($json['id'])) {
        continue;
    }

    $grps[$json['id']] = $json;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $this->req->name;
    if (!strlen($name)) {
        die('Invalid Params');
    }

    $grpid = $this->req->grpid;
    if (!isset($grps[$grpid])) {
        die('Invalid Params');
    }

    // actor config
    $obj = $projPath ."/dataflow";
    $obj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $obj);
    
    $id = LessPHP_Util_String::rand(10, 2);

    $obj .= "/{$grpid}/{$id}.actor.json";
    $obj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $obj);
    $set = array(
        'id'    => $id,
        'name'  => $name,
        'created' => time(),
        'updated' => time(),
    );
    lesscreator_fs::FsFilePut($obj, Json::prettyPrint($set));

    // actor
    $obj = $projPath ."/dataflow/{$grpid}/{$id}.actor";
    $rs = lesscreator_fs::FsFilePut($obj, "#!/bin/sh\n\n");
    if ($rs->status != 200) {
        die($rs->message);
    }

    die("OK");
}
?>

<table class="h5c_dialog_header" width="100%">
    <tr>
        <td width="20px"></td>
        <td style="font-size:14px;font-weight:bold;">New Actor</td>
    </tr>
</table>

<form id="_proj_dataflow_actornew_form" action="/lesscreator/proj/dataflow/actor-new" style="padding:5px;">

  <table width="100%" cellpadding="3">
    <tr>
      <td width="160"><strong>Group<font color="red">*</font></strong></td>
      <td>
        <select name='grpid'>
        <?php
        foreach ($grps as $k => $v) {
            echo "<option value='{$k}'>{$v['name']}</option>";
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><strong>Name your Actor <font color="red">*</font></strong></td>
      <td><input type="text" name="title" value="<?php echo $set['title']?>" /></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit" class="btn btn-primary" value="保存" /></td>
    </tr>
  </table>
  
</form>
