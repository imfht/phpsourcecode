<?php

if (strlen($this->req->instanceid) < 1) {
    die($this->T('Bad Request'));
}
$projInstId = $this->req->instanceid;

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();

$grps = array();
$glob = $projPath."/dataflow/*.grp.json";
foreach (glob($glob) as $v) {
    $grpInfo = lesscreator_fs::FsFileGet($v);
    $grpInfo = json_decode($grpInfo->data->body, true);
    if (!isset($grpInfo['id'])) {
        continue;
    }
    $grps[$grpInfo['id']] = array(
        'info' => $grpInfo,
        'actor' => array()
    );

    $glob2 = $projPath."/dataflow/{$grpInfo['id']}/*.actor.json";
    foreach (glob($glob2) as $v2) {
        
        $actorInfo = lesscreator_fs::FsFileGet($v2);
        $actorInfo = json_decode($actorInfo->data->body, true);

        if (!isset($actorInfo['id'])) {
            continue;
        }

        // Compare with instances settings, if deployed
        $actorInst = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$projInstId}/flow/{$actorInfo['id']}");
        $actorInst = json_decode($actorInst->body, true);

        $actorInfo['_ins_seted'] = false;
        $actorInfo['_ins_setlock'] = false;

        switch ($actorInfo['para_mode']) {
        
        case lesscreator_service::ParaModeServer:
            if (strlen($actorInst['ParaHost']) > 7) {
                $actorInfo['_ins_seted'] = true;
            }
            break;

        case lesscreator_service::ParaModeDataSingle:
        case lesscreator_service::ParaModeDataServer: 
        case lesscreator_service::ParaModeDataShard:

            $para_datas = explode("_", $actorInfo['para_data']);
            $dataInst = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$projInstId}/data/{$para_datas[1]}");
            $dataInst = json_decode($dataInst, true);
            //lesscreator_service::debugPrint($dataInst);
            
            $actorInst['ActorId']     = $actorInfo['id'];
            $actorInst['ParaData']    = $dataInst['InstId'];
            $actorInst['ProjInst']    = $projInstId;
            $actorInst['User']        = 'guest';
            
            $instInfo = array(
                'ProjId'    => $projInfo['projid'],
                'GrpId'     => $grpInfo['id'],
                'ActorId'   => $actorInfo['id'],
                'ProjInst'  => $projInstId,
                'Func'      => '10',
                'ParaData'  => $dataInst['InstId'],
                'Info'      => $actorInfo,
            );
            $fss = $projPath."/dataflow/{$grpInfo['id']}/{$actorInfo['id']}.actor";
            $fss = lesscreator_fs::FsFileGet($fss);
            
            $kpr->NodeSet("/app/u/guest/{$projInfo['projid']}/{$projInstId}/flow/{$actorInfo['id']}", json_encode($actorInst));
            
            $kpr->NodeSet("/h5flow/script/{$projInstId}/{$actorInfo['id']}", $fss->data->body);
            $kpr->NodeSet("/h5flow/ctrlq/{$projInstId}.{$actorInfo['id']}", json_encode($instInfo));

            $actorInfo['_ins_setlock'] = true;
            $actorInfo['_ins_seted'] = true;
            //echo "CtrlQ";
            break;
        }
        
        $grps[$grpInfo['id']]['actor'][$actorInfo['id']] = $actorInfo;
    }
}
//lesscreator_service::debugPrint($grps);
echo "<table width=\"100%\" class='table table-hover table-condenseds'>";
echo "<thead><tr>
        <th width='20px'></th>
        <th></th>
        <th>Status</th>
        <th>Configuration</th>
    </tr></thead>";
foreach ($grps as $k => $v) {
    echo "<tr>
        <td>
            <img src='/fam3/icons/package.png' class='h5c_icon' /> 
        </td>
        <td>
            <strong>{$v['info']['name']}</strong>
        </td>
        <td></td>
        <td></td>
    </tr>";

    foreach ($v['actor'] as $k2 => $v2) {

        if (!isset($v2['name'])) {
            $v2['name'] = $k2;
        }

        if ($v2['_ins_seted']) {
            $status = "<img src='/fam3/icons/accept.png' class='h5c_icon' /> Configured";
        } else {
            $status = "<img src='/fam3/icons/exclamation.png' class='h5c_icon' /> Not configured";
        }
        
        $sethref = '';
        if (!$v2['_ins_setlock']) {
            $sethref = "<a href='#{$k}/{$v2['id']}' class='bbwv0a btn btn-mini'><i class='icon-cog'></i> Configure</a>";
        }

        echo "<tr>
        <td></td>
        <td>
            <img src='/fam3/icons/brick.png' class='h5c_icon' />
            {$v2['name']}
        </td>
        <td id='status{$v2['id']}'>{$status}</td>
        <td>
            {$sethref}
        </td>
        </tr>";
    }
}
echo "</table>";

?>


<script type="text/javascript">
lessModalButtonAdd("lho070", "Confirm and Next", "_launch_flow_next()", "btn-inverse");
lessModalButtonAdd("dpx9cl", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

$(".bbwv0a").click(function() {

    var href = $(this).attr("href").substr(1).split("/");
    //console.log(href);
    var uri = "proj="+ sessionStorage.ProjPath;
    uri += "&instanceid="+ sessionStorage.LaunchInstanceId;
    uri += "&flowgrpid="+ href[0];
    uri += "&flowactorid="+ href[1];

    var url = "/lesscreator/instance/launch-flow-set?"+ uri;
    lessModalNext(url , "Actor Setting", null);
});

function _launch_flow_next()
{
    sessionStorage.InsActive = sessionStorage.LaunchInstanceId;
    var url = "/lesscreator/instance/launch-done?";
    lessModalNext(url, "Well Done", null);
}

</script>
