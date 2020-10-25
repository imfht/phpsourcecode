<?php

if (strlen($this->req->instanceid) < 1) {
    die($this->T('Bad Request'));
}
$projInstId = $this->req->instanceid;

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();
//lesscreator_service::debugPrint($ins);

$datasets = array();

$glob = $projPath."/data/*.ds.json";

foreach (glob($glob) as $v) {

    $dataInfo = lesscreator_fs:FsFileGet($v);
    $dataInfo = json_decode($dataInfo->data->body, true);
    if (!isset($dataInfo['id'])) {
        continue;
    }

    $datasets[$dataInfo['id']] = $dataInfo;
    $datasets[$dataInfo['id']]['_tables'] = array();

    $globsub = $projPath."/data/{$dataInfo['id']}.*.tbl.json";
    
    foreach (glob($globsub) as $v2) {
        
        $tableInfo = lesscreator_fs::FsFileGet($v2);
        $tableInfo = json_decode($tableInfo->data->body, true);
    
        if (!isset($tableInfo['tableid'])) {
            continue;
        }
    
        // Compare with instances settings, if deployed
        $dataInst = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$projInstId}/data/{$tableInfo['tableid']}");
        $dataInst = json_decode($dataInst->body, true);
        if (!isset($dataInst['DataInst'])) {
            $tableInfo['_ins_id'] = "0";
        } else {
            $tableInfo['_ins_id'] = $dataInst['DataInst'];
        }

        $datasets[$dataInfo['id']]['_tables'][] = $tableInfo;
    }
}
//lesscreator_service::debugPrint($datasets);

echo "<table width=\"100%\" class='table table-hover table-condenseds'>";
echo "<thead><tr>
        <th width='15px'></th>
        <th></th>
        <th>Instance Status</th>
        <th colspan='2' align='center'>Deployment Options</th>
    </tr></thead>";
foreach ($datasets as $k => $v) {

    echo "<tr>
        <td>
            <img src='/fam3/icons/database.png' class='h5c_icon' /> 
        </td>
        <td><strong>{$v['name']}</strong></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>";

    foreach ($v['_tables'] as $v2) {
        
        $data = "{$v['id']}_{$v2['tableid']}";

        if ($v2['_ins_id'] == "0") {
            
            $status = "<img src='/fam3/icons/exclamation.png' class='h5c_icon' /> Not deployed";
            
            $href = "<label class='checkbox'>
                <input type='checkbox' name='dbnew{$data}' value='new' checked /> 
                Create New Table Instance
                </label>";
            
            $hrefslc = "<a href='#{$data}' class='btn btn-mini p30as5'><i class='icon-share'></i> Use existing Instance</a>";
        } else {

            $status = "<img src='/fam3/icons/accept.png' class='h5c_icon' /> {$v2['_ins_id']}";
            
            $href = "<label class='checkbox'>
                <input type='checkbox' name='dbnew{$data}' value='new' /> 
                Update Instance
                </label>";
            
            $hrefslc = "<a href='#{$data}' class='btn btn-mini p30as5'><i class='icon-share'></i> Use existing Instance</a>";
        }
        $hrefslc = ""; // TODO

        echo "<tr>
        <td></td>
        <td>
            <img src='/fam3/icons/database_table.png' class='h5c_icon' /> {$v2['tablename']}
        </td>
        <td class='h5c-font-mono' id='st{$data}'>{$status}</td>
        <td>
            {$href}
        </td>
        <td>
            {$hrefslc}
        </td>
        </tr>";
    }
}
echo "</table>";
?>


<script type="text/javascript">
lessModalButtonAdd("kdqtr8", "Confirm and Next", "_launch_data_next()", "btn-inverse");
lessModalButtonAdd("qqgn7r", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

function _launch_data_next()
{
    var uri = "proj="+ sessionStorage.ProjPath;
    uri += "&instanceid="+ sessionStorage.LaunchInstanceId;
    
    $("input[name^=dbnew]:checked").each(function() {

        if ($(this).val() == "new") {
       
            var data = $(this).attr("name").substr(5);
            var req = uri +"&data="+ data;
            //console.log(req);
            $.ajax({
                url     : "/lesscreator/instance/launch-data-new?_="+ Math.random(),
                type    : "POST",
                data    : req,
                timeout : 30000,
                async   : false,
                success : function(rsp) {
                    //console.log(rsp);
                    var obj = JSON.parse(rsp);
                    if (obj.Status != "OK") {
                        hdev_header_alert('error', obj.Status);
                        return;
                    }
                    $("#st"+ data).html("<img src='/fam3/icons/accept.png' class='h5c_icon' /> "+ obj.DataInst);
                    $("input[name=dbnew"+ data +"]").parent().remove();
                    //$(".irvj4f").val(instanceid);
                },
                error: function(xhr, textStatus, error) {
                    hdev_header_alert('error', xhr.responseText);
                    return
                }
            });
        }
    });

    uri += "&flowgrpid="+ sessionStorage.LaunchFlowGrpId;
    uri += "&flowactorid="+ sessionStorage.LaunchFlowActorId;
    uri += "&_="+ Math.random();

    var url = "/lesscreator/instance/launch-flow?"+ uri;
    
    lessModalNext(url , "Deployment Dataflow Actor", null);
}
</script>
