<?php

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();


if ($this->req->func == 'new') {

    if (!strlen($this->req->domainname)) {
        die(json_encode(array('Status' => 'Bad Request')));
    }

    $this->req->domainname = strtolower($this->req->domainname);
    $rs = $kpr->NodeGet("/app/u/guest/appweb");
    $rs = json_decode($rs->body, true);
    if (!isset($rs['domains'])) {
        $rs['domains'] = array();
    }

    $set = array(
        'domain' => $this->req->domainname,
    );
    
    foreach ($rs['domains'] as $k => $v) {
        if ($v['domain'] == $this->req->domainname) {
            $set['Status'] = "OK";
            die(json_encode($set));
        }
    }

    $rs['domains'][] = $set;

    $kpr->NodeSet("/app/u/guest/global/appweb", json_encode($rs));
    
    $set['Status'] = "OK";
    die(json_encode($set));
}


if ($this->req->func == 'save') {

    if (!strlen($this->req->domainname)) {
        die(json_encode(array('Status' => 'Bad Request')));
    }
    if (!strlen($this->req->instanceid)) {
        die(json_encode(array('Status' => 'Bad Request')));
    }

    $this->req->domainname = strtolower($this->req->domainname);
    
    $projInstSet = $kpr->NodeGet("/app/u/guest/{$projInfo['projid']}/{$this->req->instanceid}/info");
    $projInstSet = json_decode($projInstSet->body, true);
    if (!isset($projInstSet['ProjId'])) {
        die(json_encode(array('Status' => 'Bad Request')));
    }

    $qweb = array(
        'webdomain' => $this->req->domainname,
        'projid' => $projInfo['projid'],
        'instid' => $this->req->instanceid,
        'user'   => 'guest',
    );
    $kpr->NodeSet("/app/qw/{$this->req->instanceid}", json_encode($qweb));

    if (isset($projInstSet['webdomain']) 
        && $projInstSet['webdomain'] == $this->req->domainname) {
        die(json_encode(array('Status' => 'OK')));
    }

    $projInstSet['webdomain'] = $this->req->domainname;
    
    $kpr->NodeSet("/app/u/guest/{$projInfo['projid']}/{$this->req->instanceid}/info",
        json_encode($projInstSet));    
    
    die(json_encode(array('Status' => 'OK')));
}


$rs = $kpr->NodeGet("/app/u/guest/global/appweb");
$rs = json_decode($rs->body, true);

if (isset($rs['domains'])) {
    
    $raw = "";    
    foreach ($rs['domains'] as $v2) {

        $raw .= '
<tr>
    <td width="30px">
        <input type="radio" name="domain" value="'.$v2['domain'].'" /> 
    </td>
    <td class="insn'.$v2['domain'].'">'.$v2['domain'].'</td>
</tr>';
    }

    if (strlen($raw)) {
        echo "<h4>Select a Exist Domain</h4>";
        echo "<table>{$raw}</table>";
        echo '<div class="h5c-hrline"></div>';
    }
}
?>

<h4>Setting a New Domain</h4>
<form id="x7kzwf" action="/lesscreator/instance/launch-web?func=new">
<table>
<tr>
    <td>
        <input type="text" name="domainname" class="ag9vlq" value="" /> 
    </td>
</tr>
</table>
</form>


<script type="text/javascript">
lessModalButtonAdd("ogsb1m", "Confirm and Next", "_launch_web_next()", "btn-inverse");
lessModalButtonAdd("wdmuxh", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

var domain  = null;

$('input:radio[name="domain"]').click(function() {
    domain = $(this).val();
    sessionStorage.domainActive = domain
});

$('.ag9vlq').click(function() {
    $("input[name='domain']").prop("checked", false);
    domain = null;
});

function _launch_web_next()
{    
    var domainnew = $(".ag9vlq").val();

    if (domain == null && domainnew != "") {

        $.ajax({
            url     : $("#x7kzwf").attr('action'),
            type    : "POST",
            data    : $("#x7kzwf").serialize() + "&proj="+ projCurrent,
            timeout : 30000,
            async   : false,
            success : function(rsp) {
                //console.log(rsp);
                var obj = JSON.parse(rsp);
                if (obj.Status != "OK") {
                    hdev_header_alert('error', obj.Status);
                    return;
                }
                domain = obj.domain;
                sessionStorage.domainActive = domain;
            },
            error: function(xhr, textStatus, error) {
                hdev_header_alert('error', xhr.responseText);
                return;
            }
        });

    }

    if (domain == null) {
        alert('Select an Domain');
        return;
    }

    var uri = "proj="+ sessionStorage.ProjPath;
    uri += "&instanceid="+ sessionStorage.LaunchInstanceId;

    $.ajax({
        url     : "/lesscreator/instance/launch-web?func=save",
        type    : "POST",
        data    : uri +"&domainname="+ domain,
        timeout : 30000,
        async   : false,
        success : function(rsp) {
            var obj = JSON.parse(rsp);
            if (obj.Status != "OK") {
                hdev_header_alert('error', obj.Status);
                return;
            }
        },
        error: function(xhr, textStatus, error) {
            hdev_header_alert('error', xhr.responseText);
            return;
        }
    });

    
    uri += "&flowgrpid="+ sessionStorage.LaunchFlowGrpId;
    uri += "&flowactorid="+ sessionStorage.LaunchFlowActorId;
    uri += "&_="+ Math.random();

    var url = "/lesscreator/instance/launch-data?"+ uri;
    
    lessModalNext(url , "Database Deployment Setup", null);
}

if (domain == null && sessionStorage.domainActive) {
    $("input[value='"+sessionStorage.domainActive+"']").prop("checked", true);
    domain = sessionStorage.domainActive;
}
</script>
