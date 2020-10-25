<?php

use LessPHP\Encoding\Json;


$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);
if (!isset($projInfo['projid'])) {
    die($this->T('Bad Request'));
}

if ($this->app->method == 'POST') {

    $datasetid = $this->req->datasetid;
    $fsd = $projPath."/data/{$datasetid}.ds.json";
    $rs = lesscreator_fs::FsFileGet($fsd);
    if ($rs->status == 200) {
        die("Bad Request, Data already exists");
    }

    $set = array(
        'id'      => $datasetid,
        'name'    => $this->req->datasetname,
        'type'    => '1',
        'projid'  => $projInfo['projid'],
        'created' => time(),
        'updated' => time(),
    );
    lesscreator_fs::FsFilePut($fsd, Json::prettyPrint($set));
    
    die("OK");
}

$datasetid = LessPHP_Util_String::rand(8, 2);
?>

<div id="h5c_dialog_alert"></div>

<form id="c47vz9" action="/lesscreator/data/create-ts">
<table width="100%">
  <tr>
    <td width="180px"><strong>DataSet ID</strong></td>
    <td>
      <input type="text" name="datasetid" value="<?php echo $datasetid?>" readonly="readonly" />
    </td>
  </tr>
  <tr>
    <td><strong>Name your DataSet</strong></td>
    <td>
      <input type="text" id="datasetname" name="datasetname" value="" />
    </td>
  </tr>
</table>
</form>

<script>
lessModalButtonAdd("qdpvv3", "<?php echo $this->T('Close')?>", "lessModalClose()", "");
lessModalButtonAdd("t42qf1", "Confirm and Commit", "_data_new_ts()", "btn-inverse");
lessModalButtonAdd("yc82zu", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

function _data_new_ts()
{
    event.preventDefault();
        
    $.ajax({ 
        type    : "POST",
        url     : $("#c47vz9").attr('action') +"?_="+ Math.random(),
        data    : $("#c47vz9").serialize() +"&proj="+projCurrent,
        success : function(rsp) {
            if (rsp == "OK") {

                rsp = "<h4>Success</h4>";
                rsp += '<p>Your DataSet has been created successfully</p>';
                rsp += '<p><a class="btn" href="#" onclick="_data_create_open()">Manage</a></p>';

                lessAlert("#h5c_dialog_alert", "alert-success", rsp);
                
            } else {
                lessAlert("#h5c_dialog_alert", "alert-error", rsp);
            }
        }
    });
}

function _data_create_open()
{
    var opt = {
        "img": "database",
        "title": $("#datasetname").val(),
        "close": 1
    }
    var id = $("input [name=datasetid]").val();

    if (typeof _proj_data_tabopen == 'function') {
        _proj_data_tabopen('/lesscreator/proj/data/list?proj='+ projCurrent, 1);
    }
    lessModalClose();
}
</script>
