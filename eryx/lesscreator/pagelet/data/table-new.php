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
$fsd = $projPath."/data/{$datasetid}.ds.json";
$rs = lesscreator_fs::FsFileGet($fsd);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}
$dataInfo = json_decode($dataInfo->data->body, true);

if ($projInfo['projid'] != $dataInfo['projid']) {
    die("Permission denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($this->req->tableid)) {
        die($this->T('Bad Request'));
    }
    $tableid = $this->req->tableid;

    $fstbl = $projPath."/data/{$datasetid}.{$tableid}.tbl.json";
    $rs = lesscreator_fs::FsFileGet($fstbl);
    if ($rs->status == 200) {
        die("The current table already exists");
    }

    $tableInfo = array(
        'projid'        => $projInfo['projid'],
        'datasetid'     => $datasetid,
        'tableid'       => $tableid,
        'tablename'     => $this->req->tablename,
        'schema'        => array(array('name' => 'id',
            'type'  => 'varchar',
            'len'   => '40',
            'idx'   => '2'
        )),
        'created'       => time(),
        'updated'       => time(),
    );

    $rs = lesscreator_fs::FsFilePut($fstbl, Json::prettyPrint($tableInfo));
    if ($rs->status != 200) {
        die($rs->message);
    }
    die("OK");
}

$tableid = LessPHP_Util_String::rand(8, 2);
?>
<div class="j82fpe alert hide"></div>
<form id="x23w5t" action="/lesscreator/data/table-new">
<input type="hidden" name="id" value="<?php echo $dataInfo['id']?>" />
<table width="100%">
    <tr>
        <td width="160px"><strong>DataSet ID</strong></td>
        <td><?php echo $dataInfo['id']?></td>
    </tr>
    <tr>
        <td><strong>Table ID</strong></td>
        <td><input type="text" name="tableid" value="<?php echo $tableid?>" /></td>
    </tr>
    <tr>
        <td><strong>Name Your Table</strong></td>
        <td><input type="text" name="tablename" value="" /></td>
    </tr>
</table>  
</form>

<script type="text/javascript">

lessModalButtonAdd("d8id3r", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

lessModalButtonAdd("h6xj1q", "Confirm and Save", "_data_tableid_set()", "btn-inverse");

$("#x23w5t").submit(function(event) {
    event.preventDefault();
    _data_tableid_set();
});
function _data_tableid_set()
{
    var time = new Date().format("yyyy-MM-dd HH:mm:ss");
    $.ajax({ 
        type    : "POST",
        url     : $("#x23w5t").attr('action') +"?_="+ Math.random(),
        data    : $("#x23w5t").serialize() +"&proj="+ projCurrent,
        success : function(rsp) {
            if (rsp == "OK") {
                lessAlert(".j82fpe", "alert-success", "OK "+ time);
                if (typeof _proj_data_tabopen == 'function') {
                    _proj_data_tabopen('/lesscreator/proj/data/list?proj='+projCurrent, 1);
                }
            } else {
                lessAlert(".j82fpe", "alert-error", rsp +" "+ time);
            }
        }
    });
}

</script>
