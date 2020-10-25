<?php

if (strlen($this->req->instanceid) < 1) {
    die($this->T('Bad Request'));
}
if (strlen($this->req->flowgrpid) < 1) {
    die($this->T('Bad Request'));
}
if (strlen($this->req->flowactorid) < 1) {
    die($this->T('Bad Request'));
}

$insid = $this->req->instanceid;
$grpid = $this->req->flowgrpid;
$actorid = $this->req->flowactorid;


$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();

$actorInfo = $projPath."/dataflow/{$grpid}/{$actorid}.actor.json";
$actorInfo = lesscreator_fs::FsFileGet($actorInfo);
$actorInfo = json_decode($actorInfo->data->body, true);

$actorIns = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$insid}/flow/{$actorid}");
$actorIns = json_decode($actorIns->body, true);

$pms = lesscreator_service::listParaMode();
?>
<table width="100%">
<tr>
    <td width="120px" valign="top">Parallel Mode</td>
    <td>
<?php
$hosts = array();
if ($actorInfo['para_mode'] == lesscreator_service::ParaModeServer) {
    $hostBinded = array();
    if (isset($actorIns['ParaHost'])) {
        $hostBinded = explode(",", $actorIns['ParaHost']);
    }
    echo $pms[lesscreator_service::ParaModeServer];
    //echo "<p class='alert'>Double-click to open the Data Instance</p>";
    echo "<div class='h5c_row_fluid'>";
    $rs = $kpr->NodeList("/kpr/ls");
    //lesscreator_service::debugPrint($rs);
    $rs = json_decode($rs->body, true);
    //lesscreator_service::debugPrint($rs);
    foreach ($rs as $v) {
        $rs2 = $kpr->NodeGet("/kpr/ls/{$v['P']}");
        $rs2 = json_decode($rs2->body, true);
        $hosts[$v['P']] = $rs2;  
        $checked = '';
        if (in_array($rs2['Id'], $hostBinded)) {
            $checked = 'checked';
        }
        echo "<a style='width:220px;' class='span href h5c-font-mono' href='#{$v['P']}'>
                <label class='checkbox'>
                  <input type='checkbox' class='bxdmt5' value='{$rs2['Id']}' {$checked}/>
                  {$rs2['Id']}/{$rs2['Ip']} 
                </label>
            </a>";
    }
    echo "</div>";
}
//lesscreator_service::debugPrint($hosts);
?>
    </td>
</tr>
</table>

<script type="text/javascript">
lessModalButtonAdd("ofzzbg", "Save and Back", "_launch_flow_set_back()", "btn-inverse");
lessModalButtonAdd("lf5krc", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

function _launch_flow_set_back()
{
    var actorid = '<?php echo "$actorid"?>';
    var uri = "proj="+ sessionStorage.ProjPath;
    uri += "&instanceid="+ sessionStorage.LaunchInstanceId;
    uri += "&flowgrpid="+ sessionStorage.LaunchFlowGrpId;
    uri += "&flowactorid="+ actorid;

    var hosts = "";
    $(".bxdmt5:checked").each(function() {
        if (hosts != "") {
            hosts += ",";
        }
        hosts += $(this).val();
    });
    uri += "&hosts="+ hosts;
    
    var datains = $("input[name=datains]:checked").val();
    uri += "&datainsid="+ datains;

    $.ajax({
        url     : "/lesscreator/instance/launch-flow-set-put?_="+ Math.random(),
        type    : "POST",
        data    : uri,
        timeout : 30000,
        async   : false,
        success : function(rsp) {
            console.log(rsp);
            var obj = JSON.parse(rsp);
            if (obj.Status != "OK") {
                hdev_header_alert('error', obj.Status);                
                return;
            }
            $("#status"+ actorid).html("<img src='/fam3/icons/accept.png' class='h5c_icon' /> OK");
            lessModalPrev()
            // $("input[name=dbnew"+ dataid +"]").parent().remove();
                    //$(".irvj4f").val(instanceid);
        },
        error: function(xhr, textStatus, error) {
            hdev_header_alert('error', xhr.responseText);
            return
        }
    });

    console.log(hosts);
}
</script>
