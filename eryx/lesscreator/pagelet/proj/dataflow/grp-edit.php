<?php

use LessPHP\Encoding\Json;


$proj = preg_replace("/\/+/", "/", rtrim($this->req->proj, '/'));

$projPath = lesscreator_proj::path($this->req->proj);

$grpid = $this->req->grpid;

$fs = $projPath."/dataflow/{$grpid}.grp.json";

$json = lesscreator_fs::FsFileGet($fs);
if ($json->status != 200) {
    die('Bad Request');
}
$json = json_decode($json->data->body, true);
if (!isset($json['id'])) {
    die('Bad Request');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $this->req->name;
    if (!strlen($name)) {
        die('Bad Request');
    }
    
    $json['name'] = $name;
    $json['updated'] = time();

    $rs = lesscreator_fs::FsFilePut($fs, Json::prettyPrint($json));
    if ($rs->status != 200) {
        die($rs->message);
    }
    
    die("OK");
}
?>

<form id="pmvc8e" action="/lesscreator/proj/dataflow/grp-edit" method="post">
    <input type="hidden" name="proj" value="<?=$proj?>" />
    <input type="hidden" name="grpid" value="<?=$grpid?>" />
    <div>
        <h5>Name</h5>
        <input type="text" name="name" class="inputfocus" value="<?php echo $json['name']?>" />
    </div>
    <div class="clearhr"></div>
    <div><input type="submit" value="<?php echo $this->T('Save')?>" class="input_button" /></div>
</form>

<script type="text/javascript">
$("#pmvc8e").submit(function(event) {

    event.preventDefault();
    
    $.ajax({
        type: "POST",
        url: $(this).attr('action'),
        data: $(this).serialize(),
        timeout: 3000,
        success: function(rsp) {
            
            if (rsp == "OK") {
                hdev_header_alert('success', rsp);
                _proj_dataflow_tabopen('<?=$proj?>', '', 1);
                lessModalClose();
            } else {
                alert(rsp);
            }
        },
        error: function(xhr, textStatus, error) {
            alert('Error:'+ textStatus +' '+ xhr.responseText);
        }
    });
});
</script>