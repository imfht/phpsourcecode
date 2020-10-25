<?php

use LessPHP\Encoding\Json;


$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);
if (!isset($projInfo['projid'])) {
    die($this->T('Bad Request'));
}

if ($this->app->method == 'POST') {

    $datasetid = $this->req->datasetid;
    $fsd = $projPath."/lcproj/lessdata/{$datasetid}.ds.json";

    $rs = lesscreator_fs::FsFileGet($fsd);
    if ($rs->status == 200) {
        die(sprintf($this->T('`%s` exists'), $this->T('DataSet ID')));
    }

    $set = array(
        'id'      => $datasetid,
        'name'    => $this->req->datasetname,
        'type'    => $this->req->datasettype,
        'projid'  => $projInfo['projid'],
        'created' => time(),
        'updated' => time(),
    );
    lesscreator_fs::FsFilePut($fsd, Json::prettyPrint($set));
    
    die("OK");
}

$datasetid = LessPHP_Util_String::rand(8, 2);
$datasettype = intval($this->req->datasettype);

?>

<div id="h5c_dialog_alert"></div>

<form id="o1pj61" action="/lesscreator/plugins/lessdata/create-rds">
<input type="hidden" name="datasettype" value="<?php echo $datasettype?>" />
<table width="100%">
  <tr>
    <td width="180px"><strong><?php echo $this->T('DataSet ID')?></strong></td>
    <td>
      <input type="text" name="datasetid" value="<?php echo $datasetid?>" readonly="readonly" />
    </td>
  </tr>
  <tr>
    <td><strong><?php echo $this->T('Name your DataSet')?></strong></td>
    <td>
      <input type="text" id="datasetname" name="datasetname" value="" />
    </td>
  </tr>
</table>
</form>

<script>
lessModalButtonAdd("hril76", "<?php echo $this->T('Close')?>", "lessModalClose()", "");
lessModalButtonAdd("edmfpf", "<?php echo $this->T('Confirm and Save')?>", "_data_new_rds()", "btn-inverse");
lessModalButtonAdd("d2jns6", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

function _data_new_rds()
{
    event.preventDefault();
        
    $.ajax({ 
        type    : "POST",
        url     : $("#o1pj61").attr('action') +"?_="+ Math.random(),
        data    : $("#o1pj61").serialize() +"&proj="+projCurrent,
        success : function(rsp) {
            if (rsp == "OK") {

                rsp = "<h4><?php echo $this->T('Successfully Done')?></h4>";
                rsp += '<p><?php echo $this->T('Your DataSet has been created successfully')?></p>';
                rsp += '<p><a class="btn" href="#" onclick="_data_create_open()"><?php echo $this->T('Manage')?></a></p>';

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
        _proj_data_tabopen('/lesscreator/plugins/lessdata/list?proj='+ projCurrent, 1);
    }
    lessModalClose();
}
</script>
