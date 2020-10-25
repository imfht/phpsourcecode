<?php

use LessPHP\Encoding\Json;


$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);
if (!isset($projInfo['projid'])) {
    die($this->T('Bad Request'));
}

if (!isset($this->req->id) || strlen($this->req->id) == 0) {
    die($this->T('Bad Request'));
}
$datasetid = $this->req->id;
$fsd = $projPath."/lcproj/lessdata/{$datasetid}.ds.json";
$rs = lesscreator_fs::FsFileGet($fsd);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}
$dataInfo = json_decode($rs->data->body, true);

if ($projInfo['projid'] != $dataInfo['projid']) {
    die($this->T('Access denied'));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($this->req->name)) {
        $dataInfo['name'] = $this->req->name;
    }
    
    $dataInfo['updated'] = time();
    $rs = lesscreator_fs::FsFilePut($fsd, Json::prettyPrint($dataInfo));
    if ($rs->status != 200) {
        die($rs->message);
    }

    die("OK");
}
?>
<div class="bmejc8 alert hide"></div>
<form id="b2qcyo" action="/lesscreator/plugins/lessdata/dataset-set">
<input type="hidden" name="id" value="<?php echo $dataInfo['id']?>" />
<table width="100%">
    <tr>
        <td width="120px"><strong><?php echo $this->T('DataSet ID')?></strong></td>
        <td><?php echo $dataInfo['id']?></td>
    </tr>
    <tr>
        <td><strong><?php echo $this->T('Name')?></strong></td>
        <td><input type="text" name="name" value="<?php echo $dataInfo['name']?>" /></td>
    </tr>
</table>  
</form>

<script type="text/javascript">

lessModalButtonAdd("o4wn8e", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

lessModalButtonAdd("qe7kft", "<?php echo $this->T('Confirm and Save')?>", "_data_dataset_set()", "btn-inverse");

$("#b2qcyo").submit(function(event) {
    event.preventDefault();
    _data_dataset_set();
});
function _data_dataset_set()
{
    //var time = new Date().format("yyyy-MM-dd HH:mm:ss");

    $.ajax({ 
        type    : "POST",
        url     : $("#b2qcyo").attr('action') +"?_="+ Math.random(),
        data    : $("#b2qcyo").serialize() +"&proj="+ projCurrent,
        success : function(rsp) {
            if (rsp == "OK") {
                lessAlert(".bmejc8", "alert-success", "OK");
                if (typeof _proj_data_tabopen == 'function') {
                    _proj_data_tabopen('/lesscreator/plugins/lessdata/list?proj='+projCurrent, 1);
                }
            } else {
                lessAlert(".bmejc8", "alert-error", rsp);
            }
        }
    });
}

</script>
